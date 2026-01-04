<?php
include 'config.php';

$success_msg = '';
$error_msg = '';

// Handle trainer deletion
if (isset($_GET['id'])) {
  $id = mysqli_real_escape_string($connection, $_GET['id']);

  // Check if there are any members assigned to this trainer
  $check_query = "SELECT COUNT(*) as member_count FROM members WHERE trainer_id = '$id'";
  $result = mysqli_query($connection, $check_query);
  $row = mysqli_fetch_assoc($result);

  if ($row['member_count'] > 0) {
    $error_msg = "Cannot delete trainer: " . $row['member_count'] . " member(s) are assigned to this trainer.";
  } else {
    $delete = mysqli_query($connection, "DELETE FROM `trainers` WHERE `id` = '$id'");
    if ($delete) {
      $success_msg = "Trainer deleted successfully!";
    } else {
      $error_msg = "Error deleting trainer: " . mysqli_error($connection);
    }
  }
}

// Handle new trainer submission
if (isset($_REQUEST["submit"])) {
  $id = mysqli_real_escape_string($connection, $_REQUEST["id"]);
  $name = mysqli_real_escape_string($connection, $_REQUEST["name"]);
  $dob = mysqli_real_escape_string($connection, $_REQUEST["dob"]);
  $gender = mysqli_real_escape_string($connection, $_REQUEST["gender"]);
  $experience = mysqli_real_escape_string($connection, $_REQUEST["experience"]);

  // Check if trainer with this ID already exists
  $check = mysqli_query($connection, "SELECT id FROM trainers WHERE id = '$id'");
  if (mysqli_num_rows($check) > 0) {
    $error_msg = "Error: A trainer with this ID already exists.";
  } else {
    $ins = "INSERT INTO trainers(id, name, dob, gender, experience) 
                VALUES ('$id', '$name', '$dob', '$gender', '$experience')";
    $query1 = mysqli_query($connection, $ins);

    if ($query1) {
      $success_msg = "Trainer added successfully!";
      $_POST = array(); // Clear form
    } else {
      $error_msg = "Error adding trainer: " . mysqli_error($connection);
    }
  }
}

// Get statistics
$total_trainers_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM trainers");
$total_trainers = mysqli_fetch_assoc($total_trainers_query)['total'];

$male_trainers_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM trainers WHERE gender = 'M'");
$male_trainers = mysqli_fetch_assoc($male_trainers_query)['total'];

$female_trainers_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM trainers WHERE gender = 'F'");
$female_trainers = mysqli_fetch_assoc($female_trainers_query)['total'];

// Get all trainers with member count
$query = "SELECT t.*, 
          (SELECT COUNT(*) FROM members m WHERE m.trainer_id = t.id) as member_count 
          FROM trainers t 
          ORDER BY t.id DESC";
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers - Gym Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="admin-login.php">
            <i class="fas fa-dumbbell"></i> NS FITNESS
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="packages.php"><i class="fas fa-box"></i> Packages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="trainers.php"><i class="fas fa-user-tie"></i> Trainers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="members.php"><i class="fas fa-users"></i> Members</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="transaction.php"><i class="fas fa-credit-card"></i> Billing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.html"><i class="fas fa-home"></i> Home</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <h1><i class="fas fa-user-tie"></i> Gym Trainers</h1>
            <p>Manage your fitness trainers and their assignments</p>
        </div>

        <!-- Statistics -->
        <div class="stats-container fade-in">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $total_trainers; ?></div>
                <div class="stat-label">Total Trainers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-male"></i>
                </div>
                <div class="stat-value"><?php echo $male_trainers; ?></div>
                <div class="stat-label">Male Trainers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-female"></i>
                </div>
                <div class="stat-value"><?php echo $female_trainers; ?></div>
                <div class="stat-label">Female Trainers</div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (!empty($success_msg)): ?>
              <div class="alert alert-success fade-in">
                  <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
              </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
              <div class="alert alert-danger fade-in">
                  <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
              </div>
        <?php endif; ?>

        <!-- Add Trainer Form -->
        <div class="form-card fade-in">
            <h3><i class="fas fa-user-plus"></i> Add New Trainer</h3>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id"><i class="fas fa-hashtag"></i> Trainer ID</label>
                            <input type="text" name="id" id="id" class="form-control" placeholder="Enter unique ID" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Trainer Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter full name" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dob"><i class="fas fa-calendar"></i> Date of Birth</label>
                            <input type="date" name="dob" id="dob" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="gender"><i class="fas fa-venus-mars"></i> Gender</label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value="">-- Select Gender --</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="experience"><i class="fas fa-award"></i> Experience</label>
                            <input type="text" name="experience" id="experience" class="form-control" placeholder="e.g., 5 YEARS" required>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Add Trainer
                </button>
            </form>
        </div>

        <!-- Trainers List -->
        <div class="data-section fade-in">
            <h3><i class="fas fa-list"></i> All Trainers</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Experience</th>
                            <th>Assigned Members</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0):
                          while ($row = mysqli_fetch_assoc($result)):
                            $age = date_diff(date_create($row['dob']), date_create('today'))->y;
                            ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td>
                                        <i class="fas fa-user-circle" style="color: #667eea; margin-right: 0.5rem;"></i>
                                        <strong><?php echo $row['name']; ?></strong>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($row['dob'])); ?>
                                        <br><small class="text-muted"><?php echo $age; ?> years old</small>
                                    </td>
                                    <td>
                                        <?php if ($row['gender'] == 'M'): ?>
                                              <span class="badge badge-info">
                                                  <i class="fas fa-male"></i> Male
                                              </span>
                                        <?php else: ?>
                                              <span class="badge badge-warning">
                                                  <i class="fas fa-female"></i> Female
                                              </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-award"></i> <?php echo $row['experience']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['member_count'] > 0): ?>
                                              <span class="badge badge-primary">
                                                  <i class="fas fa-users"></i> <?php echo $row['member_count']; ?> members
                                              </span>
                                        <?php else: ?>
                                              <span class="badge badge-secondary">
                                                  <i class="fas fa-user-slash"></i> No members
                                              </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="trainers.php?id=<?php echo $row['id']; ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this trainer?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                          <?php
                          endwhile;
                        else:
                          ?>
                              <tr>
                                  <td colspan="7">
                                      <div class="empty-state">
                                          <i class="fas fa-user-slash"></i>
                                          <p>No trainers found. Add your first trainer above!</p>
                                      </div>
                                  </td>
                              </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>