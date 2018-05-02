<?php
require_once('view/top.php');
require_once('lib/connect.php');
require_once('config/config.php');
$conn = db_init($config["host"], $config["dbuser"], $config["dbpw"], $config["dbname"]);

// extracting only strings to prevent injection attack
$filtered = array(
  'password' => mysqli_real_escape_string($conn, $_POST['password']),
  'email' => mysqli_real_escape_string($conn, $_POST['email']),
  'title' => mysqli_real_escape_string($conn, $_POST['title']),
  'description' => mysqli_real_escape_string($conn, $_POST['description']),
  'location' => mysqli_real_escape_string($conn, $_POST['location'])
);

// Edit data into table

$sql = "
    UPDATE list SET
      password = '{$filtered['password']}',
      email = '{$filtered['email']}',
      title = '{$filtered['title']}',
      description = '{$filtered['description']}',
      location = '{$filtered['location']}'
    WHERE id = {$_POST['id']}
      ";

mysqli_query($conn, $sql);

header("Location: index.php");


 ?>
