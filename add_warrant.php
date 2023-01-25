<?php
  require_once('authorize.php');
  require_once('connectvars.php');

  $first_name = $_POST['firstname'];
  $last_name = $_POST['lastname'];
  $gender = $_POST['gender'];
  $birth_date = $_POST['birthdate'];
  $crime_record= $_POST['crimerecord'];

  $celestial_body_id = $_POST['celestialbody'];

  $crime_type_id= $_POST['crimetype'];
  $crime_details = $_POST['details'];
  $crime_jail_sentence = $_POST['jailsentence'];
  $crime_date_commited = $_POST['datecommited'];

  $warrant_amount = $_POST['amount'];

  $dbc = pg_pconnect(DBC_DATA)
  	or die('Error connecting to PostgreSQL server.');

  pg_query("BEGIN")
  	or die('Error starting the transaction.');

  $query1 = "INSERT INTO \"Warrants\" (\"Status\", \"Amount\", \"PoliceStationID\")" .
  		 "VALUES(true, '$warrant_amount', '$police_station_id') RETURNING \"WarrantID\"";

  $data1 = pg_query($dbc, $query);
   
  $row1 = pg_fetch_array($data);
  $warrant_id = (int)$row[0];

  $query2 = "INSERT INTO \"Crimes\" (\"Details\", \"JailSentence\", \"CommitedDate\", \"CrimeTypeID\", \"WarrantID\")" .
  		 "VALUES('$crime_details', '$crime_jail_sentence', '$crime_date_commited', '$crime_type_id', '$warrant_id')";
  
  $data2 = pg_query($dbc, $query);
   
  $query3 = "INSERT INTO \"BountyHeads\" (\"Name\", \"Surname\", \"Gender\", \"BirthDate\", \"CrimeRecord\", \"CelestialBodyID\", \"WarrantID\")" .
  		 "VALUES('$first_name', '$last_name', '$gender', '$birth_date', '$crime_record', '$celestial_body_id', '$warrant_id')";

  $data3 = pg_query($dbc, $query);

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
?>