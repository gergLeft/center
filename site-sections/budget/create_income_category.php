<?php
  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : Create Income Category';
  $page_tag = 'budget-create_income_category';
  $form_display = array(
    'txtCatName' => '',
    'txtMonthBudget' => '',
  );
  include 'functions.php';

  //page postback actions
  $catName_err = $monthBudget_err = "";
  if ( "POST" === $_SERVER['REQUEST_METHOD']) {
    if (empty($_POST["txtCatName"]) ||
        empty($_POST["txtMonthBudget"])
    ) {
      if (empty($_POST["txtCatName"])) {
        $catName_err = "error";
      }
      if (empty($_POST["txtMonthBudget"])) {
        $monthBudget_err = "error";
      }
    } else {
      $new_category = new budget_category();
      $new_category->type = budget_category::INCOME;
      $new_category->name = $_POST["txtCatName"];
      $new_category->value = $_POST["txtMonthBudget"];
      try {
        $new_category->create_category();
        
        if ($new_category->id > 0) {
          $serverFeedback = Message::translate_code_to_message(Message::CATEGORY_CREATED);
          $serverFeedbackClass = "success";
          clearFormDisplay($form_display);
        } 
      } catch (Exception $ex) {
        $serverFeedback = Message::translate_code_to_message(Message::DUPLICATE_CATEGORY);
        $serverFeedbackClass = $catName_err = "error";
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
  
  <div class="row formRow required <?php echo $username_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Category Name: <?php echo REQUIRED_INDICATOR ?></label>
      <input id="txtCatName" name="txtCatName" type="text" MaxLength="50" value="<?php echo $form_display["txtCatName"] ?>"/>
      <?php if (!empty($catName_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="row formRow required <?php echo $monthBudget_err ?>">
    <div class="small-12 medium-6 small-centered columns">
      <label>Monthly Budget: <?php echo REQUIRED_INDICATOR ?></label>
      <input id="txtMonthBudget" name="txtMonthBudget" type="text" MaxLength="50" value="<?php echo $form_display["txtMonthBudget"] ?>"/>
      <?php if (!empty($monthBudget_err)) :?>
      <small class="error"><?php echo Message::translate_code_to_message(Message::FIELD_MISSING) ?></small>
      <?php endif; ?>
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
  