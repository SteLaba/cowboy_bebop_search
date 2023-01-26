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

    	  $login_id = pg_escape_string($dbc, $_SESSION['user_id']);

	  $first_name = pg_escape_string($dbc, $_POST['firstname']);
	  $last_name = pg_escape_string($dbc, $_POST['lastname']);
	  $gender = pg_escape_string($dbc, $_POST['gender']);
	  $birth_date = pg_escape_string($dbc, $_POST['birthdate']);
	  $crime_record= pg_escape_string($dbc, $_POST['crimerecord']);

	  $celestial_body_id = pg_escape_string($dbc, $_POST['celestialbody']);

	  $crime_type_id= pg_escape_string($dbc, $_POST['crimetype']);
	  $crime_details = pg_escape_string($dbc, $_POST['details']);
	  $crime_jail_sentence = pg_escape_string($dbc, $_POST['jailsentence']);
	  $crime_date_commited = pg_escape_string($dbc, $_POST['datecommited']);

	  $warrant_amount = pg_escape_string($dbc, $_POST['amount']);

	  if (!empty($first_name)
		&& !empty($last_name)
		&& !empty($gender)
		&& !empty($birth_date)
		&& !empty($crime_record)
		&& !empty($celestial_body_id)
		&& !empty($crime_type_id)
		&& !empty($crime_details)
		&& !empty($crime_jail_sentence)
		&& !empty($crime_date_commited)
		&& !empty($warrant_amount)
		) {

		  $dbc = pg_pconnect(DBC_DATA)
		  	or die('Error connecting to PostgreSQL server.');

		  pg_query("BEGIN")
		  	or die('Error starting the transaction.');

		  $police_station_id = pg_fetch_array(pg_query("SELECT \"PoliceStationID\" FROM \"PoliceStations\" WHERE \"LoginID\" = '$login_id'"))[0];

		  $query1 = "INSERT INTO \"Warrants\" (\"Status\", \"Amount\", \"PoliceStationID\")" .
		  		 "VALUES(true, '$warrant_amount', '$police_station_id') RETURNING \"WarrantID\"";

		  $data1 = pg_query($dbc, $query1);
		   
		  $row = pg_fetch_array($data1);
		  $warrant_id = (int)$row[0];

		  $query2 = "INSERT INTO \"Crimes\" (\"Details\", \"JailSentence\", \"CommitedDate\", \"CrimeTypeID\", \"WarrantID\")" .
		  		 "VALUES('$crime_details', '$crime_jail_sentence', '$crime_date_commited', '$crime_type_id', '$warrant_id')";
		  
		  $data2 = pg_query($dbc, $query2);
		   
		  $query3 = "INSERT INTO \"BountyHeads\" (\"Name\", \"Surname\", \"Gender\", \"BirthDate\", \"CrimeRecord\", \"CelestialBodyID\", \"WarrantID\")" .
		  		 "VALUES('$first_name', '$last_name', '$gender', '$birth_date', '$crime_record', '$celestial_body_id', '$warrant_id')";

		  $data3 = pg_query($dbc, $query3);

		  if ($data1 && $data2 && $data3) {
		  	pg_query("COMMIT")
		  	   or die('Error commiting the transaction.');
		  } else {
		  	pg_query("ROLLBACK")
		  	   or die('Error rolling back the transaction.');
		  }

		   pg_close($dbc);

		   // Redirect to the home page
		   $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/policestation.php';
		   header('Location: ' . $home_url);
	  }
	}
  }

?>