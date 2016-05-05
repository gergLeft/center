<?php
  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Projects : Edit Project';
  $page_tag = 'projects-edit_project';
  $form_display = array(
    'txtProjectName' => '',
    'txtProjectDueDate' => '',
    'txtDescription' => '',
    'ddlPriority' => '',
    'txtProjectOwner' => '',
  );
  $projName_err = $projDate_err = $projDesc_err = $projOwner_err = "";
  include 'functions.php';
  $this_project = new project($_GET["pid"]);

  //page postback actions
  if ( "POST" === $_SERVER['REQUEST_METHOD']) {
    if (isset($_POST["btnDelete"])) {
      project::delete_project($_GET["pid"]);
    } else if (empty($_POST["txtProjectName"]) ||
        empty($_POST["txtProjectDueDate"])
    ) {
      if (empty($_POST["txtProjectName"])) {
        $projName_err = "error";
      }
      if (empty($_POST["txtProjectDueDate"])) {
        $projDate_err = "error";
      }
      if (empty($_POST["txtProjectOwner"])) {
        $projOwner_err = "error";
      }
      
    } else {
      $this_project->name = $_POST["txtProjectName"];
      $this_project->dueDate = $_POST["txtProjectDueDate"];      
      $this_project->description = $_POST["txtDescription"];
      $this_project->priority = $_POST["ddlPriority"];
      $this_project->owner = $_POST["txtProjectOwner"];
      
      try {
        if ($this_project->update_project()) {
          $serverFeedback = Message::translate_code_to_message(Message::PROJECT_CREATED);
          $serverFeedbackClass = "success";
          clearFormDisplay($form_display);
        } 
      } catch (Exception $ex) {
        dump($ex);
        $serverFeedbackClass = "error";
      }
    }
  } else {
    $form_display = array(
      'txtProjectName' => $this_project->name,
      'txtProjectDueDate' => $this_project->dueDate,
      'txtDescription' => $this_project->description,
      'ddlPriority' => $this_project->priority,
      'txtProjectOwner' => $this_project->owner,
    );
  }
?>
<?php include 'components/global/header.php'; ?>

<form class="custom" method="POST">
  <?php if (isset($serverFeedback)) : ?>
  <div class="row formRow <?php echo $serverFeedbackClass ?>">
    <div class="small-12 medium-6 small-centered columns">
      <span id="lblServerFeedback"><?php echo $serverFeedback; ?></span>
    </div>
  </div>
  <?php endif; ?>
  
  <div class="row formRow required <?php echo $username_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Project Name: <?php echo REQUIRED_INDICATOR ?></label>
      <input id="txtProjectName" name="txtProjectName" type="text" MaxLength="50" value="<?php echo $form_display["txtProjectName"] ?>"/>
      <?php if (!empty($projName_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $username_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Project Owner: <?php echo REQUIRED_INDICATOR ?></label>
      <input id="txtProjectOwner" name="txtProjectOwner" type="text" MaxLength="50" value="<?php echo $form_display["txtProjectOwner"] ?>"/>
      <?php if (!empty($projOwner_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $monthBudget_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Due Date: <?php echo REQUIRED_INDICATOR ?></label>
      <input id="txtProjectDueDate" name="txtProjectDueDate" type="text" MaxLength="50" value="<?php echo $form_display["txtProjectDueDate"] ?>" class="foundation-date"/>
      <?php if (!empty($projDate_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $monthBudget_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Description: </label>
      <textarea id="txtDescription" name="txtDescription"><?php echo $form_display["txtDescription"] ?></textarea>
      <?php if (!empty($projDesc_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="row formRow required">
    <div class="small-12 medium-6 small-centered columns">
      <label>Priority: <?php echo REQUIRED_INDICATOR ?>
        <select id='ddlPriority' name='ddlPriority'>
          <option value="<?php echo project::PRIORITY_LOW ?>" <?php if (project::PRIORITY_LOW == $form_display["ddlPriority"]) : ?>selected<?php endif; ?>><?php echo ucwords(project::PRIORITY_LOW) ?></option>
          <option value="<?php echo project::PRIORITY_MEDIUM ?>" <?php if (project::PRIORITY_MEDIUM == $form_display["ddlPriority"]) : ?>selected<?php endif; ?>><?php echo ucwords(project::PRIORITY_MEDIUM) ?></option>
          <option value="<?php echo project::PRIORITY_HIGH ?>" <?php if (project::PRIORITY_HIGH == $form_display["ddlPriority"]) : ?>selected<?php endif; ?>><?php echo ucwords(project::PRIORITY_HIGH) ?></option>
          <option value="<?php echo project::PRIORITY_CRITICAL ?>" <?php if (project::PRIORITY_CRITICAL == $form_display["ddlPriority"]) : ?>selected<?php endif; ?>><?php echo ucwords(project::PRIORITY_CRITICAL) ?></option>
        </select>
      </label>
    </div>
  </div>
  
  <div class="row formRow">
    <div class="small-12 medium-6 small-centered columns">
      <div class="row">
        <div class="small-6 columns">
          <ul class="button-group">
            <li><a class="small button secondary" href="./">Cancel</a></li>
            <li><input type="submit" class="small button" type="submit"></li>
          </ul>     
        </div>
      
        <div class="small-6 text-right columns">
          <input type="submit" class="small button" type="submit" id="btnDelete" name="btnDelete" value="Delete">
        </div>
      </div>
    </div>
  </div>
</form>

<?php include "components/global/footer.php"; ?>
  