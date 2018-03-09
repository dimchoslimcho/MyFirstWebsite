<?php
require_once "pdo.php";
if(!isset($_GET['term'])) die('Missing required parameter');

/*if(!isset($_COOKIE[sesion_name()])){
  die("Must be logged in"); // Treba da go razgledam ubavo ovoj kod
}*/


session_start();

if(!isset($_SESSION['user_id'])) die("ACCESS DENIED");

//echo ($_GET['term']);
$stmt = $pdo->prepare('SELECT name FROM Institution
WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_GET['term']."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
$retval[] = $row['name'];
}


echo(json_encode($retval, JSON_PRETTY_PRINT));
