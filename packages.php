<?php

include 'config.php';

if (isset($_GET['id'])){
  $id=$_GET['id'];
  $delete=mysqli_query($connection,"DELETE FROM `packages` WHERE `id` = $id");
}


if(isset($_REQUEST["submit"])){
  $id = $_REQUEST["id"];
  $name = $_REQUEST["name"];
  $duration = $_REQUEST["duration"];
  $price = $_REQUEST["price"];

  $ins = "INSERT into packages(id,name,duration,price) VALUES ('$id','$name','$duration','$price')";
  $query1 = mysqli_query($connection, $ins);

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
  <form>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputEmail4">Package ID</label>
      <input type="text" name="id" class="form-control" id="inputEmail4" placeholder="ID">
    </div>
    <div class="form-group col-md-6">
      <label for="inputPassword4">Package Name</label>
      <input type="text" name="name" class="form-control" id="inputPassword4" placeholder="Name">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Duration</label>
    <input type="text" name="duration" class="form-control" id="inputAddress" placeholder="Duration">
  </div>
  <div class="form-group">
    <label for="inputAddress2">Price</label>
    <input type="text" name="price" class="form-control" id="inputAddress2" placeholder="Price">
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





<?php


$connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);
 
$query = "SELECT * FROM packages ORDER BY id DESC";
$result = mysqli_query($connection,$query);
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
      <td>Duration</td>
      <td>Price</td>
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
          <td>'.$row["duration"].'</td>
          <td>'.$row["price"].'</td>
          <td>
          <a href = "packages.php?id='.$row['id'].' " >Delete</a>
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
    $('packages').DataTable();
});

  </script>