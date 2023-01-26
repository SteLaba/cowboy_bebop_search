<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Cowboy Bebop - Sign Up</title>
  <link rel="stylesheet" type="text/css" href="signup_style.css" />
</head>
<body>
  <h1>Cowboy Bebop - Sign Up</h1>

<?php
  require_once('connectvars.php');

  // Connect to the database
  $dbc = pg_connect(DBC_DATA);

  if (isset($_POST['submit'])) {
    // Grab the profile data from the POST
    $username = pg_escape_string($dbc, trim($_POST['username']));
    $password1 = pg_escape_string($dbc, trim($_POST['password1']));
    $password2 = pg_escape_string($dbc, trim($_POST['password2']));

    $name = pg_escape_string($dbc, trim($_POST['name']));
    $surname = pg_escape_string($dbc, trim($_POST['surname']));
    $gender = pg_escape_string($dbc, trim($_POST['gender']));
    $birthdate = pg_escape_string($dbc, trim($_POST['birthdate']));
    $collected_heads = pg_escape_string($dbc, trim($_POST['collectedheads']));
    $celestial_body_id = pg_escape_string($dbc, trim($_POST['celestialbody']));
    $money = pg_escape_string($dbc, trim($_POST['money']));

    if (!empty($username) 
      && !empty($name)
      && !empty($surname)
      && !empty($gender)
      && !empty($birthdate)
      && !empty($collected_heads)
      && !empty($celestial_body_id)
      && !empty($money)
      && !empty($password1) 
      && !empty($password2) 
      && ($password1 == $password2)) {
      // Make sure someone isn't already registered using this username
      $query = "SELECT \"LoginID\" FROM \"Login\" WHERE \"Username\" = '$username'";
      $data = pg_query($dbc, $query);

      if (pg_num_rows($data) == 0) {
        // The username is unique, so insert the data into the database        
        pg_query("BEGIN")
          or die('Error starting the transaction.');

        $query1 = "INSERT INTO \"Login\" " .
        "(\"Username\", \"Password\", \"isAdmin\") ".
        "VALUES ('$username', '$password1', false) RETURNING \"LoginID\"";
        $data1 = pg_query($dbc, $query1);
        $login_id = pg_fetch_array($data1)[0];

        $query2 = "INSERT INTO \"BountyHunters\" " . 
        "(\"Name\", \"Surname\", \"Gender\", \"Birthdate\", \"CollectedHeads\",  \"CelestialBodyID\", \"Money\", \"LoginID\")" . 
        "VALUES ('$name', '$surname', '$gender', '$birthdate', '$collected_heads', '$celestial_body_id', '$money', '$login_id')";
        $data2 = pg_query($dbc, $query2);

        if ($data1 && $data2) {
          pg_query("COMMIT")
            or die('Error commiting the transaction.');

          // Confirm success with the user
          echo '<p id="success_id">Your new account has been successfully created. You\'re now ready to <a href="login.php">log in</a>.</p>';

        } else {
          pg_query("ROLLBACK")
            or die('Error rolling back the transaction.');
        }

        pg_close($dbc);
        exit();
      }
      else {
        // An account already exists for this username, so display an error message
        echo '<p class="error">An account already exists for this username.</p>';
        $username = "";
      }
    }
    else {
      echo '<p class="error">You must enter all of the sign-up data.</p>';
    }
  }
?>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
      <legend>Registration Info</legend>
      <label for="name">Name:</label><br>
      <input type="text" name="name" /><br />
      <label for="name">Surname:</label><br>
      <input type="text" name="surname" /><br />
      <label for="gender">Gender:</label><br>
      <select name="gender">
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      </select><br>
      <label for="birthdate">Date of birth:</label><br>
      <input type="date" name="birthdate" /><br />
      <label for="collectedheads">Total amount of collected heads:</label><br>
      <input type="number" name="collectedheads" /><br />
      <label for="celestialbody">Choose Celestial Body of birth:</label><br>
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
      <label for="money">Money:</label><br>
      <input type="number" name="money" /><br />
      <label for="username">Username:</label><br>
      <input type="text" id="username" name="username"  /><br />
      <label for="password1">Password:</label><br>
      <input type="password" id="password1" name="password1" /><br />
      <label for="password2">Password (retype):</label><br>
      <input type="password" id="password2" name="password2" /><br />
      <input type="submit" value="Sign Up" id="submit" name="submit" />
    </fieldset>
  </form>
</body> 
</html>
<?php
pg_close($dbc);
?>