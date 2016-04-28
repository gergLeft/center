<?php
  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : Create Ledger Item';
  $page_tag = 'budget-create_ledger_item';
  $form_display = array(
    'ddlCategory' => '',
    'txtCompany' => '',
    'txtValue' => '',
    'txtDate' => '',
    'txtTime' => '',
  );
  include 'functions.php';

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
      $new_ledger_item->category = $_POST["ddlCategory"];
      $new_ledger_item->company = $_POST["txtCompany"];
      $new_ledger_item->value = $_POST["txtValue"];
      $new_ledger_item->date = $_POST["txtDate"];
      $new_ledger_item->time = $_POST["txtTime"];
      
      try {
        $new_ledger_item->create_ledger_item();
        
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
      <input id="txtDate" name="txtDate" type="text" MaxLength="50" value="<?= $form_display["txtDate"] ?>" placeholder="0.00" />
      </label>
      <?php if (!empty($value_err)) :?>
      <small class="error"><?= Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div><div class="small-12 medium-6 small-centered columns">
      <label>Time:
      <input id="txtTime" name="txtTime" type="text" MaxLength="50" value="<?= $form_display["txtTime"] ?>" placeholder="0.00" />
      </label>
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
  