<?php
/**
 * @name membership_info
 */
class membership_info {
		
  public $user_id;
  public $username;
  public $password;
  public $last_login;
  public $create_date;
  
  public $active_homepage_modules = array();
  public $inactive_homepage_modules = array();

  public function createUser() {
    //check if user exists already
    if ($this->getMemberByUsername($this->username)) {
      //duplicate
      throw new Exception(Message::DUPLICATE_USERNAME);
    } else {
      //unique
      $this->user_id = $this->create_authorized_user_record();
    }
  }
  
  public function setUserInfo($userId) {
    $db = new db_sql();
    $query = 
      "SELECT 
        *
      FROM 
        authorized_users
      WHERE ";
    $query .= sprintf("user_id = '%s' ", $userId);
    $db->query($query);
    if ($db->next_record()) {
      $this->user_id = $db->f("user_id");
      if (isset($this->user_id)) {
        $this->username = $db->f("username");
      }
    }
  }

  public function login() {
      $db = new db_sql();
      $query = 
        "SELECT 
          user_id 
        FROM 
          authorized_users
        WHERE ";
      $query .= sprintf("username = '%s' AND ", $this->username);
      $query .= sprintf("password = '%s'", md5($this->password));
      $db->query($query);
	  dump($db->Error);
      if ($db->next_record()) {
        $_SESSION["user_id"] = $db->f("user_id");
        
        $query = "UPDATE authorized_users SET ";
        $query .= sprintf("last_login = '%s', ", date("Y-m-d H:i:s"));
        $query .= sprintf("WHERE");
        $query .= sprintf("user_id = %s", $_SESSION["user_id"]);
        $db->query($query);
      }

      return false;
  }
  
  public function getMemberByID($id) {
    return $this->getMemberBy_criteria("user_id", "=", $id);
  }
  
  public function getMemberByUsername($username) {
    return $this->getMemberBy_criteria("username", "=", $username);
  }

  public static function logout() {
    session_destroy();
    header("Location: login.php");
  }

  private function getMemberBy_criteria($field, $compare, $value) {
    $db = new db_sql();
    $query = 
      "SELECT 
        *
      FROM 
        authorized_users
      WHERE ";

    $query .= sprintf("%s %s '%s' ", $field, $compare, $value);
    $db->query($query);
    if ($db->next_record()) {
      $this->user_id = $db->f("user_id");
      $this->username = $db->f("username");
      $this->last_login = $db->f("last_login");
      $this->create_date = $db->f("create_date");
      return true;
    }
    
    return false;
  }
  
  private function create_authorized_user_record() {
    //if not, create authorized_users record
    $db = new db_sql();
    $query = "INSERT INTO authorized_users SET ";
    $query .= sprintf("username = '%s', ", $this->username);
    $query .= sprintf("password = '%s', ", md5($this->password));
    $query .= sprintf("last_login = '1900-01-01 00:00:00', ");
    $query .= sprintf("create_date = '%s' ", date("Y-m-d H:i:s"));
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      return $db->last_id();
    }
  }
}
?>