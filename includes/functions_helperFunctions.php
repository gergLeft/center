<?php

function isExternalPage($page) {
	switch($page) {
		case "login":
		case "registration":
			return true;
			break;
		default:
			return false;
			break;
	}
}

function randString($length = 8) {
  return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function clearFormDisplay(&$arr) {
  foreach ($arr as $a) {
    $a = "";
  }
}

function get_next_scheduled_day($startDate, $pattern) {
  //starting on the start date, find the next scheduled date (this is the first occurrance)
  //use the pattern and schedule date (ie. weekly/Sunday; monthly:1st of the month; yearly:Jan 1st)
  switch($pattern) {
    default:
      return false;
      break;
    case ledger_item::SCHEDULE_DAILY:
      $date = strtotime("+1 day", strtotime($startDate));
      break;
    case ledger_item::SCHEDULE_WEEKLY:
      $date = strtotime('next ' . date("l", strtotime($startDate)), strtotime($startDate));//weekly
      break;
    case ledger_item::SCHEDULE_BIWEEKLY:
      $date = strtotime('+1 week', strtotime('next ' . date("l", strtotime($startDate)), strtotime($startDate)));//bi-weekly
      break;
    case ledger_item::SCHEDULE_MONTHLY:
      // is day of month BEFORE $startDate - use next month
      $date = strtotime("+1 month", strtotime($startDate));
      break;
    case ledger_item::SCHEDULE_SEMIMONTHLY://day of month & day of month+15 days
      $sdt = strtotime($startDate);
      $d = date("d", $sdt);
      $m = date("m", $sdt);
      $y = date("y", $sdt);
      if ( $d <= 13) {
        //$date = this month, 15 days after "d"
        $date = strtotime("+15 days", $sdt);//monthly
      } else if ( 14 == $d || 15 == $d || 16 == $d) {
        $date = strtotime("+15 days", $sdt);//monthly
        dump(date("m", $date));
        dump($m);
        if (date("m", $date) !== $m) {
          $date = strtotime("last day of this month", $sdt);
        }
      } else {
        $d = (31 == $d) ? $d = 15 : $d-15;
        $sd = strtotime("+1 month", strtotime($m . "/01/" . $y));//next month
        $m = date("m", $sd);
        $y = date("y", $sd);
        $date = strtotime($m . "/" . $d . "/" . $y);
      }
      break;
    case ledger_item::SCHEDULE_QUARTERLY://day of month & day of month+15 days
      // else day of month IS $startDate or AFTER - use this month
      $date = strtotime("+3 months", strtotime($startDate));//monthly
      break;
    case ledger_item::SCHEDULE_SEMIANNUALLY://day of month & day of month+15 days
      // else day of month IS $startDate or AFTER - use this month
      $date = strtotime("+6 months", strtotime($startDate));//monthly
      break;
    case ledger_item::SCHEDULE_ANNUALLY://day of month & day of month+15 days
      // else day of month IS $startDate or AFTER - use this month
      $date = strtotime("+1 year", strtotime($startDate));//monthly
      break;
  }
  
  return date("m/d/Y", $date);
}
?>