<?php
include 'config.php';

$success_msg = '';
$error_msg = '';

if(isset($_POST["submit"])){
    $admin_id = mysqli_real_escape_string($connection, $_POST["admin_id"]);
    $mode = mysqli_real_escape_string($connection, $_POST["mode"]);
    $date = mysqli_real_escape_string($connection, $_POST["date"]);

    // Get the next available transaction ID
    $result = mysqli_query($connection, "SELECT IFNULL(MAX(transaction_id), 0) + 1 AS next_id FROM transaction");
    $row = mysqli_fetch_assoc($result);
    $next_id = $row['next_id'];

    $ins = "INSERT INTO transaction (transaction_id, admin_id, mode, date) VALUES ('$next_id', '$admin_id', '$mode', '$date')";
    
    if(mysqli_query($connection, $ins)) {
        $success_msg = "Transaction added successfully! Transaction ID: " . $next_id;
    } else {
        $error_msg = "Error: " . mysqli_error($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction - Gym Management System</title>
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
      <li class="nav-item">
      <a class="nav-link" href="index.html">Index</a>
      </li>
    </ul>
  </div>
</nav>
    <!-- nav bar end -->
       <!-- form start -->

  <form action="" method="post">
  <?php if(!empty($success_msg)): ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
  <?php endif; ?>
  <?php if(!empty($error_msg)): ?>
    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
  <?php endif; ?>
    <div class="form-group">
      <label>Transaction ID will be generated automatically</label>
      <input type="text" class="form-control" value="Auto-generated" disabled>
    </div>
    <div class="form-group col-md-6">
      <label for="inputPassword4">admin_id </label>
      <input type="text" name="admin_id" class="form-control" id="inputPassword4" placeholder="Admin_id">
    </div>
    <div class="form-group">
  <div class="form-group">
    <label for="inputAddress2">mode of payment</label>
    <input type="text" name="mode" class="form-control" id="inputAddress2" placeholder="cash/card/upi">
  </div>
  <div class="form-group">
    <label for="inputAddress">date</label>
    <input type="text" name="date" class="form-control" id="inputAddress" placeholder="yyyy-mm-dd">
  </div>
 

  <div class="form-group">
    <input type="submit" name="submit" class="form-control" id="inputAddress2" placeholder="Submit">
  </div>
</form>



  <!-- form end -->




<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>


