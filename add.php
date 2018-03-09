<?php
session_start();
if ( ! isset($_SESSION['name']) ) {

die('ACCESS DENIED');

}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
require_once "pdo.php";
require_once "util.php";
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){

  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header('Location: add.php');
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header('Location: add.php');
    return;
  }

    $stmt = $pdo->prepare('INSERT INTO profile
(user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :lnm, :em, :hd, :sm)');
$stmt->execute(array(
':uid' => $_SESSION['user_id'],
':fn' => $_POST['first_name'],
':lnm' => $_POST['last_name'],
':hd' => $_POST['headline'],
':sm' => $_POST['summary'],
':em' => $_POST['email'])
);
$profile_id = $pdo->lastInsertId();
insertPositions($pdo, $profile_id);
insertEducations($pdo, $profile_id);


      $_SESSION['success'] = "Record added";
      header('Location: index.php');
      return;

}

?>
<!DOCTYPE html>
<html>
<head>
<title>Dimitar Bochvarovski's registry</title>
<?php require_once "bootstrap.php"; ?>
<script
src="https://code.jquery.com/jquery-3.2.1.js"
integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
crossorigin="anonymous"></script>
<script
src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<?php
$name = $_SESSION['name'];
echo "<h1>";
echo "Adding Profile for ";
echo ($name);
echo ("</h1>\n");
flash_message();
?>
<form method="post">
<p>First Name:
  <input type="text" name="first_name" size="80"></p>
  <p>Last Name:
    <input type="text" name="last_name" size="80"></p>
<p>Email:
  <input type="text" name="email" size="40"></p>
<p>Headline:
  <input type="text" name="headline" size="100"></p>
  <p>Summary:</p>
  <p> <textarea name = "summary" rows="8" cols="150"></textarea></p>
  <p>
    Education: <input type="submit" id="addEdu" value="+">
  <div id="edu_fields">
  </div>
</p>
  <p>
    Position: <input type="submit" id="addPos" value="+">
  <div id="position_fields">
  </div>
</p>
<p>
  <input type="submit" value="Add">
  <input type="submit" name="cancel" value="Cancel"
</p>
</form>


<script>
countEdu = 0;
  $('#document').ready(function(){
    //window.console && console.log('Document ready called');
    $('#addEdu').click(function(event){
      event.preventDefault();
      if(countEdu >= 9){
        alert("Maximum of nine position entires exceeded");
        return;
      }
      countEdu++;
      window.console && console.log('Adding education: '+countEdu);
      $('#edu_fields').append(
        '<div id="education'+countEdu+'">\
        <p>Year: <input type="text" name="edu_year'+countEdu+'" value=""/>\
        <input type="button" value="-"\
        onclick="$(\'#education'+countEdu+'\').remove();return false;"></p>\
        <p>School: <input type="text" id="school'+countEdu+'" size="80" name="edu_school'+countEdu+'" class="school" value=""</p></div>'
      );
      $('#school'+countEdu+'').on('input',function(e){
        txt = document.getElementById('school'+countEdu+'').value;
        url = "school.php?term=" + txt;
      console.log(url);
        $.getJSON(url, function(data){
          $('.school').autocomplete({
          source: data
          });
          window.console && console.log(data);
        })
      });

    });
  });

countPos = 0;
  $('#document').ready(function(){
    //window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
      event.preventDefault();
      if(countPos >= 9){
        alert("Maximum of nine position entires exceeded");
        return;
      }
      countPos++;
      window.console && console.log('Adding position: '+countPos);
      $('#position_fields').append(
        '<div id="position'+countPos+'">\
        <p>Year: <input type="text" name="year'+countPos+'" value=""/>\
        <input type="button" value="-"\
        onclick="$(\'#position'+countPos+'\').remove();return false;"></p>\
        <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></div>'
      );
    });
  });
</script>
</body>
</html>
