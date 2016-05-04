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

<?php
  //TODO: slug category for test/display purposes
  $category = budget_category::getAllCategoryStateForMonth(date("m/d/y"), false);
?>
<?php foreach ($category as $cat) : ?>
  <div class="row">
    <div class="columns small-3">
      <a href="./category_detail.php?c=<?php echo $cat->id ?>"><?php echo $cat->name ?></a>
    </div>
    <div class="columns small-8 progress">
      <?php 
        if ($cat->value > 0) {
          $catPercent = $cat->amount_used / $cat->value * 100;
        } else {
          $catPercent = 100;
        }
        
        if ($catPercent < 75 || $cat->value === 0) {
          $budget_usage_cat = "budget_okay"; 
        } else if ($catPercent < 90) {
          $budget_usage_cat = "budget_caution"; 
        } else if ($catPercent < 100) {
          $budget_usage_cat = "budget_danger"; 
        } else {
          $budget_usage_cat = "budget_over";
        }
      ?>
      <div class="used <?php echo $budget_usage_cat; ?>" style="width: <?php echo $catPercent; ?>%"><?php echo $cat->amount_used; ?></div>
    </div>
    <div class="columns small-1">
      <?php if ($cat->value > 0) : ?>
      <?php echo sprintf("%01.0f%%", $cat->amount_used / $cat->value * 100) ?>
      <?php else: ?>&nbsp;<?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<?php include "components/global/footer.php"; ?>
  