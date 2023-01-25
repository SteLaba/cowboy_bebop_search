<?php
  require_once('authorize.php');
  require_once('connectvars.php');

    $dbc = pg_connect(DBC_DATA); 
    $userid = (int)$_SESSION['user_id'];
    $warrantid = (int)$_GET['warrant_id'];

    // Retrieve the user data from MySQL
    $query = "DELETE FROM \"Warrants\" WHERE \"WarrantID\" = $warrantid AND \"Amount\" = 0";
    $data = pg_query($dbc, $query);
    

  // Redirect to the home page
    $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/policestation.php';
    header('Location: ' . $home_url);
?>