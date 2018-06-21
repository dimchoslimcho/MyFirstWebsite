<?php
session_start();
if ( ! isset($_SESSION['name']) ) {

die('ACCESS DENIED');

}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if(!isset($_REQUEST['profile_id'])){
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}
require_once "pdo.php";
require_once "util.php";
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){

  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
  }

    $stmt = $pdo->prepare('UPDATE profile
SET user_id = :uid, first_name = :fn, last_name = :lnm, email = :em, headline = :hd, summary = :sm WHERE profile_id = :profile_id');
$stmt->execute(array(
  ':uid' => $_SESSION['user_id'],
  ':fn' => $_POST['first_name'],
  ':lnm' => $_POST['last_name'],
  ':hd' => $_POST['headline'],
  ':sm' => $_POST['summary'],
  ':profile_id' => $_POST['profile_id'],
  ':em' => $_POST['email'])
);
$stmt = $pdo->prepare('DELETE FROM Position
WHERE profile_id=:pid');
$stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

insertPositions($pdo, $_REQUEST['profile_id']);

$stmt = $pdo->prepare('DELETE FROM Education
WHERE profile_id=:pid');
$stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

insertEducations($pdo, $_REQUEST['profile_id']);




      $_SESSION['success'] = "Record edited";
      header('Location: index.php');
      return;

}
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$position_id = sizeof($positions);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
$school_id = sizeof($schools);
$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :xyz AND user_id = :uid');
$stmt->execute(array(':xyz' =>$_GET['profile_id'], ':uid' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
  $_SESSION['error'] = "Could not load profile";
  header('Location: index.php');
}
$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$email = htmlentities($row['email']);
$flag = 0;
$flag_sch = 0;
if($positions !== false) $flag = 1;
if($schools !== false) $flag_sch = 1;
?>

<!DOCTYPE html>
<html>
<head>
<title>Dimitar Bochvarovski's Registry</title>
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
echo "Edit Profie:";
echo ("</h1>\n");
flash_message();
?>

<form method="post">
<input type="hidden" name="profile_id" value="<?=$row['profile_id'] ?>">
<p>First Name:
  <input type="text" name="first_name" value="<?= $first_name ?>" size="80"></p>
  <p>Last Name:
    <input type="text" name="last_name" value="<?= $last_name ?>" size="80"></p>
<p>Email:
  <input type="text" name="email" value="<?= $email ?>" size="40"></p>
<p>Headline:
  <input type="text" name="headline" value="<?= $headline ?>" size="100"></p>
  <p>Summary:</p>
  <p> <textarea name = "summary" rows="8" cols="150"><?=$summary?></textarea></p>
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
  <input type="submit" value="Save">
  <input type="submit" name="cancel" value="Cancel"
</p>
</form>

<script>
flag_sch = <?= htmlentities($flag_sch) ?>;
school_id = <?= htmlentities($school_id) ?>;
if(flag_sch == 1){
  schools = new Array();
  school = new Array();
  schools = <?php echo json_encode($schools); ?>;
  console.log(schools.length);

  $('#document').ready(function(){
    for(i=0; i < school_id; i++){
      school = schools[i]
      console.log(school['year']);
     window.console && console.log('Position: '+school);
      $('#edu_fields').append(
        '<div id="education'+school['rank']+'">\
        <p>Year: <input type="text" name="edu_year'+school['rank']+'" value="'+school['year']+'"/>\
        <input type="button" value="-"\
        onclick="$(\'#education'+school['rank']+'\').remove();return false;"></p>\
        <p>School: <input type="text" id="school'+school['rank']+'" size="80" name="edu_school'+school['rank']+'" class="school" value="'+school['name']+'"</p></div>'
      );
      $('#school'+school['rank']+'').on('input',function(e){
        txt = document.getElementById('school'+school['rank']+'').value;
        url = "school.php?term=" + txt;
      console.log(url);
        $.getJSON(url, function(data){
          $('.school').autocomplete({
          source: data
          });
          window.console && console.log(data);
        })
      });

    }
  });
  flag_sch = 0;
  countEdu = school_id;
}
else countEdu = 0;
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

flag = <?= htmlentities($flag) ?>;
position_id = <?= htmlentities($position_id) ?>;
console.log(flag);
if(flag == 1){
  positions = new Array();
  position = new Array();
  positions = <?php echo json_encode($positions); ?>;
  console.log(positions.length);

  $('#document').ready(function(){
    for(i=0; i < position_id; i++){
      position = positions[i]
      console.log(position['year']);
     window.console && console.log('Position: '+position);
      $('#position_fields').append(
        '<div id="position'+position['rank']+'">\
        <p>Year: <input type="text" name="year'+position['rank']+'" value="'+position['year']+'"/>\
        <input type="button" value="-"\
        onclick="$(\'#position'+position['rank']+'\').remove();return false;"></p>\
        <textarea name="desc'+position['rank']+'" rows="8" cols="80">'+position['description']+'</textarea></div>'
      );
    }
  })
  flag = 0;
  countPos = position_id;
}
else countPos = 0;
  $('#document').ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
      event.preventDefault();
      if(countPos >= 9){
        alert("Maximum of nine positions entires exceeded");
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
