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
    'ddlSchedule_date_week' => '',
    'ddlSchedule_date_month' => '',
    'ddlSchedule_date_year' => '',
    'stopSchedule' => '',
    'stopSchedule_count_value' => '',
    'stopSchedule_date_value' => ''
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
          switch ($_POST['ddlSchedule']) {
            default:
            case ledger_item::SCHEDULE_DAILY:
              $scheduleDate = "daily";
              break;
            case ledger_item::SCHEDULE_WEEKLY:
            case ledger_item::SCHEDULE_BIWEEKLY:  
              $scheduleDate = $_POST["ddlSchedule_date_week"];
              break;
            case ledger_item::SCHEDULE_SEMIMONTHLY:
            case ledger_item::SCHEDULE_MONTHLY:
              $scheduleDate = $_POST["ddlSchedule_date_month"];
              break;
            case ledger_item::SCHEDULE_QUARTERLY:
            case ledger_item::SCHEDULE_SEMIANNUALLY:
            case ledger_item::SCHEDULE_ANNUALLY:
              $scheduleDate = $_POST["ddlSchedule_date_year"];
              break;
          }
          
          $new_ledger_item->create_recurring_ledger_item($_POST['ddlSchedule'], $scheduleDate, $_POST["stopSchedule"], $_POST["stopSchedule_" . $_POST["stopSchedule"] . "_value"] );
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
  <div class="row formRow <?= $serverFeedbackClass ?>">
    <div class="small-12 medium-6 small-centered columns">
      <span id="lblServerFeedback"><?= $serverFeedback; ?></span>
    </div>
  </div>
  <?php endif; ?>
  
  <div class="row formRow required <?= $catName_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Type: <?= REQUIRED_INDICATOR ?>
        <select id='ddlType' name='ddlType'>
          <option value="<?= ledger_item::EXPENSE ?>"><?= ucwords(ledger_item::EXPENSE) ?></option>
          <option value="<?= ledger_item::INCOME ?>"><?= ucwords(ledger_item::INCOME) ?></option>
        </select>
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?= $catName_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Category: <?= REQUIRED_INDICATOR ?>
        <?php $cats = budget_category::get_budget_categories(); ?>
        <select id='ddlCategory' name='ddlCategory'>
          <?php foreach ($cats as $cat) : ?>
              <option value="<?= $cat->id; ?>"><?= $cat->name; ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?= $compName_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Company: <?= REQUIRED_INDICATOR ?>
        <input id="txtCompany" name="txtCompany" type="text" MaxLength="50" value="<?= $form_display["txtCompany"] ?>"/>
        <?php if (!empty($compName_err)) :?>
        <small class="error"><?= Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
        <?php endif; ?>
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?= $value_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Value: <?= REQUIRED_INDICATOR ?>
        <div class="row">
          <div class="small-1 columns">
            <span class="prefix">$</span>
          </div>
          <div class='small-11 columns'>
            <input id="txtValue" name="txtValue" type="text" MaxLength="50" value="<?= $form_display["txtValue"] ?>" placeholder="0.00" />
          </div>
          <?php if (!empty($value_err)) :?>
          <small class="error"><?= Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
          <?php endif; ?>
        </div>      
      </label>
    </div>
  </div>
  
  <div class="row formRow required <?= $value_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Date: <?= REQUIRED_INDICATOR ?>
      <input id="txtDate" name="txtDate" type="text" MaxLength="50" value="<?= $form_display["txtDate"] ?>" placeholder="mm/dd/yyyy" class="foundation-date" />
      </label>
      <?php if (!empty($value_err)) :?>
      <small class="error"><?= Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
    <?php /*<div class="small-12 medium-6 small-centered columns">
      <label>Time:
      <input id="txtTime" name="txtTime" type="text" MaxLength="50" value="<?= $form_display["txtTime"] ?>" placeholder="00:00 am/pm" class="foundation-time"/>
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
      <label>Recurring Schedule: <?= REQUIRED_INDICATOR ?>
        <select id='ddlSchedule' name='ddlSchedule'>
          <option value="<?= ledger_item::SCHEDULE_DAILY ?>">Daily</option>
          <option value="<?= ledger_item::SCHEDULE_WEEKLY ?>">Weekly</option>
          <option value="<?= ledger_item::SCHEDULE_BIWEEKLY ?>">Bi-Weekly</option>
          <option value="<?= ledger_item::SCHEDULE_SEMIMONTHLY ?>">Semi-Monthly</option>
          <option value="<?= ledger_item::SCHEDULE_MONTHLY ?>">Monthly</option>
          <option value="<?= ledger_item::SCHEDULE_QUARTERLY ?>">Quarterly</option>
          <option value="<?= ledger_item::SCHEDULE_SEMIANNUALLY ?>">Semi-Annually</option>
          <option value="<?= ledger_item::SCHEDULE_ANNUALLY ?>">Annually</option>
        </select>
      </label>
    </div>
    <div class="small-12 medium-6 small-centered columns">
      <label>Recurring Schedule Base: <?= REQUIRED_INDICATOR ?>
        <span class="scheduleBaseVisibilityCtrl scheduleBaseVisibilityCtrl_day"><br />Every day</span>
        <select id='ddlSchedule_date_week' name='ddlSchedule_date_week' class="scheduleBaseVisibilityCtrl scheduleBaseVisibilityCtrl_week  hideI" >
          <option value="sunday">Sunday</option>
          <option value="monday">Monday</option>
          <option value="tuesday">Tuesday</option>
          <option value="wednesday">Wednesday</option>
          <option value="thursday">Thursday</option>
          <option value="friday">Friday</option>
          <option value="saturday">Saturday</option>
        </select>
        <select id='ddlSchedule_date_month' name='ddlSchedule_date_month' class="scheduleBaseVisibilityCtrl scheduleBaseVisibilityCtrl_month hideI" >
          <?php for ($i=1; $i<=31; $i++) : ?>
          <option value="<?= $i ?>"><?= $i <= 28 ? $i : $i . " (or last day of month)" ?></option>
          <?php endfor; ?>
        </select>
        <input type="text" id="txtSchedule_date_year" name="txtSchedule_date_year" class="scheduleBaseVisibilityCtrl scheduleBaseVisibilityCtrl_year hideI foundation-date" />
      </label>
    </div>
    <div class="small-12 medium-6 small-centered columns">
      <label>Stop on: </label>
      <div class="columns small-6 end stopSchedule_container stopSchedule_count_container active">
        <input type="radio" name="stopSchedule" value="count" id="stopSchedule_count" checked>
        <label for="stopSchedule_count">
          After 
            <input type="text" id="stopSchedule_count_value"  name="stopSchedule_count_value" class=""/> 
          Transactions
        </label>
      </div>
      <div class="columns small-6 end stopSchedule_container stopSchedule_date_container">
        <input type="radio" name="stopSchedule" value="date" id="stopSchedule_date">
        <label for="stopSchedule_date">
          Selected Date: 
          <input type="text" id="stopSchedule_count_value"  name="stopSchedule_date_value" class="foundation-date" disabled />
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
  