<?php

  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : Create Ledger Item';
  $page_tag = 'budget-create_ledger_item';
  $form_display = array(
    'ddlType' => '',
    'ddlCategory' => '',
    'txtCompany' => '',
    'txtValue' => '',
    'txtDate' => '',
    'txtTime' => '',
    'cbxRecurring' => '',
    'ddlSchedule' => '',
    'stopSchedule' => '',
    'stopSchedule_count_value' => '',
    'stopSchedule_date_value' => '',
  );
  include '/functions.php';

  //page postback actions
  $catName_err = $compName_err = $value_err = $date_err = "";
  if ( "POST" === $_SERVER['REQUEST_METHOD']) {
    if (empty($_POST["txtCompany"]) ||
        empty($_POST["txtValue"])
    ) {
      if (empty($_POST["txtCompany"])) {
        $compName_err = "error";
      }
      if (empty($_POST["txtValue"])) {
        $value_err = "error";
      }
      if (empty($_POST["txtDate"])) {
        $date_err = "error";
      }
    } else {
      $new_ledger_item = new ledger_item();
      $new_ledger_item->type = $_POST["ddlType"];
      $new_ledger_item->category = $_POST["ddlCategory"];
      $new_ledger_item->company = $_POST["txtCompany"];
      $new_ledger_item->value = $_POST["txtValue"];
      $new_ledger_item->date = $_POST["txtDate"];
      //$new_ledger_item->time = $_POST["txtTime"];
      
      try {
        if ($_POST['cbxRecurring']) {
          $ledger_item_set = $new_ledger_item->create_recurring_ledger_item($_POST['ddlSchedule'], $_POST["stopSchedule"], $_POST["stopSchedule_" . $_POST["stopSchedule"] . "_value"]);
        } else {
          $new_ledger_item->create_ledger_item();
        }
        
        if ($new_ledger_item->id > 0) {
          $serverFeedback = Message::translate_code_to_message(Message::LEDGER_ITEM_CREATED);
          $serverFeedbackClass = "success";
          clearFormDisplay($form_display);
        } 
      } catch (Exception $ex) {
        $serverFeedback = Message::translate_code_to_message(Message::ERROR_COMPLETING_REQUEST);
        $serverFeedbackClass = "error";
      }
    }
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
  
  <div class="row formRow required <?php echo $catName_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Type: <?php echo REQUIRED_INDICATOR ?>
        <select id='ddlType' name='ddlType'>
          <option value="<?php echo ledger_item::EXPENSE ?>" <?php if (ledger_item::EXPENSE == $form_display["ddlType"]) : ?>selected<?php endif; ?> ><?php echo ucwords(ledger_item::EXPENSE) ?></option>
          <option value="<?php echo ledger_item::INCOME ?>" <?php if (ledger_item::INCOME == $form_display["ddlType"]) : ?>selected<?php endif; ?> ><?php echo ucwords(ledger_item::INCOME) ?></option>
        </select>
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $catName_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Category: <?php echo REQUIRED_INDICATOR ?>
        <?php $cats = budget_category::get_budget_categories(); ?>
        <select id='ddlCategory' name='ddlCategory'>
          <?php foreach ($cats as $cat) : ?>
              <option value="<?php echo $cat->id; ?>" <?php if ($cat->id == $form_display["ddlCategory"]) : ?>selected<?php endif; ?> ><?php echo $cat->name; ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $compName_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Company: <?php echo REQUIRED_INDICATOR ?>
        <input id="txtCompany" name="txtCompany" type="text" MaxLength="50" value="<?php echo $form_display["txtCompany"] ?>"/>
        <?php if (!empty($compName_err)) :?>
        <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
        <?php endif; ?>
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $value_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Value: <?php echo REQUIRED_INDICATOR ?>
        <div class="row">
          <div class="small-1 columns">
            <span class="prefix">$</span>
          </div>
          <div class='small-11 columns'>
            <input id="txtValue" name="txtValue" type="text" MaxLength="50" value="<?php echo $form_display["txtValue"] ?>" placeholder="0.00" />
          </div>
          <?php if (!empty($value_err)) :?>
          <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
          <?php endif; ?>
        </div>      
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $value_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Date: <?php echo REQUIRED_INDICATOR ?>
      <input id="txtDate" name="txtDate" type="text" MaxLength="50" value="<?php echo $form_display["txtDate"] ?>" placeholder="mm/dd/yyyy" class="foundation-date" />
      </label>
      <?php if (!empty($value_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
    <?php /*<div class="small-12 medium-6 small-centered columns">
      <label>Time:
      <input id="txtTime" name="txtTime" type="text" MaxLength="50" value="<?php echo $form_display["txtTime"] ?>" placeholder="00:00 am/pm" class="foundation-time"/>
      </label>
    </div>*/ ?>
  </div>
  
  <div class="row formRow">
    <div class="small-12 medium-6 small-centered columns">
      <label for="cbxRecurring">
        <input id="cbxRecurring" name="cbxRecurring" type="checkbox" <?php echo "on" === $form_display["cbxRecurring"] ? "checked='checked'" : ""; ?>>Recurring Item
      </label>
    </div>
  </div>
  
  <div class="row formRow hide" id="recurring_details">
    <div class="small-12 medium-6 small-centered columns">
      <label>Recurring Schedule: <?php echo REQUIRED_INDICATOR ?>
        <select id='ddlSchedule' name='ddlSchedule'>
          <option value="<?php echo ledger_item::SCHEDULE_DAILY ?>" <?php if (ledger_item::SCHEDULE_DAILY == $form_display["ddlSchedule"] || "" == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?> >Daily</option>
          <option value="<?php echo ledger_item::SCHEDULE_WEEKLY ?>" <?php if (ledger_item::SCHEDULE_WEEKLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Weekly</option>
          <option value="<?php echo ledger_item::SCHEDULE_BIWEEKLY ?>" <?php if (ledger_item::SCHEDULE_BIWEEKLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Bi-Weekly</option>
          <option value="<?php echo ledger_item::SCHEDULE_SEMIMONTHLY ?>" <?php if (ledger_item::SCHEDULE_SEMIMONTHLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Semi-Monthly</option>
          <option value="<?php echo ledger_item::SCHEDULE_MONTHLY ?>" <?php if (ledger_item::SCHEDULE_MONTHLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Monthly</option>
          <option value="<?php echo ledger_item::SCHEDULE_QUARTERLY ?>" <?php if (ledger_item::SCHEDULE_QUARTERLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Quarterly</option>
          <option value="<?php echo ledger_item::SCHEDULE_SEMIANNUALLY ?>" <?php if (ledger_item::SCHEDULE_SEMIANNUALLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Semi-Annually</option>
          <option value="<?php echo ledger_item::SCHEDULE_ANNUALLY ?>" <?php if (ledger_item::SCHEDULE_ANNUALLY == $form_display["ddlSchedule"]) : ?>selected<?php endif; ?>>Annually</option>
        </select>
      </label>
    </div>
    
    <div class="small-12 medium-6 small-centered columns">
      <label>Stop on: </label>
      <div class="columns small-6 end stopSchedule_container stopSchedule_count_container active">
        <input type="radio" name="stopSchedule" value="count" id="stopSchedule_count" checked>
        <label for="stopSchedule_count">
          After 
            <input type="text" id="stopSchedule_count_value"  name="stopSchedule_count_value" class="" value="<?php echo $form_display["stopSchedule_count_value"]; ?>" /> 
          Transactions
        </label>
      </div>
      <div class="columns small-6 end stopSchedule_container stopSchedule_date_container">
        <input type="radio" name="stopSchedule" value="date" id="stopSchedule_date">
        <label for="stopSchedule_date">
          Selected Date: 
          <input type="text" id="stopSchedule_count_value" name="stopSchedule_date_value" class="foundation-date" disabled value="<?php echo $form_display["stopSchedule_date_value"]; ?>" />
        </label>
      </div>
    </div>
    
  </div>
  
  <div class="row formRow">
    <div class="small-12 medium-6 small-centered columns">
      <ul class="button-group">
        <li><a class="small button secondary" href="./">Cancel</a></li>
        <li><input type="submit" class="small button" type="submit"></li>
      </ul>      
    </div>
  </div>
</form>
<?php include "components/global/footer.php"; ?>
  