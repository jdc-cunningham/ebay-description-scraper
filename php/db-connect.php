<?php

  try {
    $dbusername = "";
    $dbpassword = "";
    $dbh = new PDO('mysql:host=localhost;dbname=',$dbusername,$dbpassword);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
  }

?>