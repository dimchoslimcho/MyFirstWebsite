<?php
session_start();
require_once "pdo.php";
require_once "util.php";
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';



// Check to see if we have some POST data, if we do process it

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
  unset($_SESSION['name']);
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "Both fields must be filled out";
        header("Location: login.php");
        return;

    }
    else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
      $_SESSION['error'] = "Invalid email address";
      header("Location: login.php");
      return;

    }
     else {
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
        $stmt->execute(array(':em'=> $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            // Redirect the browser to game.php

            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            error_log("Login success ".$_POST['email']);
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = $failure = "Incorrect email or password";
            error_log("Login fail ".$_POST['email']." $check");
            header("Location: login.php");
            return;

        }
    }
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Dimitar Bochvarovski's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php

flash_message();
?>
/*For login use umsi@umich.edu for email
  and php123 for password
*/
<form method="POST">
<p>User Name:
<input type="text" name="email" id = 'email'></p>
<p>Password:
<input type="password" name="pass" id = "id_1723">
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<script>
  function doValidate(){
    console.log('Validating...');
    try{
      addr = document.getElementById('email').value;
      pw = document.getElementById('id_1723').value;
      console.log(addr);
      console.log("Validating addr" +addr + " pw:"+pw);
      if(pw == null || pw == "" || addr == null || addr == ""){
        alert("Both fields must be filled out");
        return false;
      }
      if(addr.indexOf('@') == -1){
        alert("Invalid email address");
        return false;
      }
      return true;
    } catch(e){
      return false;
    }
    return false;
  }

</script>
</div>
</body>
