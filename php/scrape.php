<?php

  // require db connection 
  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'db-connect.php');

  // require scraper
  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'simple_html_dom.php');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $target_sid = $_POST['sid'];
    $target_url = $_POST['targ_url'];

    function getUrlToScrape($target_url) {
      $url_to_scrape = $target_url;
      $ebay_url_pattern = "vi.vipr.ebaydesc.com";
      try {
        if ($html = file_get_html($url_to_scrape)) {
          $url_array = array();
          foreach($html->find('div[id=ebay-dscr-src]') as $element) {
            array_push($url_array, trim($element->innertext));
          }
          foreach ($url_array as $url) {
            $pos = strpos($url, $ebay_url_pattern);
            if ($pos === false) {
            }
            else {
              return $url;
            }
          }
        }
      }
      catch (Exception $e) {
        return "broken link";
      }
    }

    function getSubDescriptions($targ_url) {
      $url_to_scrape = $targ_url;
      try {
        if ($plaintext = file_get_html($url_to_scrape)->plaintext) {
          $pre_trim = trim($plaintext);
          $pre_trim = html_entity_decode($pre_trim);
          $pre_trim = str_replace(array("\r", "\n"), '', $pre_trim);
          $pre_trim = mb_convert_encoding($pre_trim, "HTML-ENTITIES", 'UTF-8');
          $trimmed_result = substr($pre_trim, 0, 233);
          if ($plaintext[0] === null) {
            return "broken link";
          }
          else {
            return $trimmed_result;
          }
        }
      }
      catch (Exception $e) {
        // if ($plaintext === false) {
        return "broken link";
        //}
      }
    }

    // get url to scrape from post
    $targ_url = getUrlToScrape($target_url);

    // result array
    $result_array = array();

    // check $targ_url;
    if ($targ_url === "broken link") {
      $result_array['status'] = 'fail';
      $result_array['reason'] = 'target url bad';
    }
    else {
      if (getSubDescriptions($targ_url) === "broken link") {
        $result_array['status'] = 'fail';
        $result_array['reason'] = 'get subdescriptions failed';
      }
      else {
         $stmt = $dbh->prepare("UPDATE db_table SET Description=:new_description WHERE sid=:sid");
          // $stmt = $dbh->prepare("UPDATE classifieds_listings SET Description=:new_description WHERE sid=:sid");
          
          $stmt->bindParam(':sid', $target_sid, PDO::PARAM_INT);
          // this line causes errors when errors are turned on 
          // I should create a variable from the result of getSubDescriptions($targ_url) rather
          // than passing the function call into the bindParam line
          $pass_param = getSubDescriptions($targ_url);
          $stmt->bindParam(':new_description', $pass_param, PDO::PARAM_INT);
          if(!empty($pass_param)) {
            $result_array['status'] = 'success';
            $stmt->execute();
          }
          else {
            $result_array['status'] = 'fail';
            $result_array['reason'] = 'Non-ebay listing or no description available.';
          }
      }
    }

    echo json_encode($result_array);

  }
  
?>