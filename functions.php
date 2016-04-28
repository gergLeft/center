<?php
session_start();
	
//enable debug
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_DEPRECATED);

include 'includes/db_sql.phpclass.inc';
include 'includes/message.phpclass.inc';
include 'includes/functions_helperFunctions.php';
include 'includes/membership_info.phpclass.inc';
include 'includes/budget_category.phpclass.inc';
include 'includes/ledger_item.phpclass.inc';

//process global event handlers
include 'includes/functions_globalEventHandlers.php';

//verify logged in - if not, redirect to login page (exclusions may apply)
if (isExternalPage($page_tag)) {
  if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
  }
} else {
  if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
  }
}

//load global variables
$loginProtectedPage = !isExternalPage($page_tag);

$membership = new membership_info();
		
$currentUserInfo = new membership_info();
if (isset($_SESSION["user_id"])) {
  $currentUserInfo->setUserInfo($_SESSION["user_id"]);
}
$currentUser = $currentUserInfo->username;

$passwordStrengthRegularExpression = " @\"(?=.{6,})(?=(.*\d){1,})(?=(.*\W){1,})";

if ( "POST" === $_SERVER['REQUEST_METHOD']) {
  if (isset($form_display)) {
    foreach($form_display as $key => $value){
      if(isset($_POST[$key])){
        $form_display[$key] = htmlspecialchars($_POST[$key]);
      }
    }
  }
}

define('REQUIRED_INDICATOR', "<span><super>*</super></span>");

$defaultHomepageModules = array(
  array(
    "title" => 'Schedule', 
    "slug" => 'schedule'
  ),
  array(
    "title" => 'Grocery', 
    "slug" => 'grocery'
  ),
  array(
    "title" => 'Budget', 
    "slug" => 'budget'
  ),
  array(
    "title" => 'Tasks', 
    "slug" => 'tasks'
  ),
  array(
    "title" => 'Wishlist', 
    "slug" => 'wishlist'
  ),
);

$defaultInactiveModules = array(
  array(
    "title" => 'Chores', 
    "slug" => 'chores'
  ),
  array(
    "title" => 'Meals', 
    "slug" => 'meals'
  ),
  array(
    "title" => 'Classwork', 
    "slug" => 'classwork'
  ),
  array(
    "title" => 'Projects', 
    "slug" => 'projects'
  ),
);
  
function dump($object) {
  echo "<pre>";
  var_dump($object);
  echo "</pre>";
} 
?>