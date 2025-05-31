<?php

include 'config.php';

if (isset($_GET['id'])){
  $id=$_GET['id'];
  $delete=mysqli_query($connection,"DELETE FROM `members` WHERE `id` = $id");
}

if(isset($_REQUEST["submit"])){
  $id = mysqli_real_escape_string($connection, $_REQUEST["id"]);
  $name = mysqli_real_escape_string($connection, $_REQUEST["name"]);
  $gender = mysqli_real_escape_string($connection, $_REQUEST["gender"]);
  $dob = mysqli_real_escape_string($connection, $_REQUEST["dob"]);
  $trainer_id = mysqli_real_escape_string($connection, $_REQUEST["trainer_id"]);
  $package_id = mysqli_real_escape_string($connection, $_REQUEST["package_id"]);
  $batch = mysqli_real_escape_string($connection, $_REQUEST["batch"]);

  // Validate required fields
  if(empty($name) || empty($trainer_id) || empty($package_id)) {
    $message = "Please fill in all required fields.";
    $messageType = "error";
  } else {

    $ins = "INSERT INTO members(id, name, gender, dob, trainer_id, package_id, batch) 
            VALUES ('$id', '$name', '$gender', '$dob', '$trainer_id', '$package_id', '$batch')";
    $query1 = mysqli_query($connection, $ins);
    
    if($query1) {
      $message = "Member registered successfully!";
      $messageType = "success";
      // Clear form
      $_POST = array();
    } else {
      $message = "Error: " . mysqli_error($connection);
      $messageType = "error";
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

  <form method="POST" action="">
  <?php
  // Function to generate a unique 4-digit member ID
  function generateMemberID($connection) {
    $unique = false;
    $maxAttempts = 100; // Prevent infinite loops
    $attempts = 0;
    
    while (!$unique && $attempts < $maxAttempts) {
      // Generate a random 4-digit number
      $newID = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
      
      // Check if ID already exists
      $check = mysqli_query($connection, "SELECT id FROM members WHERE id = '$newID'");
      if (mysqli_num_rows($check) === 0) {
        $unique = true;
      }
      $attempts++;
    }
    
    return $unique ? $newID : false;
  }
  
  // Generate a new member ID
  $newMemberID = generateMemberID($connection);
  ?>
  
  <input type="hidden" name="id" value="<?php echo $newMemberID; ?>">
  
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Member ID</label>
      <div class="form-control bg-light"><?php echo $newMemberID; ?></div>
      <small class="form-text text-muted">Auto-generated Member ID</small>
    </div>
    <div class="form-group col-md-6">
      <label for="name">Member Name</label>
      <input type="text" name="name" class="form-control" id="name" placeholder="Enter full name" required>
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Gender</label>
    <input type="text" name="gender" class="form-control" id="inputAddress" placeholder="M/F">
  </div>
  <div class="form-group">
    <label for="inputAddress2">Date of Birth</label>
    <input type="text" name="dob" class="form-control" id="inputAddress2" placeholder="yyyy-mm-dd">
  </div>
  <div class="form-group">
    <label for="trainer_select">Select Trainer</label>
    <select name="trainer_id" class="form-control" id="trainer_select" required>
      <option value="">-- Select Trainer --</option>
      <?php
      $trainers_query = "SELECT id, name FROM trainers ORDER BY name";
      $trainers_result = mysqli_query($connection, $trainers_query);
      if(mysqli_num_rows($trainers_result) > 0) {
        while($trainer = mysqli_fetch_assoc($trainers_result)) {
          echo '<option value="'.$trainer['id'].'">'.$trainer['name'].'</option>';
        }
      } else {
        echo '<option value="" disabled>No trainers available. Please add trainers first.</option>';
      }
      ?>
    </select>
  </div>
  <div class="form-group">
    <label for="package_id">Select Package</label>
    <select name="package_id" class="form-control" id="package_id" required>
      <option value="">-- Select Package --</option>
      <?php
      $packages_query = "SELECT * FROM packages ORDER BY name";
      $packages_result = mysqli_query($connection, $packages_query);
      if(mysqli_num_rows($packages_result) > 0) {
        while($row = mysqli_fetch_assoc($packages_result)) {
          echo '<option value="'.$row['id'].'">'.$row['name'].' - $'.$row['amount'].'</option>';
        }
      } else {
        echo '<option value="" disabled>No packages available. Please add packages first.</option>';
      }
      ?>
    </select>
  </div>
  <div class="form-group">
    <label for="batch">Select Batch</label>
    <select name="batch" class="form-control" id="batch" required>
      <option value="">-- Select Batch --</option>
      <option value="MORNING">Morning</option>
      <option value="EVENING">Evening</option>
    </select>
  </div>

  <div class="form-group">
    <input type="submit" name="submit" class="btn btn-primary" value="Register Member">
  </div>
</form>



  <!-- form end -->




<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>






<?php
// Function to get trainer name by ID
function getTrainerName($connection, $trainer_id) {
    $query = "SELECT name FROM trainers WHERE id = '$trainer_id' LIMIT 1";
    $result = mysqli_query($connection, $query);
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    return 'N/A';
}

// Function to get package name by ID
function getPackageName($connection, $package_id) {
    $query = "SELECT name FROM packages WHERE id = '$package_id' LIMIT 1";
    $result = mysqli_query($connection, $query);
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    return 'N/A';
}

$query = "SELECT * FROM members ORDER BY id DESC";
$result = mysqli_query($connection, $query);
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

<table id="members" class="table table-striped table-bordered">
  <thead class="thead-dark">
    <tr>
      <th>ID</th>
      <th>Member Name</th>
      <th>Gender</th>
      <th>Date of Birth</th>
      <th>Trainer</th>
      <th>Package</th>
      <th>Batch</th>
      <th>Actions</th>
    </tr>
  </thead>
    <?php
    while($row = mysqli_fetch_array($result))
    {
      echo '
      <tr> 
          <td>'.$row["id"].'</td>
          <td>'.$row["name"].'</td>
          <td>'.strtoupper($row["gender"]).'</td>
          <td>'.date('M d, Y', strtotime($row["dob"])).'</td>
          <td>'.getTrainerName($connection, $row["trainer_id"]).'</td>
          <td>'.getPackageName($connection, $row["package_id"]).'</td>
          <td>'.ucfirst(strtolower($row["batch"])).'</td>
          <td>
            <a href="members.php?id='.$row['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this member?\')">Delete</a>
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
    $('members').DataTable();
});



  </script>