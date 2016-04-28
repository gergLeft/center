<?php
  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget';
  $page_tag = 'budget';
  include 'functions.php';

  //page postback actions
?>
<?php include 'components/global/header.php'; ?>

<?php 
  /*TODO: Replace with ajax controls; doing it this way to get the database 
          and business logic established and allow output */
?>

<?php /* At a Glance Dashboard */ ?>
<div class="row">
  <div class="columns">
    <h3><?= $_GET["c"] ?></h3>
  </div>
</div>

<?php //Table containing ledger items ?>

<?php include "components/global/footer.php"; ?>
  