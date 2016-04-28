<?php

  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : Import Ledger Items';
  $page_tag = 'import_ledger_items';
  include 'functions.php';

  //page postback actions
  //TODO: Process duplicate resolution form
  $runImport = true;
  if ("POST" === $_SERVER["REQUEST_METHOD"]) {
    foreach ($_POST as $key=>$post_field_value) {
      if (0 === strpos($key, "dup_trans_key-") && "create" === $post_field_value ) {
        $keyInt = substr($key, strlen("dup_trans_key-"));
        $new_ledger_item = new ledger_item();
        $new_ledger_item->company = $_POST["dup_trans_company-".$keyInt];
        $new_ledger_item->value = $_POST["dup_trans_value-".$keyInt];
        $new_ledger_item->date = $_POST["dup_trans_date-".$keyInt];
        $new_ledger_item->time = $_POST["dup_trans_time-".$keyInt];
        $new_ledger_item->type = $_POST["dup_trans_type-".$keyInt];
        $new_ledger_item->create_ledger_item();
      }
    }
    $runImport = false;
  }
?>
<?php include 'components/global/header.php'; ?>

<?php 
  if ($runImport && isset($_REQUEST["f"])) :
  $import = file_get_contents("./import_ledger_uploads/" . $_REQUEST["f"]); 
  $import_csv_rows = str_getcsv($import, "\n");
  $success = $fail = 0;
  $duplicates = $error_log = array();
  $firstColHeaders = true;
  for ($i=0; $i<=sizeof($import_csv_rows); $i++) {
    if (empty($import_csv_rows[$i])) {
      //skip this row
      continue;
    }
    if ($firstColHeaders) {
      //do nothing, skip this record
      $firstColHeaders = false;
      continue;
    } 
    
    $rowData = str_getcsv($import_csv_rows[$i]);
    $new_ledger_item = new ledger_item();
    
    $new_ledger_item->company = $rowData[2];
    $new_ledger_item->value = $rowData[3];
    $new_ledger_item->date = $rowData[1];
    $new_ledger_item->time = "00:00:00";
    if ($new_ledger_item->value < 0) {
      $new_ledger_item->type = ledger_item::EXPENSE;
    } else {
      $new_ledger_item->type = ledger_item::INCOME;
    }
    
    if ( "beginning balance" !== strtolower($new_ledger_item->company)) {
      try {
        $thisTrans = $new_ledger_item;
        $res = $new_ledger_item->create_ledger_item(true);
        if (true === $res) {
          $success++;
        } else {
          $duplicates[] = array("new"=>$thisTrans, "possible_duplicates"=>$res);
        }
      } catch(Exception $ex) {
        $fail++;
        $err_log[] = $ex;
      }
    }
  }
?>

Records: <?php echo sizeof($import_csv_rows); ?><br />
Successes: <?php echo $success; ?><br />
Failures: <?php echo $fail; ?><br />
Duplicates: <?php echo sizeof($duplicates); ?>
<?php if ($fail > 0) : ?>
Log: <?php dump($error_log);?>
<?php endif; ?>

<?php if (sizeof($duplicates) > 0 ) : ?>
<hr />
<h5>Resolve Duplications</h5>
<form class="custom" method="POST">
<?php $d=0;foreach($duplicates as $dup) : $d++; ?>
<div class="row">
  <div class="columns large-1">
    <select name="dup_trans_key-<?= $d ?>" id="dup_trans_key-<?= $d ?>">
      <option value=""> - </option>
      <option value="create">Approve</option>
      <option value="skip">Deny</option>
    </select>
  </div>
  <?php $new = true; foreach ($dup as $trans) : ?>
  
  <div class="columns large-5 <?php echo $new ? "import-trans" : "duplicate-trans"; ?>" >
    
    <?php if ($new) :?>
    <input type="hidden" name="dup_trans_type-<?= $d ?>" id="dup_trans_type-<?= $d ?>" value="<?= $trans->type ?>" />
    <input type="hidden" name="dup_trans_category-<?= $d ?>" id="dup_trans_category-<?= $d ?>" value="<?= $trans->category ?>" />
    <input type="hidden" name="dup_trans_company-<?= $d ?>" id="dup_trans_company-<?= $d ?>" value="<?= $trans->company ?>" />
    <input type="hidden" name="dup_trans_value-<?= $d ?>" id="dup_trans_value-<?= $d ?>" value="<?= $trans->value ?>" />
    <input type="hidden" name="dup_trans_date-<?= $d ?>" id="dup_trans_date-<?= $d ?>" value="<?= $trans->date ?>" />
    <input type="hidden" name="dup_trans_time-<?= $d ?>" id="dup_trans_time-<?= $d ?>" value="<?= $trans->time ?>" />
    <?php 
        $new = false;
      endif; 
    ?>
    
    <div class="row">
      <div class="columns small-2">
        Type: <?php echo ucwords($trans->type); ?>
      </div>
      <div class="columns small-2">
        Category: <?php echo $trans->category; ?>
      </div>
      <div class="columns small-3">
        Company: <?php echo $trans->company; ?>
      </div>
      <div class="columns small-2">
        Value: <?php echo sprintf("$%01.2f", $trans->value); ?>
      </div>
      <div class="columns small-2">
        Date: <?php echo $trans->date . " " . $trans->time; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>
  <input type="submit" class="button" type="submit">
</form>
<?php endif; ?>
<?php elseif (!isset($_REQUEST["f"])) : ?>
No File Specified.
<?php endif; ?>

<?php include "components/global/footer.php"; ?>
  