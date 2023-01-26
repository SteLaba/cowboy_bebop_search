<?php
	// If the user is logged in, take the order.
  	session_start();
  	require_once('connectvars.php');

  	if (isset($_SESSION['user_id'])) {
    	$dbc = pg_connect(DBC_DATA); 
    	$login_id = (int)$_SESSION['user_id'];
    	$warrantid =(int)$_POST['warrant_id'];

    	$bounty_hunter_id = pg_fetch_array(pg_query("SELECT \"BountyHunterID\" FROM \"BountyHunters\" WHERE \"LoginID\" = '$login_id'"))[0];

  		// Retrieve the user data from PostgreSQL
  		$query = "UPDATE \"Warrants\" SET \"Status\" = false, \"BountyHunterID\" = $bounty_hunter_id, \"Amount\" = 0 WHERE \"WarrantID\" = '$warrantid'";
  		$data = pg_query($dbc, $query);
  	}

  // Redirect to the home page
  	$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
  	header('Location: ' . $home_url);
?>