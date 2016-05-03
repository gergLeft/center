<?php
  $page_title = 'Registration';
  $page_tag = 'registration';
  $form_display = array(
    'txtRegisterUsername' => '',
  );
  include 'functions.php';

  $username_err = $pwd_err = $confirm_pwd_err = "";
  
  if ( "POST" === $_SERVER['REQUEST_METHOD']) {
    if (empty($_POST["txtRegisterUsername"]) ||
        empty($_POST["txtRegisterPassword"]) ||
        empty($_POST["txtConfirmPassword"])
    ) {
      if (empty($_POST["txtRegisterUsername"])) {
        $username_err = "error";
      }
      if (empty($_POST["txtRegisterPassword"])) {
        $pwd_err = "error";
      }
      if (empty($_POST["txtConfirmPassword"])) {
        $confirm_pwd_err = "error";
      }
      $serverFeedback = 'Please fill out all required fields.';
      $serverFeedbackClass = "error";
    } else if ($_POST["txtRegisterPassword"] === $_POST["txtConfirmPassword"]) {
      $new_member = new membership_info();
      $new_member->username = $_POST["txtRegisterUsername"];
      $new_member->password = $_POST["txtRegisterPassword"];
      try {
        $new_member->createUser();
        
        if ($new_member->user_id > 0) {
          $serverFeedback = Message::translate_code_to_message(Message::ACCOUNT_CREATED);
          $serverFeedbackClass = "success";
          clearFormDisplay($form_display);
        } 
      } catch (Exception $ex) {
        $serverFeedback = Message::translate_code_to_message(Message::DUPLICATE_USERNAME);
        $serverFeedbackClass = $username_err = "error";
      }
    } else {
      $serverFeedback = 'Passwords do not match.';
      $serverFeedbackClass = $confirm_pwd_err = $pwd_err = "error";
    }
  }
?>

<?php include 'components/global/header.php'; ?>
<div id="pnlRegistrationForm">
  <form method="POST" target="_self" class="custom">
    <div class="row">
      <div class="small-6 small-centered text-center columns">
        <h2>Register</h2>
      </div>
    </div>  
    
    <?php if (isset($serverFeedback)) : ?>
    <div class="row formRow <?php echo $serverFeedbackClass ?>">
      <div class="small-12 medium-6 small-centered columns">
        <span id="lblServerFeedback"><?php echo $serverFeedback; ?></span>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="row formRow required <?php echo $username_err ?>">
      <div class="small-12 medium-6 small-centered columns">
        <label>Username: <?php echo REQUIRED_INDICATOR ?></label>
        <input id="txtRegisterUsername" name="txtRegisterUsername" type="text" MaxLength="25" tabindex="10" value="<?php echo $form_display["txtRegisterUsername"] ?>"/>
        <?php if (!empty($username_err)) :?>
        <small class="error"><?php echo Message::translate_code_to_message(Message::DUPLICATE_USERNAME) ?></small>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="row formRow required">
      <div class="small-12 medium-6 small-centered columns">
        <label>Password: <?php echo REQUIRED_INDICATOR ?></label>
        <input id="txtRegisterPassword" name="txtRegisterPassword" type="password" MaxLength="25" tabindex="20" />
        <?php if (!empty($pwd_err)) :?>
        <small class="error"><?php echo Message::translate_code_to_message(Message::PWD_TOO_WEAK) ?></small>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="row formRow required">
      <div class="small-12 medium-6 small-centered columns">
        <label>Confirm Password: <?php echo REQUIRED_INDICATOR ?></label>
        <input id="txtConfirmPassword" name="txtConfirmPassword" type="password" MaxLength="25" tabindex="20" />
        <?php if (!empty($confirm_pwd_err)) :?>
        <small class="error"><?php echo Message::translate_code_to_message(Message::PWD_DONT_MATCH) ?></small>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="row">
      <div class="small-12 text-center small-centered columns">
        <a href="/" class="secondary button" tabindex="50">Cancel</a>
        <input type="submit" id="btnRegisterSubmit" name="btnRegisterSubmit" class="button" value="Submit" tabindex="40" />
      </div>
  	</div>
    
  </form>
</div>
<?php include 'components/global/footer.php'; ?>
