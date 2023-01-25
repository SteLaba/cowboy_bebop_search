<?php
  require_once('authorize.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Cowboy Bebop - Police Station Administration</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
  <h2>Cowboy Bebop - Police Station Administration</h2>
  <p>Wanted List:</p>
  <hr />

<?php
  require_once('connectvars.php');

  // Connect to the database 
  $dbc = pg_connect(DBC_DATA);
  // Retrieve the score data from MySQL
  $query = "SELECT * FROM \"Warrants\" WHERE \"PoliceStationID\" = $police_station_id";
  $data = pg_query($dbc, $query);
  // Loop through the array of score data, formatting it as HTML 
  echo '<table>';
  echo '<tr><th>WarrantId</th><th>Status</th><th>Amount</th><th>Action</th></tr>';
  while ($row = pg_fetch_array($data)) { 
    // Display the score data
    echo '<tr class="warrant_row">';
    echo '<td><strong>' . $row['WarrantID'] . '</strong></td>';
    echo '<td>' . $row['Status'] . '</td>';
    echo '<td>' . $row['Amount'] . '</td>';
    if ($row['Amount'] == 0) {
      echo '<td><a href="remove_warrant.php?warrant_id=' . $row['WarrantID'] . '">Remove</a>';
    }
    echo '</td></tr>';
  }
  echo '</table>';

?>

  <form method="post" action="add_warrant.php">

    <label for="firstname">First name:</label>
    <input type="text" name="firstname" /><br />
    <label for="lastname">Last name:</label>
    <input type="text" name="lastname" /><br />
    <label for="gender">Choose gender:</label>
    <select name="gender">
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select><br>
    <label for="birthdate">Date of birth of Bounty Head:</label>
    <input type="date" name="birthdate" /><br />
    <label for="crimerecord">Total amount of crimes commited by Bounty Head:</label>
    <input type="number" name="crimerecord" /><br />
    <label for="celestialbody">Choose Celestial Body where Bounty Head is registered:</label>
    <select name="celestialbody">
      
    <?php
      $query = "SELECT \"CelestialBodyID\", \"Name\" FROM \"CelestialBodies\"";
      $data = pg_query($dbc, $query);
      while ($row = pg_fetch_array($data)) { 
        // Display the score data
        echo '<option value="' . $row['CelestialBodyID'] . '">' . $row['Name'] . '</option>';
      }
    ?>
    </select><br>
    <label for="crimetype">Choose type of crime commited:</label>
    <select name="crimetype">
      
    <?php
      $query = "SELECT \"CrimeTypeID\", \"Name\" FROM \"CrimeTypes\"";
      $data = pg_query($dbc, $query);
      while ($row = pg_fetch_array($data)) { 
        // Display the score data
        echo '<option value="' . $row['CrimeTypeID'] . '">' . $row['Name'] . '</option>';
      }
      pg_close($dbc);
    ?>
    </select><br>
    <label for="details">Details:</label>
    <input type="textarea" name="details" /><br />
    <label for="jailsentence">Jail Sentence (Months):</label>
    <input type="number" name="jailsentence" /><br />
    <label for="datecommited">Date when crime was commited:</label>
    <input type="date" name="datecommited" /><br />
    <label for="amount">Bounty (Woolong):</label>
    <input type="number" name="amount" /><br />
    <input type="submit" value="Issue Warrant" name="submit" />
  </form>

</body> 
</html>