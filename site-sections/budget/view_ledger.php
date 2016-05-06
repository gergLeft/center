<?php

  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : View Ledger';
  $page_tag = 'budget-view_ledger';
  include 'functions.php';

  //page postback actions
?>
<?php include 'components/global/header.php'; ?>

<?php /*TODO: Change to infinite scroll or historical paging w/ "jump to month" option */ ?>

<?php //Table containing ledger items for the current month ?>
<?php 
  //get all ledger items
  //TODO: add filter for only this month
  if (isset($_GET["c"])) {
    $ledger_items = ledger_item::get_all_ledger_items_by_cat($_GET["c"]);
    $catFilter = "&c=" . $_GET["c"];
  } else {
    $ledger_items = ledger_item::get_all_ledger_items();
    $catFilter = "";
  }  
?>
<?php 
  //get today, start of month, and end of month dates
  $baseDate = isset($_GET["d"]) ? $_GET["d"] : date('m/d/y');
  $month_start = date('m/01/y', strtotime($baseDate));
  $month_end = date('m/t/y', strtotime($baseDate));
  if ("" === $catFilter) {
    $eodBalance = ledger_item::get_starting_balance($month_start);
    $all_cats = budget_category::get_budget_categories();
  }
?>
<div class="row">
  <h2>
  <?php /*title = Date Range (Month Year) - Category */ ?>
  <?php echo date("M Y", strtotime($month_start)); ?>
  <?php 
    if ("" !== $catFilter) : 
      $activeCat = new budget_category($_GET["c"]);
  ?>
   - <?php echo $activeCat->name; ?>
  <?php endif; ?>
  </h2>
</div>
<div class="row">
  <div class="columns small-6">
    <a href="/site-sections/budget/view_ledger.php?d=<?php echo date('m/01/y', strtotime($month_start . ' -1 day')); echo $catFilter; ?> ">&ltdot;Previous Month</a>
  </div>
  <div class="columns small-6 text-right">
    <a href="/site-sections/budget/view_ledger.php?d=<?php echo date('m/01/y', strtotime($month_end . ' +1 day')); echo $catFilter; ?> ">Next Month&gtdot;</a>
  </div>
</div>

<?php //iterate through days of month
  $alt = false;
  $month_summary = 0;
  for ($d = strtotime($month_start); $d <= strtotime($month_end); $d = strtotime(date('m/d/y', $d) . ' +1 day')) : 
    $date = date('m/d/y', $d);
    //reset day transactions & find all transactions for that specific day
    $day_transactions = array();
    $day_summary = 0;
    foreach($ledger_items as $ledger_item) {
      if ($ledger_item->date === $date){ 
        $day_transactions[] = $ledger_item;
        if ($ledger_item->type == ledger_item::EXPENSE) {
          $day_summary -= abs($ledger_item->value);
        } else {
          $day_summary += abs($ledger_item->value);
        }
      }
    }
    $month_summary += $day_summary;
?>

<div class="row ledger_day <?php if ($alt) { $alt = false; echo "alt"; } else { $alt = true; } ?>">
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo date('l', $d); ?>
  </div>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo date('m/d/y', $d); ?>
  </div>
  <?php if ("" === $catFilter) : ?>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php 
      $eodBalance += $day_summary; 
      echo sprintf("$%01.2f", $eodBalance); 
    ?>
  </div>
  <?php endif; ?>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo sprintf("$%01.2f", $day_summary); ?>
  </div>
  <div class="columns small-12 large-8">
    <div class="row">
      <?php foreach ($day_transactions as $t) : ?>
      <div class="columns small-12 medium-4 large-3 <?php echo $t->type . "-record"; ?> <?php echo $t->status . "-record"; ?>" title="<?php echo $t->note; ?>">
        <a href="/site-sections/budget/edit_ledger_item.php?tid=<?php echo $t->id; ?>" class="ledger_link">
          <?php echo sprintf("$%01.2f", $t->value); ?> - 
        <?php echo $t->company; ?>
        </a><br />
        <?php if (isset($all_cats)) : ?>
        <a class="ledger_category ledger_link" href="/site-sections/budget/view_ledger.php?d=<?php echo $month_start . "&c=" . $t->category; ?> " ><?php echo $all_cats[$t->category]->name; ?></a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endfor; ?>

<?php if ("" === $catFilter) : ?>
<div class="row ledger_summary">
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo date('M Y', $d); ?>
  </div>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">&nbsp;</div>
  <div class="columns small-6 large-1 ledger_day_heading end">
    <?php 
      echo sprintf("$%01.2f", $month_summary); 
    ?>
  </div>
</div>
<?php endif; ?>

<div class="row">
  <div class="columns small-6">
    <a href="/site-sections/budget/view_ledger.php?d=<?php echo date('m/01/y', strtotime($month_start . ' -1 day')); echo $catFilter; ?> ">&ltdot;Previous Month</a>
  </div>
  <div class="columns small-6 text-right">
    <a href="/site-sections/budget/view_ledger.php?d=<?php echo date('m/01/y', strtotime($month_end . ' +1 day')); echo $catFilter; ?> ">Next Month&gtdot;</a>
  </div>
</div>

<?php include "components/global/footer.php"; ?>
  