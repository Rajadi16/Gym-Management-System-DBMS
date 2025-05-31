<?php

include 'config.php';
$message = '';
$messageType = '';

if (isset($_GET['id'])){
    $id = mysqli_real_escape_string($connection, $_GET['id']);
    
    // First, check if there are any members assigned to this trainer
    $check_query = "SELECT COUNT(*) as member_count FROM members WHERE trainer_id = $id";
    $result = mysqli_query($connection, $check_query);
    $row = mysqli_fetch_assoc($result);
    
    if($row['member_count'] > 0) {
        // If members exist, show an error message
        $message = "Cannot delete trainer: There are members assigned to this trainer. Please reassign or delete those members first.";
        $messageType = 'error';
    } else {
        // If no members are assigned, proceed with deletion
        $delete = mysqli_query($connection, "DELETE FROM `trainers` WHERE `id` = $id");
        if($delete) {
            $message = "Trainer deleted successfully.";
            $messageType = 'success';
        } else {
            $message = "Error deleting trainer: " . mysqli_error($connection);
            $messageType = 'error';
        }
    }
}

if(isset($_REQUEST["submit"])){
    $id = mysqli_real_escape_string($connection, $_REQUEST["id"]);
    $name = mysqli_real_escape_string($connection, $_REQUEST["name"]);
    $dob = mysqli_real_escape_string($connection, $_REQUEST["dob"]);
    $gender = mysqli_real_escape_string($connection, $_REQUEST["gender"]);
    $experience = mysqli_real_escape_string($connection, $_REQUEST["experience"]);

    // Check if trainer with this ID already exists
    $check = mysqli_query($connection, "SELECT id FROM trainers WHERE id = '$id'");
    if(mysqli_num_rows($check) > 0) {
        $message = "Error: A trainer with this ID already exists.";
        $messageType = 'error';
    } else {
        $ins = "INSERT INTO trainers(id, name, dob, gender, experience) 
                VALUES ('$id', '$name', '$dob', '$gender', '$experience')";
        $query1 = mysqli_query($connection, $ins);
        
        if($query1) {
            $message = "Trainer added successfully!";
            $messageType = 'success';
            // Clear form after successful submission
            $_POST = array();
        } else {
            $message = "Error adding trainer: " . mysqli_error($connection);
            $messageType = 'error';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
     <!-- nav bar start -->
     <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="admin-login.php"><img src="img/mylogo.png" alt="logo"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=nsfitness">Gym Management System</a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="packages.php">Packages</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="trainers.php">Trainers</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="members.php">Members</a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="transaction.php">Billing</a>
      </li>
    </ul>
  </div>
</nav>
    <!-- nav bar end -->

    <!-- form start -->

  <?php if(!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
      <?php echo $message; ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  <?php endif; ?>
  <form method="POST" action=""
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Trainer ID</label>
      <input type="text" name="id" class="form-control" id="inputEmail4" placeholder="ID">
    </div>
    <div class="form-group col-md-6">
      <label for="inputPassword4">Trainer Name</label>
      <input type="text" name="name" class="form-control" id="inputPassword4" placeholder="Name">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Date of birth</label>
    <input type="text" name="dob" class="form-control" id="inputAddress" placeholder="yyyy-mm-dd">
  </div>
  <div class="form-group">
    <label for="inputAddress2">Gender</label>
    <input type="text" name="gender" class="form-control" id="inputAddress2" placeholder="Gender(M/F)">
  </div>
  <div class="form-group">
    <label for="inputAddress2">Experience</label>
    <input type="text" name="experience" class="form-control" id="inputAddress2" placeholder="Experience">
  </div>
  <div class="form-group">
    <input type="submit" name="submit" class="btn btn-primary" value="Add Trainer">
  </div>
</form>



  <!-- form end -->

    



<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>





<?php


$query = "SELECT * FROM trainers ORDER BY id DESC";
$result = mysqli_query($connection,$query);
if(!$result) {
    die("Error in query: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<!-- Data table-->

<table id = "packages" class="table table-striped table-bordered">
  <thead>
    <tr>
      <td>id</td>
      <td>Name</td>
      <td>Date of birth</td>
      <td>Gender</td>
      <td>Experience</td>
      <td>Delete</td>
    </tr>
  </thead>
    <?php
    while($row = mysqli_fetch_array($result))
    {
      echo '
      <tr> 
          <td>'.$row["id"].'</td>
          <td>'.$row["name"].'</td>
          <td>'.$row["dob"].'</td>
          <td>'.$row["gender"].'</td>
          <td>'.$row["experience"].'</td>
          <td>
          <a href="trainers.php?id='.$row['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this trainer?\')">Delete</a>
        </td>
      </tr>
      ';
    }
    
    ?>
</table>
<!-- Data table-->


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

<script>
$(document).ready(function(){
    $('trainers').DataTable();
});

  </script>