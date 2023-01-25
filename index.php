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
  <title>Cowboy Bebop - The Wanted List</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php
  require_once('connectvars.php');

  // Connect to the database 
  $dbc = pg_connect(DBC_DATA); 

  // Generate the navigation menu
  if (isset($_SESSION['username'])) {
    $bh_username = pg_escape_string($dbc, trim($_SESSION['username']));
  
    $query = "SELECT \"Name\", \"Surname\", \"Gender\", \"Birthdate\", \"CollectedHeads\", \"Status\", \"Money\" FROM \"BountyHunters\" WHERE \"UserName\" = '$bh_username'";

    $data = pg_query($dbc, $query);
    $user_data = pg_fetch_array($data);
    
    echo '<p>Bounty Hunter: '. $user_data['Name'] . ' ' . $user_data['Surname'] . '</p>';  
    echo '<p>Gender: ' . $user_data['Gender'] .'</p>';
    echo '<p>Date of birth: ' . $user_data['Birthdate'] . '</p>';
    echo '<p>Number of collected Bounty Heads: ' . $user_data['CollectedHeads'] . '</p>';
    echo '<p>Status: ' .$user_data['Status'] . '</p>';
    echo '<p>Money: ' . $user_data['Money'] . '</p>';

    echo '<a href="logout.php">Log Out (' . $_SESSION['username'] . ')</a>';
  }
  else {
    echo '<a href="login.php">Log In</a><br />';
    echo '<a href="signup.php">Sign Up</a>';
  }

  // Retrieve the user data from MySQL
  $query = "SELECT \"WarrantID\", \"Status\", \"Amount\", \"BountyHunterID\", \"PoliceStationID\" FROM \"Warrants\" WHERE \"Status\" LIMIT 5";
  $data = pg_query($dbc, $query);

  // Loop through the array of user data, formatting it as HTML
  echo '<h4>Wanted:</h4>';
  echo '<table>';
  while ($row = pg_fetch_array($data)) {
    if (isset($_SESSION['user_id'])) {
      echo '<form method="post" action="update.php">' .
      '<td>' . $row['Status'] . '</td>' . 
        '<td>' . $row['Amount'] . '</td>' .
        '<td><button type="submit" value="' . $row['WarrantID'] . '" name="warrant_id">Take Order!</button></td>' .
      '</form></tr>';
    }
    else {
      echo '<td>' . $row['Status'] . '</td>' . 
        '<td>' . $row['Amount'] . '</td>' .
      '</tr>';
    }
  }
  echo '</table>';

  pg_close($dbc);
?>

</body> 
</html>