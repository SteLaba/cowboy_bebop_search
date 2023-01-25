<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Cowboy Bebop - Sign Up</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
  <h3>Cowboy Bebop - Sign Up</h3>

<?php
  require_once('connectvars.php');

  // Connect to the database
  $dbc = pg_connect(DBC_DATA);

  if (isset($_POST['submit'])) {
    // Grab the profile data from the POST
    $username = pg_escape_string($dbc, trim($_POST['username']));
    $password1 = pg_escape_string($dbc, trim($_POST['password1']));
    $password2 = pg_escape_string($dbc, trim($_POST['password2']));

    if (!empty($username) && !empty($password1) && !empty($password2) && ($password1 == $password2)) {
      // Make sure someone isn't already registered using this username
      $query = "SELECT * FROM \"BountyHunters\" WHERE \"UserName\" = '$username'";
      $data = pg_query($dbc, $query);
      if ($data) {
        // The username is unique, so insert the data into the database
        $query = "INSERT INTO \"BountyHunters\" (\"UserName\", \"Password\") VALUES ('$username', '$password1')";
        pg_query($dbc, $query);

        // Confirm success with the user
        echo '<p>Your new account has been successfully created. You\'re now ready to <a href="login.php">log in</a>.</p>';

        pg_close($dbc);
        exit();
      }
      else {
        // An account already exists for this username, so display an error message
        echo '<p class="error">An account already exists for this username. Please use a different address.</p>';
        $username = "";
      }
    }
    else {
      echo '<p class="error">You must enter all of the sign-up data, including the desired password twice.</p>';
    }
  }

  pg_close($dbc);
?>

  <p>Please enter your username and desired password to sign up for Bounty Hunter.</p>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
      <legend>Registration Info</legend>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />
      <label for="password1">Password:</label>
      <input type="password" id="password1" name="password1" /><br />
      <label for="password2">Password (retype):</label>
      <input type="password" id="password2" name="password2" /><br />
    </fieldset>
    <input type="submit" value="Sign Up" name="submit" />
  </form>
</body> 
</html>