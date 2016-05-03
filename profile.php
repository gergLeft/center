<?php
	$page_title = 'Profile';
	$page_tag = 'profile';
	include 'functions.php';
	
	//page postback actions
?>

<?php include 'components/global/header.php'; ?>

  <div class="row">
    <div class="small-12 large-6 columns">
			<h2><?php echo $currentUser; ?></h2>
    </div>
  </div>

<?php include 'components/global/footer.php'; ?>
