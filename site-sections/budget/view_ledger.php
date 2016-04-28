<?php

  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : View Ledger';
  $page_tag = 'view_ledger';
  include 'functions.php';

  //page postback actions
?>
<?php include 'components/global/header.php'; ?>

<?php /*TODO: Change to infinite scroll or historical paging w/ "jump to month" option */ ?>

<?php //Table containing ledger items for the current month ?>
<?php 
  //get today, start of month, and end of month dates
  $baseDate = isset($_GET["d"]) ? $_GET["d"] : date('m/d/y');
  $month_start = date('m/01/y', strtotime($baseDate));
  $month_end = date('m/t/y', strtotime($baseDate));
  $eodBalance = ledger_item::get_starting_balance($month_start);
  
  //get all ledger items
  //TODO: add filter for only this month
  $ledger_items = ledger_item::get_all_ledger_items();
?>

<?php //iterate through days of month
  $alt = false;
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
?>

<div class="row ledger_day <?php if ($alt) { $alt = false; echo "alt"; } else { $alt = true; } ?>">
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo date('l', $d); ?>
  </div>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo date('m/d/y', $d); ?>
  </div>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php 
      $eodBalance += $day_summary; 
      echo sprintf("$%01.2f", $eodBalance); 
    ?>
  </div>
  <div class="columns small-6 medium-2 large-1 ledger_day_heading">
    <?php echo sprintf("$%01.2f", $day_summary); ?>
  </div>
  <div class="columns small-12 large-8">
    <div class="row">
      <?php foreach ($day_transactions as $t) : ?>
      <div class="columns small-12 medium-4 large-3 <?php echo ($t->type === ledger_item::EXPENSE) ? "expense-record" : "income-record"; ?>">
        <?php echo sprintf("$%01.2f", $t->value); ?> - 
        <?php echo $t->company; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endfor; ?>

<a href="/site-sections/budget/view_ledger.php?d=<?php echo date('m/01/y', strtotime($month_start . ' -1 day')); ?> ">&ltdot;Previous Month</a>
<a href="/site-sections/budget/view_ledger.php?d=<?php echo date('m/01/y', strtotime($month_end . ' +1 day')); ?> ">Next Month&gtdot;</a>

<?php include "components/global/footer.php"; ?>
  