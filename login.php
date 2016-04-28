<?php
  $page_title = 'Login';
  $page_tag = 'login';
  $form_display = array(
    'txtLoginUsername' => ""
  );
  include 'functions.php';
    
  $username_err = $pwd_err = "";
  if ( "POST" === $_SERVER['REQUEST_METHOD']) {
    if (empty($_POST["txtLoginUsername"])) {
      $username_err = "error";
    }
    if (empty($_POST["txtLoginPassword"])) {
      $pwd_err = "error";
    }
  }
?>

<?php include 'components/global/header.php'; ?>
<div id="pnlLoginForm">
  <form method="POST" target="_self" class="custom">
    <?php if (isset($login_err) && !empty($login_err)) : ?>
    <?php endif; ?>
    <div class="row">
      <div class="small-6 small-centered text-center columns">
        <h2>Login</h2>
      </div>
    </div>  
    
    <?php if (isset($serverFeedback)) : ?>
    <div class="row formRow <?= $serverFeedbackClass ?>">
      <div class="small-12 medium-6 small-centered columns">
        <span id="lblServerFeedback"><?= $serverFeedback; ?></span>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="row formRow <?= $username_err ?>">
      <div class="small-12 medium-6 small-centered columns">
        <label>Username: <?= REQUIRED_INDICATOR ?></label>
        <input id="txtLoginUsername" name="txtLoginUsername" type="text" MaxLength="25" tabindex="10" value="<?= $form_display["txtLoginUsername"] ?>"/>
        <?php if (!empty($username_err)) :?>
        <small class="error"><?= Message::translate_code_to_message(Message::MISSING_FIELD) ?></small>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="row formRow <?= $pwd_err ?>">
      <div class="small-12 medium-6 small-centered columns">
        <label>Password: <?= REQUIRED_INDICATOR ?></label>
        <input id="txtLoginPassword" name="txtLoginPassword" type="password" MaxLength="25" tabindex="20" />
        <?php if (!empty($pwd_err)) :?>
        <small class="error"><?= Message::translate_code_to_message(Message::MISSING_FIELD) ?></small>
        <?php endif; ?>
      </div>
    </div>
    
    <?php //TODO: Implement remember logic to keep user signed in to system indefinetly ?>
    <div class="row">
      <div class="small-12 medium-6 small-centered columns">
        <label for="chkPersistCookie" tabindex="30">
          <input type="checkbox" id="chkPersistCookie" tabindex="30" />
          Keep me logged in
        </label>
      </div>
    </div>
    
    <div class="row">
      <div class="small-12 text-center small-centered columns">
        <a href="/register.php" class="secondary button" tabindex="50">Register</a>
        <input type="submit" id="btnLoginSubmit" name="btnLoginSubmit" class="button" value="Login" tabindex="40" />
      </div>
  	</div>
  </form>
</div>
<?php include 'components/global/footer.php'; ?>
