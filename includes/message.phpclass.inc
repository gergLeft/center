<?php
/**
 * @name Message
 */
class Message {
  
  const LEDGER_ITEM_CREATED = 'ledger_item_created';
  const ERROR_COMPLETING_REQUEST = 'error_completing_request';
  const DUPLICATE_USERNAME = 'duplicate_username';
  const PWD_DONT_MATCH = 'pwd_dont_match';
  const FIELD_MISSING = 'field_missing';
  const MISSING_FIELD = self::FIELD_MISSING;
  const PWD_TOO_WEAK = 'pwd_too_weak';
  const ACCOUNT_CREATED = 'account_created';
  const UNAUTHORIZED_USER = 'unauthorized_user';
  const DUPLICATE_CATEGORY = 'duplicate_category';
  const CATEGORY_CREATED = 'category_created';
  const PROJECT_CREATED = 'project_created';
  
  public static function translate_code_to_message($err){
    switch($err) {
      default:
        return "An unknown error has occurred";
        break;
      case self::ERROR_COMPLETING_REQUEST://generic error
        return "There was a prolem processing your request";
        break;
      case self::DUPLICATE_USERNAME:
        return "An account with this username already exists";
        break;
      case self::DUPLICATE_CATEGORY:
        return "You already have a category by the given name";
        break;
      case self::PWD_DONT_MATCH:
        return "Passwords do not match";
        break;
      case self::FIELD_MISSING:
        return "Please fill in required field";
        break;
      case self::PWD_TOO_WEAK:
        return "Password is too weak, please enter a strong password";
        break;
      case self::ACCOUNT_CREATED:
        return "Your account has been created";
        break;
      case self::CATEGORY_CREATED:
        return "Your category has been created";
        break;
      case self::UNAUTHORIZED_USER:
        return "Invalid username and/or password";
        break;
      case self::LEDGER_ITEM_CREATED:
        return "Your ledger item has been saved";
        break;
      case self::PROJECT_CREATED:
        return "Your project has been saved";
        break;
    }
  }
}
?>