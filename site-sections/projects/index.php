<?php
  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Projects';
  $page_tag = 'projects';
  $form_display=array();
  include 'functions.php';

  //page postback actions
  
  //TODO: add drag and drop re-ordering
  //TODO: add link to edit/comment/view
?>
<?php include 'components/global/header.php'; ?>

<?php $all_projects = project::get_projects(); ?>
<?php 
  $alt = false;
  foreach ($all_projects as $project) : 
?>
<div class="row project-record <?php if ($alt) echo "alt"; $alt = !$alt; ?>">
  <div class="columns small-3">
    <?php echo $project->name; ?>
  </div>
  <div class="columns small-1">
    <?php echo date("m/d/y", strtotime($project->dueDate)); ?>
  </div>
  <div class="columns small-1">
    <?php echo ucwords($project->priority); ?>
  </div>
  <div class="columns small-4">
    <?php echo ucwords($project->description); ?>
  </div>
  <div class="columns small-1">
    <?php echo $project->owner; ?>
  </div>
  <div class="columns small-1">
    <select id='ddlPriority' name='ddlPriority' class="small">
      <option value="<?php echo project::PRIORITY_LOW ?>" ><?php echo ucwords(project::PRIORITY_LOW) ?></option>
      <option value="<?php echo project::PRIORITY_MEDIUM ?>" ><?php echo ucwords(project::PRIORITY_MEDIUM) ?></option>
      <option value="<?php echo project::PRIORITY_HIGH ?>" ><?php echo ucwords(project::PRIORITY_HIGH) ?></option>
      <option value="<?php echo project::PRIORITY_CRITICAL ?>" ><?php echo ucwords(project::PRIORITY_CRITICAL) ?></option>
    </select>
  </div>
  <div class="columns small-1">
    <a class="secondary tiny button">Close</a>
  </div>
</div>
<?php endforeach; ?>

<?php include "components/global/footer.php"; ?>
  