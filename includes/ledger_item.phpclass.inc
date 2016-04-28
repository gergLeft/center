<?php
/**
 * @name membership_info
 */
class ledger_item {
  const EXPENSE = 'expense';
  const INCOME = 'income';
  
  public $id;
  public $type = self::EXPENSE;
  public $category = 0;
  public $company = "";
  public $value = 0.00;
  public $date;
  public $time = "00:00:00";
  
  public function create_ledger_item($check_duplicates = false) {
    //check for possible duplicate first
    if ($check_duplicates) {
      $dups = self::has_possible_duplicate($this);
      if (false !== $dups) {
        //possible duplicate
        return $this;
      }
    }
    
    //no possible duplicates found or ignored
    $db = new db_sql();
    $query = "INSERT INTO ledger_items SET ";
    $query .= sprintf("type = '%s', ", $this->type);
    $query .= sprintf("value = '%s', ", abs($this->value));
    $query .= sprintf("category = %s, ", $this->category);
    $query .= sprintf("company = '%s', ", mysql_real_escape_string($this->company));
    $query .= sprintf("date = '%s' ", date('Y-m-d H:i:s', strtotime($this->date . " " . $this->time)));

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
  
  public static function get_all_ledger_items() {
    $l = new ledger_item();
    return $l->getLedgerBy_criteria();//TODO: place limit to query.
  }
  
  public function has_possible_duplicate($li) {
    $l = new ledger_item();
    $args = array(
      array(
        "field" => "type", 
        "value" => $li->type
      ),
      array(
        "field" => "company", 
        "value" => $li->company
      ),
      array(
        "field" => "value", 
        "value" => abs($li->value)
      ),
      array(
        "field" => "date", 
        "value" => date('Y-m-d H:i:s', strtotime($li->date . " " . $li->time)) 
      ),
    );
    return $l->getLedgerBy_criteria($args);
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
      $item->company = $db->f("company");
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
      $this->category = $return_arr[0]->category;
      $this->company = $return_arr[0]->company;
      return true;
    } else {
      return $return_arr;
    }
  }
}
?>