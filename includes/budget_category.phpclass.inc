<?php
/**
 * @name membership_info
 */
class budget_category {
  const EXPENSE = 'expense';
  const INCOME = 'income';
  
  public $id;
  public $type = self::EXPENSE;
  public $name = "";
  public $value = 0.00;
  public $allow_carryOver = false;
  public $tax_deductible = false;
  public $amount_used = 0.00;

  public function create_category() {
    //check if category exists already
    if ($this->getCategoryByName($this->name)) {
      //duplicate
      throw new Exception(Message::DUPLICATE_CATEGORY);
    } else {
      //unique
      $this->create_budget_category();
    }
  }
  
  public function getCategoryByID($id) {
    return $this->getMemberBy_criteria("id", "=", $id);
  }
  
  public function getCategoryByName($name) {
    return $this->getCategoryBy_criteria("name", "=", $name);
  }

  private function getCategoryBy_criteria($field, $compare, $value) {
    $db = new db_sql();
    $query = 
      "SELECT 
        *
      FROM 
        budget_categories
      WHERE ";

    $query .= sprintf("%s %s '%s' ", $field, $compare, $value);
    $db->query($query);
    if ($db->next_record()) {
      $this->id = $db->f("id");
      $this->name = $db->f("name");
      $this->type = $db->f("type");
      $this->value = abs($db->f("value"));
      $this->allow_carryOver = $db->f("allow_carryOver");
      $this->tax_deductible = $db->f("tax_deductible");
      return true;
    }
    
    return false;
  }
  public static function get_budget_categories() {
    $db = new db_sql();
    $query = "SELECT * FROM budget_categories";
    $query .= " ORDER BY name ASC";
    $db->query($query);
    
    $cats = array();
    while ($db->next_record()) {
      $cat = new budget_category();
      $cat->id = $db->f("id");
      $cat->name = $db->f("name");
      $cat->type = $db->f("type");
      $cat->value = abs($db->f("value"));
      $cat->allow_carryOver = $db->f("allow_carryOver");
      $cat->tax_deductible = $db->f("tax_deductible");
      $cats[$cat->id] = $cat;
    }
    
    $uncat = new budget_category();
    $uncat->id = 0;
    $uncat->name = "Uncategorized";
    $uncat->type = "Expense";
    $uncat->value = 0;
    $uncat->allow_carryOver = false;
    $uncat->tax_deductible = false;
    $cats[0] = $uncat;
    
    return $cats;
  }
  
  public static function getAllCategoryState() {
    //first get all the cateogries (expense & income)
    $all_cats = self::get_budget_categories();
    
    //next get all the ledger items (expense & income)
    $all_ledgers = ledger_item::get_all_ledger_items();
    
    //load each ledger into associated category
    foreach ($all_ledgers as $ledger) {
      $all_cats[$ledger->category]->amount_used += abs($ledger->value);
    }
    
    return $all_cats;
  }
  
  private function create_budget_category() {
    //if not, create authorized_users record
    $db = new db_sql();
    $query = "INSERT INTO budget_categories SET ";
    $query .= sprintf("name = '%s', ", $this->name);
    $query .= sprintf("type = '%s', ", $this->type);
    $query .= sprintf("value = '%s', ", abs($this->value));
    $query .= sprintf("allow_carryOver = %s, ", $this->allow_carryOver ? 1 : 0 );
    $query .= sprintf("tax_deductible = %s ", $this->tax_deductible ? 1 : 0 );
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      $this->id = $db->last_id();
      return true;
    }
  }
}
?>