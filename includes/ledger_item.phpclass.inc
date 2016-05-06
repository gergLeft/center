<?php
/**
 * @name membership_info
 */
class ledger_item {
  const EXPENSE = 'expense';
  const INCOME = 'income';
  
  const STATUS_CLEARED = 'cleared';
  const STATUS_PENDING = 'pending';
  
  const SCHEDULE_DAILY = 'daily';
  const SCHEDULE_WEEKLY = 'weekly';
  const SCHEDULE_BIWEEKLY = 'bi-weekly';
  const SCHEDULE_SEMIMONTHLY = 'semi-monthly';
  const SCHEDULE_MONTHLY = 'monthly';
  const SCHEDULE_QUARTERLY = 'quarterly';
  const SCHEDULE_SEMIANNUALLY = 'semi-annually';
  const SCHEDULE_ANNUALLY = 'annually';
  
  public $id;
  public $type = self::EXPENSE;
  public $category = 0;
  public $company = "";
  public $note = "";
  public $value = 0.00;
  public $date;
  public $time = "00:00:00";
  public $status = self::STATUS_CLEARED;
  public $recurring_set_id = false;
  
  function __construct($id = null) {
    if ($id !== null) {
      self::getLedgerBy_criteria(array(array("field"=>"id", "compare"=>"=", "value"=>$id)));
    }
  }
  
  public function create_recurring_ledger_item($pattern, $stopType, $stopVal) {
    
    //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
    //stopType = date or count
    //stopVal = date to stop on, or count of transactions
    
    $transactions = array();
    
    //index = 0, date selected; 
    $base = new ledger_item();
    $base->type = $this->type;
    $base->category = $this->category;
    $base->company = $this->company;
    $base->note = $this->note;
    $base->value = $this->value;
    $base->date = $this->date;
    $base->time = $this->time;
    $base->status = self::STATUS_PENDING;
    $base->recurring_set_id = $base->create_ledger_item();
    //TODO: update recurring_set_id on base transactions
    $transactions[] = $base;
    
    //find out how many transactions we need
    if ("count" === $stopType) {
      
      $nextDate = $base->date;
      
      for ($i=0; $i < $stopVal-1; $i++) { //$stopVal-1 because we created index 0 above as the 1st transaction
        $nextTrans = clone $base;
        $nextTrans->date = get_next_scheduled_day($nextDate, $pattern);
        $nextTrans->id = $nextTrans->create_ledger_item();
        $transactions[] = $nextTrans;
        
        //increment the date so it'll advance
        $nextDate = $nextTrans->date;
      }
    } else {
      //populate next date so it can be checked in the conditional
      $nextDate = get_next_scheduled_day($base->date, $pattern);
      $i=0;
      
      while (strtotime($nextDate) <= strtotime($stopVal)) {
        //if ($i++>20) return;
        $nextTrans = clone $base;
        $nextTrans->date = $nextDate; 
        $nextTrans->id = $nextTrans->create_ledger_item();
        $transactions[] = $nextTrans;
        
        //populate next date so it can be checked in the conditional again
        $nextDate = get_next_scheduled_day($nextDate, $pattern);
      }
    }
    return $transactions;
  }
  
