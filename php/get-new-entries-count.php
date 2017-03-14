<?php

  // require db connection 
  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'db-connect.php');

  // require scraper
  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'simple_html_dom.php');

  $base_url = "https://www.your-site.com/location-of-listings";

  $result_array = array();

  $counter = 0;

  function prepareTitle($test_string) {
    // convert spaces to dashes
    $test_string = str_replace(" ", "-", $test_string);
    // strip html tags
    $test_string = strip_tags($test_string);
    // url encode
    $test_string = urlencode($test_string);
    // return string 
    return $test_string;
  }

  $stmt = $dbh->prepare('SELECT sid,Title,Description FROM db_table ORDER BY sid DESC');
  if($stmt->execute()) {
    $result = $stmt->fetchAll();
    $result_array['entries'] = array();
    if (count($result) === 0) {
      $result_array['status'] = 'fail';
      echo json_encode($result_array);
    }
    // $result_array['entries']['sid'] = array();
    foreach($result as $row) {
      if (($row['Description'] === NULL) || ($row['Description'] === "NULL") || ($row['Description'] === " ") || ($row['Description'] === null) || ($row['Description'] === "")) {
        $counter++;
        // add to return array
        $result_array['entries'][$row['sid']] = $base_url . $row['sid'] . '/' . prepareTitle($row['Title']) . '.html';
      }
    }
    $result_array['status'] = 'success';
  }
  else {
    $result_array['status'] = 'fail';
  }

  $result_array['count']  = $counter;

  echo json_encode($result_array);
  
?>