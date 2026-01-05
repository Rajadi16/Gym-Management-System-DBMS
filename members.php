<?php
include 'config.php';

$success_msg = '';
$error_msg = '';

// Handle member deletion
if (isset($_GET['id'])) {
  $id = mysqli_real_escape_string($connection, $_GET['id']);
  $delete = mysqli_query($connection, "DELETE FROM `members` WHERE `id` = '$id'");
  if ($delete) {
    $success_msg = "Member deleted successfully!";
  } else {
    $error_msg = "Error deleting member: " . mysqli_error($connection);
  }
}

// Handle new member submission
if (isset($_REQUEST["submit"])) {
  $id = mysqli_real_escape_string($connection, $_REQUEST["id"]);
  $name = mysqli_real_escape_string($connection, $_REQUEST["name"]);
  $gender = mysqli_real_escape_string($connection, $_REQUEST["gender"]);
  $dob = mysqli_real_escape_string($connection, $_REQUEST["dob"]);
  $trainer_id = mysqli_real_escape_string($connection, $_REQUEST["trainer_id"]);
  $package_id = mysqli_real_escape_string($connection, $_REQUEST["package_id"]);
  $batch = mysqli_real_escape_string($connection, $_REQUEST["batch"]);

  // Validate required fields
  if (empty($name) || empty($trainer_id) || empty($package_id)) {
    $error_msg = "Please fill in all required fields.";
  } else {
    $ins = "INSERT INTO members(id, name, gender, dob, trainer_id, package_id, batch, transaction_id) 
                VALUES ('$id', '$name', '$gender', '$dob', '$trainer_id', '$package_id', '$batch', 0)";
    $query1 = mysqli_query($connection, $ins);

    if ($query1) {
      $success_msg = "Member registered successfully! Member ID: #" . $id;
      $_POST = array(); // Clear form
    } else {
      $error_msg = "Error: " . mysqli_error($connection);
    }
  }
}

// Function to generate a unique 4-digit member ID
function generateMemberID($connection)
{
  $unique = false;
  $maxAttempts = 100;
  $attempts = 0;

  while (!$unique && $attempts < $maxAttempts) {
    $newID = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $check = mysqli_query($connection, "SELECT id FROM members WHERE id = '$newID'");
    if (mysqli_num_rows($check) === 0) {
      $unique = true;
    }
    $attempts++;
  }

  return $unique ? $newID : false;
}

// Get statistics
$total_members_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM members");
$total_members = mysqli_fetch_assoc($total_members_query)['total'];

$morning_batch_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM members WHERE batch = 'MORNING'");
$morning_batch = mysqli_fetch_assoc($morning_batch_query)['total'];

$evening_batch_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM members WHERE batch = 'EVENING'");
$evening_batch = mysqli_fetch_assoc($evening_batch_query)['total'];

// Get all members with trainer and package details
$query = "SELECT m.*, t.name as trainer_name, p.name as package_name, p.price as package_price 
          FROM members m 
          LEFT JOIN trainers t ON m.trainer_id = t.id 
          LEFT JOIN packages p ON m.package_id = p.id 
          ORDER BY m.id DESC";
$result = mysqli_query($connection, $query);

// Get trainers for dropdown
$trainers_query = "SELECT id, name FROM trainers ORDER BY name";
$trainers_result = mysqli_query($connection, $trainers_query);

// Get packages for dropdown
$packages_query = "SELECT * FROM packages ORDER BY name";
$packages_result = mysqli_query($connection, $packages_query);

