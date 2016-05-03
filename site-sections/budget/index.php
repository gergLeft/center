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
  $category = budget_category::getAllCategoryState();
?>
<div class="header row">
  <div class="columns small-3">
    Category
  </div>
  <div class="columns small-3">
    Ratio
  </div>
  <div class="columns small-2">
    Remaining
  </div>
  <div class="columns small-2">
    Used
  </div>
  <div class="columns small-2">
    Percentage
  </div>
</div>
<?php foreach ($category as $cat) : ?>
  <div class="row">
    <div class="columns small-3">
      <a href="./category_detail.php?c=<?php echo $cat->id ?>"><?php echo $cat->name ?></a>
    </div>
    <div class="columns small-3">
      <?php if ($cat->value > 0) : ?>
      <?php echo $cat->amount_used ?> / <?php echo $cat->value ?> 
      <?php else: ?>
      &nbsp;
      <?php endif; ?>
    </div>
    <div class="columns small-2">
      [<?php echo $cat->value - $cat->amount_used ?>]
    </div>
    <div class="columns small-2">
      [<?php echo $cat->amount_used ?>]
    </div>
    <div class="columns small-2">
      <?php if ($cat->value > 0) : ?>
      (<?php echo $cat->amount_used / $cat->value * 100 ?>%)
      <?php else: ?>
      &nbsp;
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<?php include "components/global/footer.php"; ?>
  