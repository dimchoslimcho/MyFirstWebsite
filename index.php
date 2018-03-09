<?php
session_start();
require_once "pdo.php";
require_once "util.php";
 ?>
<html>
<head>
<title>Dimitar Bochvarovski</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Dimitar Bochvarovski's Resume Registry</h1>
<?php
flash_message();
if(!isset($_SESSION['name'])){

  echo "<a href='login.php'>Please log in</a>";
}
else {
  echo "<p>";
  echo "<a href='logout.php'>Logout</a>";
  echo "</p>\n";

}
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
  echo ("<p>");
  echo "";
  echo ("</p>");
}else{
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
echo ('<table border="1">'."\n");
echo ("<tr><td>");
echo ("<strong>Name</strong>");
echo ("</td><td>");
echo ("<strong>Headline</strong>");
echo ("</td><td>");
if(isset($_SESSION['name'])){
echo ("<strong>Action</strong>");
echo ("</td></tr>\n");}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo ("<tr><td>");
      $link_address = 'view.php?profile_id='.$row['profile_id'];
      $fn = htmlentities($row['first_name']);
      $ln = htmlentities($row['last_name']);
      echo("<a href='$link_address'>$fn $ln</a>");

      echo ("</td><td>");
      echo (htmlentities($row['headline']));
      echo ("</td><td>");
      if(isset($_SESSION['name'])){
      echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ');
      echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
      echo ("</td></tr>\n");}

    }
echo ("</table>");

}
if(isset($_SESSION['name'])){
  echo "<p>";
  echo "<a href='add.php'>Add New Entry</a>";
  echo "</p>\n";
}
 ?>
</div>
</body>
