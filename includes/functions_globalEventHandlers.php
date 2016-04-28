<?php
	//user clicks login button
	if ("POST" == $_SERVER["REQUEST_METHOD"] 
		&& isset($_POST["btnLoginSubmit"])
        && !empty($_POST["txtLoginUsername"])
        && !empty($_POST["txtLoginPassword"])
    ) {
		$membership = new membership_info();
		$membership->username = $_POST["txtLoginUsername"];
		$membership->password = $_POST["txtLoginPassword"];
		
		if ($membership->login()) {
          $form_display = array();
          header("Location: dashboard.php");
        } else {
          $serverFeedback = Message::translate_code_to_message(Message::UNAUTHORIZED_USER);
          $serverFeedbackClass = "error";
		}
	}
	
	//user clicks logout button
	if ("POST" == $_SERVER["REQUEST_METHOD"]
		&& isset($_POST["btnLogout"]) ) {
				
		$membership = new membership_info();
		$membership->logout();
	}
?>