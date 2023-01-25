<?php 
  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {

    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Cowboy Bebop"');
    exit('<h2>Cowboy Bebop</h2>Sorry, you must enter a valid user name and password to access this page.');
  }

    require_once('connectvars.php'); 
    $dbc = pg_connect(DBC_DATA);

    $username = pg_escape_string($dbc, $_SERVER['PHP_AUTH_USER']);
    $password = pg_escape_string($dbc, $_SERVER['PHP_AUTH_PW']);

    $query = "SELECT \"PoliceStationID\" FROM \"PoliceStations\" WHERE \"UserName\" = '$username' AND \"Password\" = '$password'";
    $data = pg_query($dbc, $query);

    if(pg_num_rows($data) == 1) {
      $row = pg_fetch_array($data);
      $police_station_id = $row['PoliceStationID'];
    } 
    else {
      header('HTTP/1.1 401 Unauthorized');
      header('WWW-Authenticate: Basic realm="Cowboy Bebop"');
      exit('<h2>Cowboy Bebop</h2>Sorry, you must enter a valid user name and password to access this page.');
    }
?>