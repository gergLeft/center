<?php
  set_include_path($_SERVER["DOCUMENT_ROOT"]);
  $page_title = 'Budget : Test Ledger Schedule';
  $page_tag = 'budget-test_ledger_schedule';
  $form_display = array(
  );
  include '/functions.php';
  include 'components/global/header.php';
  
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "daily", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/02/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "weekly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "sunday", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/08/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "bi-weekly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "sunday", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/15/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "semi-monthly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/16/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/15/2016", //the transaction date
        'pattern' => "semi-monthly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/30/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/16/2016", //the transaction date
        'pattern' => "semi-monthly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/31/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/17/2016", //the transaction date
        'pattern' => "semi-monthly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "06/02/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/31/2016", //the transaction date
        'pattern' => "semi-monthly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "06/15/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "monthly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "06/01/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "quarterly", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "08/01/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "semi-annually", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "11/01/2016", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
  $tests[] = array(
      "args" => array(
        'date' => "05/01/2016", //the transaction date
        'pattern' => "annually", //pattern = daily, weekly, bi-weekly, semi-monthly, monthly, quarterly, semi-annually, annually
        'scheduleDate' => "", //scheduleDate = sunday-saturday (bi/weekly); 1-31 or last_day (semi/monthly); calendar date (quarterly/semi/annually)
      ),
      "expected" => "05/01/2017", //date of the transaction that is expected
      "results" => ""//date of the transaction that is returned
  );
   
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_DEPRECATED);

  foreach ($tests as $test){ 
    echo "Date: " . $test["args"]["date"];
    echo "<br />Pattern: " . $test["args"]["pattern"];
    echo "<br />ScheduleDate: " . $test["args"]["scheduleDate"];
    echo "<br /><br />Expected: " . $test["expected"];
    $test["results"] = get_next_scheduled_day($test["args"]["date"], $test["args"]["pattern"], $test["args"]["scheduleDate"]);
    echo "<br />Result: " . $test["results"];
    dump($test["expected"] === $test["results"]);
    echo "<hr />";
  }
  include "components/global/footer.php"; 
  
?>