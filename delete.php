<?php
session_start();


if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
require_once "pdo.php";
if ( isset($_POST['profile_id'])){



    $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id = :profile_id');
$stmt->execute(array(
':profile_id' => $_POST['profile_id'])
);
      $_SESSION['success'] = "Record deleted";
      header('Location: index.php');
      return;

}
$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :xyz');
$stmt->execute(array(":xyz" =>$_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
  $_SESSION['error'] = 'Bad value for profile_id';
  header('Location: index.php');
}

$firstName = htmlentities($row['first_name']);
$lastName = htmlentities($row['last_name']);

?>

<!DOCTYPE html>
<html>
<head>
<title>Dimitar Bochvarovski's Registry</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">

<h1>Deleteing Profile</h1>


<form method="post">
<input type="hidden" name="profile_id" value="<?=$row['profile_id'] ?>">
<p>First Name:
  <label for="firstName"><?= $firstName ?></label></p>
  <p>Last Name:
    <label for="lastName"><?= $lastName ?></label></p>
  <input type="submit" value="Delete">
  <input type="submit" name="cancel" value="Cancel"
</p>
</body>
</html>
