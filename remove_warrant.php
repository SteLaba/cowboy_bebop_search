<?php
  session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      $_SESSION['username'] = $_COOKIE['username'];
    }
  }
  require_once('connectvars.php');

  if (isset($_SESSION['user_id'])) {
    
    if ($_SESSION['is_admin']) {

      $dbc = pg_connect(DBC_DATA); 
      $warrantid = (int)$_GET['warrant_id'];

      // Retrieve the user data from MySQL
      $query = "DELETE FROM \"Warrants\" WHERE \"WarrantID\" = $warrantid AND \"Amount\" = 0";
      $data = pg_query($dbc, $query);
      

    // Redirect to the home page
      $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/policestation.php';
      header('Location: ' . $home_url);
    }
  }
?>