// Generate new member ID
$newMemberID = generateMemberID($connection);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Members - Gym Management System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="packages.php">
      <i class="fas fa-dumbbell"></i> CHOLE BHATURE FITNESS
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
          <a class="nav-link" href="trainers.php"><i class="fas fa-user-tie"></i> Trainers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="members.php"><i class="fas fa-users"></i> Members</a>
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
      <h1><i class="fas fa-id-card"></i> Gym Members</h1>
      <p>Register and manage gym member registrations</p>
    </div>

    <!-- Statistics -->
    <div class="stats-container fade-in">
      <div class="stat-card">
        <div class="stat-icon purple">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-value"><?php echo $total_members; ?></div>
        <div class="stat-label">Total Members</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <i class="fas fa-sun"></i>
        </div>
        <div class="stat-value"><?php echo $morning_batch; ?></div>
        <div class="stat-label">Morning Batch</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">
          <i class="fas fa-moon"></i>
        </div>
        <div class="stat-value"><?php echo $evening_batch; ?></div>
        <div class="stat-label">Evening Batch</div>
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

    <!-- Add Member Form -->
    <div class="form-card fade-in">
      <h3><i class="fas fa-user-plus"></i> Register New Member</h3>
      <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $newMemberID; ?>">

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label><i class="fas fa-hashtag"></i> Member ID</label>
              <div class="form-control bg-light" style="font-weight: 700; color: #667eea;">
                #<?php echo $newMemberID; ?>
              </div>
              <small class="text-muted">Auto-generated unique ID</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="name"><i class="fas fa-user"></i> Member Name *</label>
              <input type="text" name="name" id="name" class="form-control" placeholder="Enter full name" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="gender"><i class="fas fa-venus-mars"></i> Gender</label>
              <select name="gender" id="gender" class="form-control">
                <option value="">-- Select Gender --</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="dob"><i class="fas fa-calendar"></i> Date of Birth</label>
              <input type="date" name="dob" id="dob" class="form-control">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="batch"><i class="fas fa-clock"></i> Batch *</label>
              <select name="batch" id="batch" class="form-control" required>
                <option value="">-- Select Batch --</option>
                <option value="MORNING">Morning</option>
                <option value="EVENING">Evening</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="trainer_id"><i class="fas fa-user-tie"></i> Assign Trainer *</label>
              <select name="trainer_id" id="trainer_id" class="form-control" required>
                <option value="">-- Select Trainer --</option>
                <?php
                if (mysqli_num_rows($trainers_result) > 0) {
                  while ($trainer = mysqli_fetch_assoc($trainers_result)) {
                    echo '<option value="' . $trainer['id'] . '">' . $trainer['name'] . '</option>';
                  }
                } else {
                  echo '<option value="" disabled>No trainers available</option>';
                }
                ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="package_id"><i class="fas fa-box"></i> Select Package *</label>
              <select name="package_id" id="package_id" class="form-control" required>
                <option value="">-- Select Package --</option>
                <?php
                if (mysqli_num_rows($packages_result) > 0) {
                  while ($package = mysqli_fetch_assoc($packages_result)) {
                    echo '<option value="' . $package['id'] . '">' . $package['name'] . ' - ₹' . $package['price'] . '</option>';
                  }
                } else {
                  echo '<option value="" disabled>No packages available</option>';
                }
                ?>
              </select>
            </div>
          </div>
        </div>

        <button type="submit" name="submit" class="btn-submit">
          <i class="fas fa-save"></i> Register Member
        </button>
      </form>
    </div>

    <!-- Members List -->
    <div class="data-section fade-in">
      <h3><i class="fas fa-list"></i> All Members</h3>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Gender</th>
              <th>Date of Birth</th>
              <th>Trainer</th>
              <th>Package</th>
              <th>Batch</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)):
                $age = $row['dob'] ? date_diff(date_create($row['dob']), date_create('today'))->y : 'N/A';
                ?>
                <tr>
                  <td><strong>#<?php echo $row['id']; ?></strong></td>
                  <td>
                    <i class="fas fa-user-circle" style="color: #667eea; margin-right: 0.5rem;"></i>
                    <strong><?php echo $row['name']; ?></strong>
                  </td>
                  <td>
                    <?php if ($row['gender'] == 'M'): ?>
                      <span class="badge badge-info">
                        <i class="fas fa-male"></i> Male
                      </span>
                    <?php elseif ($row['gender'] == 'F'): ?>
                      <span class="badge badge-warning">
                        <i class="fas fa-female"></i> Female
                      </span>
                    <?php else: ?>
                      <span class="badge badge-secondary">N/A</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo $row['dob'] ? date('M d, Y', strtotime($row['dob'])) : 'N/A'; ?>
                    <?php if ($age !== 'N/A'): ?>
                      <br><small class="text-muted"><?php echo $age; ?> years old</small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <i class="fas fa-user-tie" style="color: #11998e; margin-right: 0.3rem;"></i>
                    <?php echo $row['trainer_name'] ? $row['trainer_name'] : '<em>Not assigned</em>'; ?>
                  </td>
                  <td>
                    <strong style="color: #667eea;">
                      <?php echo $row['package_name'] ? $row['package_name'] : '<em>None</em>'; ?>
                    </strong>
                    <?php if ($row['package_price']): ?>
                      <br><small class="text-muted">₹<?php echo $row['package_price']; ?></small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($row['batch'] == 'MORNING'): ?>
                      <span class="badge badge-success">
                        <i class="fas fa-sun"></i> Morning
                      </span>
                    <?php elseif ($row['batch'] == 'EVENING'): ?>
                      <span class="badge badge-primary">
                        <i class="fas fa-moon"></i> Evening
                      </span>
                    <?php else: ?>
                      <span class="badge badge-secondary">N/A</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="members.php?id=<?php echo $row['id']; ?>" class="btn-delete"
                      onclick="return confirm('Are you sure you want to delete this member?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </td>
                </tr>
                <?php
              endwhile;
            else:
              ?>
              <tr>
                <td colspan="8">
                  <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <p>No members found. Register your first member above!</p>
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
    setTimeout(function () {
      $('.alert').fadeOut('slow');
    }, 5000);
  </script>
</body>

</html>