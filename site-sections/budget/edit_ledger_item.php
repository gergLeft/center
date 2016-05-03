<?php

  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : Edit Ledger Item';
  $page_tag = 'budget-edit_ledger_item';
  $form_display = array(
    'ddlType' => '',
    'ddlCategory' => '',
    'txtCompany' => '',
    'txtValue' => '',
    'txtDate' => '',
    'txtTime' => '',
  );
  include '/functions.php';
  $this_item = new ledger_item($_GET["tid"]);
  
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
      $this_item->type = $_POST["ddlType"];
      $this_item->category = $_POST["ddlCategory"];
      $this_item->company = $_POST["txtCompany"];
      $this_item->value = $_POST["txtValue"];
      $this_item->date = $_POST["txtDate"];
      
      try {
        $this_item->update_ledger_item();
        
        if ($this_item->id > 0) {
          $serverFeedback = Message::translate_code_to_message(Message::LEDGER_ITEM_CREATED);
          $serverFeedbackClass = "success";
          clearFormDisplay($form_display);
        } 
      } catch (Exception $ex) {
        $serverFeedback = Message::translate_code_to_message(Message::ERROR_COMPLETING_REQUEST);
        $serverFeedbackClass = "error";
      }
    } 
  } else {
    $form_display = array(
      'ddlType' => $this_item->type,
      'ddlCategory' => $this_item->category,
      'txtCompany' => $this_item->company,
      'txtValue' => $this_item->value,
      'txtDate' => $this_item->date,
      'txtTime' => $this_item->time,
    );
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
          <option value="<?php echo ledger_item::EXPENSE ?>" <?php if (ledger_item::EXPENSE == $form_display["ddlType"]) : ?>selected<?php endif; ?>><?php echo ucwords(ledger_item::EXPENSE) ?></option>
          <option value="<?php echo ledger_item::INCOME ?>" <?php if (ledger_item::INCOME == $form_display["ddlType"]) : ?>selected<?php endif; ?>><?php echo ucwords(ledger_item::INCOME) ?></option>
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
      <ul class="button-group">
        <li><a class="small button secondary" href="./">Cancel</a></li>
        <li><input type="submit" class="small button" type="submit"></li>
      </ul>      
    </div>
  </div>
</form>
<?php include "components/global/footer.php"; ?>
  