<?php
  session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      $_SESSION['username'] = $_COOKIE['username'];
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Cowboy Bebop - Police Station Administration</title>
  <link rel="stylesheet" type="text/css" href="policestation_style.css" />
</head>
<body>
  <h1>Cowboy Bebop - Police Station Administration</h1>

<?php
  require_once('connectvars.php');
  if (isset($_SESSION['user_id'])) {
    
    if ($_SESSION['is_admin']) {

      echo '<a id="logout" href="logout.php">Log Out (' . $_SESSION['username'] . ')</a>';
      // Connect to the database 
      $dbc = pg_connect(DBC_DATA);
      // Retrieve the score data from PostgreSQL
      $query = "SELECT * FROM \"Warrants\" ";
      $data = pg_query($dbc, $query);
      // Loop through the array of score data, formatting it as HTML 
      echo '<table>';
      echo '<caption>Wanted List:</caption>';
      echo '<tr><th>WarrantId</th><th>Status</th><th>Amount</th><th>Action</th></tr>';
      while ($row = pg_fetch_array($data)) { 
        // Display the score data
        echo '<tr class="warrant_row">';
        echo '<td><strong>' . $row['WarrantID'] . '</strong></td>';
        echo '<td>' . $row['Status'] . '</td>';
        echo '<td>' . $row['Amount'] . '</td>';
        if ($row['Amount'] == 0) {
          echo '<td><a id="remove" href="remove_warrant.php?warrant_id=' . $row['WarrantID'] . '">Remove</a>';
        }
        echo '</td></tr>';
      }
      echo '</table>';
?>

  <form method="post" action="add_warrant.php">
    <fieldset>
      <label for="firstname">First name:</label><br>
      <input type="text" name="firstname" /><br />
      <label for="lastname">Last name:</label><br>
      <input type="text" name="lastname" /><br />
      <label for="gender">Choose gender:</label><br>
      <select name="gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select><br>
      <label for="birthdate">Date of birth of Bounty Head:</label><br>
      <input type="date" name="birthdate" /><br />
      <label for="crimerecord">Total amount of crimes commited by Bounty Head:</label><br>
      <input type="number" name="crimerecord" /><br />
      <label for="celestialbody">Choose Celestial Body where Bounty Head is registered:</label><br>
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
      <label for="crimetype">Choose type of crime commited:</label><br>
      <select name="crimetype">
        
      <?php
        $query = "SELECT \"CrimeTypeID\", \"Name\" FROM \"CrimeTypes\"";
        $data = pg_query($dbc, $query);
        while ($row = pg_fetch_array($data)) { 
          // Display the score data
          echo '<option value="' . $row['CrimeTypeID'] . '">' . $row['Name'] . '</option>';
        }
      ?>
      </select><br>
      <label for="details">Details:</label><br>
      <input type="text" name="details" /><br />
      <label for="jailsentence">Jail Sentence (Months):</label><br>
      <input type="number" name="jailsentence" /><br />
      <label for="datecommited">Date when crime was commited:</label><br>
      <input type="date" name="datecommited" /><br />
      <label for="amount">Bounty (Woolong):</label><br>
      <input type="number" name="amount" /><br />
      <input type="submit" value="Issue Warrant" id="submit" name="submit" />
    </fieldset>
  </form>

</body> 
</html>

<?php
  pg_close($dbc); 
    }
    else {
      // Redirect to the home page
       $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
       header('Location: ' . $home_url);
    }
  } else {
    echo '<a href="login.php">Log In</a><br />';
  }
?>