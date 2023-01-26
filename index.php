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
  <link rel="stylesheet" type="text/css" href="index_style.css" />
</head>
<body>

<?php
  require_once('connectvars.php');

  // Connect to the database 
  $dbc = pg_connect(DBC_DATA); 

  // Generate the navigation menu
  if (isset($_SESSION['user_id'])) {
    echo '<div id="bh_info">';
    echo '<fieldset>';
    echo '<legend>Profile info:</legend>';
    if (!$_SESSION['is_admin']) {
      $login_id = pg_escape_string($dbc, trim($_SESSION['user_id']));
    
      $query = "SELECT \"Name\", \"Surname\", \"Gender\", \"Birthdate\", \"CollectedHeads\", \"Status\", \"Money\" FROM \"BountyHunters\" WHERE \"LoginID\" = '$login_id'";

      $data = pg_query($dbc, $query);
      $user_data = pg_fetch_array($data);
      echo '<p>Bounty Hunter: '. $user_data['Name'] . ' ' . $user_data['Surname'] . '</p>';  
      echo '<p>Gender: ' . $user_data['Gender'] .'</p>';
      echo '<p>Date of birth: ' . $user_data['Birthdate'] . '</p>';
      echo '<p>Number of collected Bounty Heads: ' . $user_data['CollectedHeads'] . '</p>';
      echo '<p>Money: ' . $user_data['Money'] . '</p>';
      
    } else {
      // Redirect to the home page
       $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/policestation.php';
       header('Location: ' . $home_url);
    }
    echo '<a href="logout.php">Log Out (' . $_SESSION['username'] . ')</a>';
    echo '</fieldset>';
    echo '</div>';
  }
  else {
    // Redirect to the home page
       $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login.php';
       header('Location: ' . $home_url);
  }

  // Retrieve the user data from PostgreSQL
  $query = "SELECT \"BountyHeads\".\"Name\" \"BHName\", \"BountyHeads\".\"Surname\", \"BountyHeads\".\"Gender\", " .
  " \"BountyHeads\".\"BirthDate\", \"BountyHeads\".\"CrimeRecord\", \"CelestialBodies\".\"Name\" \"CBName\",  " .
  " \"CrimeTypes\".\"Name\", \"Crimes\".\"Details\", \"Crimes\".\"JailSentence\", " .
  " \"Crimes\".\"CommitedDate\",  \"Warrants\".\"Amount\" , \"Warrants\".\"WarrantID\" " .
  "FROM \"Warrants\" JOIN \"BountyHeads\" ON \"Warrants\".\"WarrantID\" =  \"BountyHeads\".\"WarrantID\" ".
  "JOIN \"Crimes\" ON \"Warrants\".\"WarrantID\" =  \"Crimes\".\"WarrantID\" " .
  "JOIN \"CelestialBodies\" ON \"BountyHeads\".\"CelestialBodyID\" =  \"CelestialBodies\".\"CelestialBodyID\" ".
  "JOIN \"CrimeTypes\" ON \"Crimes\".\"CrimeTypeID\" =  \"CrimeTypes\".\"CrimeTypeID\" " .
  "WHERE \"Status\" LIMIT 10";
  $data = pg_query($dbc, $query);

  // Loop through the array of user data, formatting it as HTML
  
  echo '<table>';
  echo '<caption>Wanted</caption';
  echo '<tr><th>Bounty Head</th>' .
       '<th>Gender</th>' . 
       '<th>Birthdate</th>' .
       '<th>Crime Record</th>' . 
       '<th>Celestial Body</th>' .
       '<th>Crime</th>' . 
       '<th>Crime Details</th>' .
       '<th>Jail Sentence</th>' . 
       '<th>Date of Commited Crime</th>' .
       '<th>Bounty</th>' .
       '<th>Action</th>' . 
       '<tr>';
  while ($row = pg_fetch_array($data)) {
    if (isset($_SESSION['user_id']) && !$_SESSION['is_admin']) {
      echo '<form method="post" action="update.php">' .
      '<td>' . $row['BHName'] . ' ' . $row['Surname'] . '</td>' . 
      '<td>' . $row['Gender'] . '</td>' .
      '<td>' . $row['BirthDate'] . '</td>' .
      '<td>' . $row['CrimeRecord'] . '</td>' .
      '<td>' . $row['CBName'] . '</td>' .
      '<td>' . $row['Name'] . '</td>' .
      '<td>' . $row['Details'] . '</td>' .
      '<td>' . $row['JailSentence'] . '</td>' .
      '<td>' . $row['CommitedDate'] . '</td>' .
      '<td>' . $row['Amount'] . '</td>' .
      '<td><button type="submit" value="' . $row['WarrantID'] . '" name="warrant_id" id="submit">Take Order!</button></td>' .
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