  public function create_ledger_item($check_duplicates = false) {
    //check for possible duplicate first
    if ($check_duplicates) {
      $dups = self::has_possible_duplicate($this);
      if (false !== $dups) {
        //possible duplicate
        return $this;
      }
    }
    
    $transTime = strtotime($this->date . " " . $this->time);
    
    //no possible duplicates found or ignored
    $db = new db_sql();
    $query = "INSERT INTO ledger_items SET ";
    $query .= sprintf("type = '%s', ", $this->type);
    $query .= sprintf("status = '%s', ", $transTime > strtotime(date("Y-m-d 00:00:00")) ? self::STATUS_PENDING : self::STATUS_CLEARED);
    $query .= sprintf("value = '%s', ", sanitizeNumber($this->value));
    $query .= sprintf("category = %s, ", $this->category);
    $query .= sprintf("company = '%s', ", mysql_real_escape_string($this->company));
    $query .= sprintf("note = '%s', ", mysql_real_escape_string($this->note));
    $query .= sprintf("date = '%s' ", date('Y-m-d H:i:s', $transTime));
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      $this->id = $db->last_id();
      return true;
    }
  }
  
  public static function delete_ledger_item($id) {
    //no possible duplicates found or ignored
    $db = new db_sql();
    $query = "DELETE FROM ledger_items WHERE ";
    $query .= sprintf("id = '%s'", $id);
    
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      return true;
    }
  }
  
  public function update_ledger_item() {
    //no possible duplicates found or ignored
    $db = new db_sql();
    $query = "UPDATE ledger_items SET ";
    $query .= sprintf("status = '%s', ", $this->status);
    $query .= sprintf("type = '%s', ", $this->type);
    $query .= sprintf("value = '%s', ", preg_replace('/[^\d\.]/', '', $this->value));
    $query .= sprintf("category = %s, ", $this->category);
    $query .= sprintf("company = '%s', ", mysql_real_escape_string($this->company));
    $query .= sprintf("note = '%s', ", mysql_real_escape_string($this->note));
    $query .= sprintf("date = '%s' ", date('Y-m-d H:i:s', strtotime($this->date . " " . $this->time)));
    $query .= sprintf(" WHERE id = '%s' ", $this->id);
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      $this->id = $db->last_id();
      return true;
    }
  }
  
  public function getLedgerByID($id) {
    return $this->getLedgerBy_criteria(array( array("field"=> "id", "compare"=>"=", "value"=>$id) ) );
  }
  
  public static function get_starting_balance($date) {
    $db = new db_sql();
    $query = "
      SELECT 
        sum(value) as sum,
        type
      FROM 
        ledger_items 
      WHERE
        date < '" . date("Y-m-d 00:00:00", strtotime($date)) . "'
      GROUP BY type
      ";
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      $bal = 0;
      while($db->next_record()) {
          if ("income" === $db->f("type")) {
            $bal += $db->f("sum");
          } else {
            $bal -= $db->f("sum");
          }
      }
      return $bal;
    }
  }
  
  public static function get_all_ledger_items_by_cat($cat) {
    $args = array(
      array(
        "field"=>"category",
        "compare"=>"=",
        "value"=>$cat,
      )
    );
    return self::get_all_ledger_items($args);
  }
  
  public static function get_all_ledger_items($arr = array()) {
    $l = new ledger_item();
    $result = $l->getLedgerBy_criteria($arr);//TODO: place limit to query.
    
    if (true === $result) {
      return array($l);
    } else {
      return $result;
    }
  }
  
  public function has_possible_duplicate(&$li) {
    $args = array(
      array(
        "field" => "type", 
        "value" => $li->type
      ),
      array(
        "field" => "value", 
        "value" => sanitizeNumber($li->value)
      ),
      array(
        "field" => "date", 
          "compare" => ">=",
        "value" => date('Y-m-d H:i:s', strtotime("-2 days", strtotime($li->date . " " . $li->time)))
      ),
      array(
        "field" => "date", 
          "compare" => "<=",
        "value" => date('Y-m-d H:i:s', strtotime("+2 days", strtotime($li->date . " " . $li->time)))
      ),
    );
    return $li->getLedgerBy_criteria($args);
  }
  
  private function getLedgerBy_criteria($arr = array()) {
    
    $db = new db_sql();
    $query = 
      "SELECT 
        *
      FROM 
        ledger_items
      WHERE ";

    if (!isset($arr["relation"])) {
      $arr["relation"] = "AND";
    }
    foreach ($arr as $a) {
      if (is_array($a)) {
        if (!isset($a["compare"])) {
          $a["compare"] = "=";
        }
        $query .= sprintf(" %s %s '%s' %s ", $a["field"], $a["compare"], $a["value"], $arr["relation"]);
      }
    }
    $query .= " 1 = 1";
    $db->query($query);
    
    $return_arr = array();
    while ($db->next_record()) {
      $item = new ledger_item();
      $item->id = $db->f("id");
      $item->type = $db->f("type");
      $item->status = $db->f("status");
      $item->company = $db->f("company");
      $item->note = $db->f("note");
      $item->category = $db->f("category");
      $item->value = $db->f("value");
      $item->date = date('m/d/y', strtotime($db->f("date")));
      $item->time = date('H:i:s', strtotime($db->f("date")));
      $return_arr[] = $item;
    }
    
    if (0 === sizeof($return_arr)) {
      return false;
    } else if (1 === sizeof($return_arr)) {
      $this->id = $return_arr[0]->id;
      $this->type = $return_arr[0]->type;
      $this->status = $return_arr[0]->status;
      $this->category = $return_arr[0]->category;
      $this->company = $return_arr[0]->company;
      $this->note = $return_arr[0]->note;
      $this->date = $return_arr[0]->date;
      $this->time = $return_arr[0]->time;
      $this->value = $return_arr[0]->value;
      return true;
    } else {
      return $return_arr;
    }
  }
}
?>