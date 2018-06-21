<?php
session_start();
require_once "pdo.php";
require_once "util.php";

$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :xyz');
$stmt->execute(array(":xyz" =>$_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
  $_SESSION['error'] = 'Bad value for profile_id';
  header('Location: index.php');
}
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$position_id = sizeof($positions);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
$school_id = sizeof($schools);
echo ($school_id);
$flag_sch = 0;
if($schools !== false) $flag_sch = 1;
$flag = 0;
if($positions !== false) $flag = 1;


$firstName = htmlentities($row['first_name']);
$lastName = htmlentities($row['last_name']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$email = htmlentities($row['email']);

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

<h1>Profile Inforamtion</h1>



<input type="hidden" name="profile_id" value="<?=$row['profile_id'] ?>">
<p>First Name:
  <label for="firstName"><?= $firstName ?></label></p>
  <p>Last Name:
    <label for="lastName"><?= $lastName ?></label></p>
    <p>Email:
      <label for="email"><?= $email ?></label></p>
      <p>Headline:
        <label for="headline"><?= $headline ?></label></p>
        <p>Summary:
          <label for="summary"><?= $summary ?></label></p>
          <div id="edu_fields">
          </div>
          <div id="position_fields">
          </div>
  <p><a href="index.php">Done</a></p>


<script>

flag_sch = <?= htmlentities($flag_sch) ?>;
school_id = <?= htmlentities($school_id) ?>;
if(flag_sch == 1){
  schools = new Array();
  school = new Array();
  schools = <?php echo json_encode($schools); ?>;
  console.log(schools.length);

  $('#document').ready(function(){
    $('#edu_fields').append(
      'Education\
      <p><ul>'
    )
    console.log(school_id);
    for(i=0; i < school_id; i++){
      
      school = schools[i]
      $('#edu_fields').append(
        '<li>'+school['year']+': '+school['name']+'</li>'

      );
    }
    $('#edu_fields').append(
      '</p></ul>'
    );
  });
  flag_sch = 0;
}
flag = <?= htmlentities($flag) ?>;
position_id = <?= htmlentities($position_id) ?>;

if(flag == 1){
  positions = new Array();
  position = new Array();
  positions = <?php echo json_encode($positions); ?>;
  console.log(positions.length);

  $('#document').ready(function(){
    $('#position_fields').append(
      'Position\
      <p><ul>'
    )
    for(i=0; i < position_id; i++){
      position = positions[i]
      $('#position_fields').append(
        '<li>'+position['year']+': '+position['description']+'</li>'

      );
    }
    $('#position_fields').append(
      '</p></ul>'
    )
  })
  flag = 0;
}
</script>

</body>
</html>
