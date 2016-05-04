<?php
/**
 * @name project
 */
class project {
  const PRIORITY_LOW = 'low';
  const PRIORITY_MEDIUM = 'medium';
  const PRIORITY_HIGH = 'high';
  const PRIORITY_CRITICAL = 'critical';
  
  public $id;
  public $name = "";
  public $dueDate;
  public $description = "";
  public $priority = self::PRIORITY_MEDIUM;
  public $owner = "";
    
  function __construct() {
    $this->dueDate = date("Y-m-d 00:00:00");
  }
  
  public static function get_projects() {
    $db = new db_sql();
    $query = "SELECT * FROM projects";
    $query .= " ORDER BY name ASC";
    $db->query($query);
    $projects = array();
    while ($db->next_record()) {
      $project = new project();
      $project->id = $db->f("id");
      $project->name = $db->f("name");
      $project->dueDate = $db->f("dueDate");
      $project->description = $db->f("description");
      $project->priority = $db->f("priority");
      $project->owner = $db->f("owner");
      $projects[$project->id] = $project;
    }
    
    return $projects;
  }
  
  public function create_project() {
    //if not, create authorized_users record
    $db = new db_sql();
    $query = "INSERT INTO projects SET ";
    $query .= sprintf("name = '%s', ", $this->name);
    $query .= sprintf("dueDate = '%s', ", date("Y-m-d 00:00:00", strtotime($this->dueDate)));
    $query .= sprintf("description = '%s', ", $this->description);
    $query .= sprintf("priority = '%s', ", $this->priority);
    $query .= sprintf("owner = '%s' ", $this->owner);
    $db->query($query);

    if ($db->Error) {
      throw new Exception($db->Error);
    } else {
      $this->id = $db->last_id();
      return true;
    }
  }
  
  private function update_project() {
    //if not, create authorized_users record
    $db = new db_sql();
    $query = "UPDATE projects SET ";
    $query .= sprintf("name = '%s', ", $this->name);
    $query .= sprintf("dueDate = '%s', ", $this->dueDate);
    $query .= sprintf("description = '%s', ", $this->description);
    $query .= sprintf("priority = '%s', ", $this->priority);
    $query .= sprintf("owner = '%s' ", $this->owner);
    
    $query .= sprintf(" WHERE id = %s ", $this->id);
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