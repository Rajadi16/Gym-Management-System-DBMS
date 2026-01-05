<?php
include 'config.php';

$success_msg = '';
$error_msg = '';

// Handle package deletion
if (isset($_GET['id'])) {
  $id = mysqli_real_escape_string($connection, $_GET['id']);

  // Check if any members are using this package
  $check_query = "SELECT COUNT(*) as member_count FROM members WHERE package_id = $id";
  $result = mysqli_query($connection, $check_query);
  $row = mysqli_fetch_assoc($result);

  if ($row['member_count'] > 0) {
    $error_msg = "Cannot delete package: " . $row['member_count'] . " member(s) are using this package.";
  } else {
    $delete = mysqli_query($connection, "DELETE FROM `packages` WHERE `id` = $id");
    if ($delete) {
      $success_msg = "Package deleted successfully!";
    } else {
      $error_msg = "Error deleting package: " . mysqli_error($connection);
    }
  }
}

// Handle new package submission
if (isset($_REQUEST["submit"])) {
  $id = mysqli_real_escape_string($connection, $_REQUEST["id"]);
  $name = mysqli_real_escape_string($connection, $_REQUEST["name"]);
  $duration = mysqli_real_escape_string($connection, $_REQUEST["duration"]);
  $price = mysqli_real_escape_string($connection, $_REQUEST["price"]);

  // Check if package ID already exists
  $check = mysqli_query($connection, "SELECT id FROM packages WHERE id = '$id'");
  if (mysqli_num_rows($check) > 0) {
    $error_msg = "Error: A package with this ID already exists.";
  } else {
    $ins = "INSERT INTO packages(id, name, duration, price) VALUES ('$id', '$name', '$duration', '$price')";
    $query1 = mysqli_query($connection, $ins);

    if ($query1) {
      $success_msg = "Package added successfully!";
      $_POST = array(); // Clear form
    } else {
      $error_msg = "Error adding package: " . mysqli_error($connection);
    }
  }
}

// Get statistics
$total_packages_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM packages");
$total_packages = mysqli_fetch_assoc($total_packages_query)['total'];

$members_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM members");
$total_members = mysqli_fetch_assoc($members_query)['total'];

// Get all packages
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM members m WHERE m.package_id = p.id) as member_count 
          FROM packages p 
          ORDER BY p.id DESC";
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Packages - Gym Management System</title>
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
          <a class="nav-link active" href="packages.php"><i class="fas fa-box"></i> Packages</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="trainers.php"><i class="fas fa-user-tie"></i> Trainers</a>
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
      <h1><i class="fas fa-box-open"></i> Membership Packages</h1>
      <p>Create and manage gym membership plans and pricing</p>
    </div>

    <!-- Statistics -->
    <div class="stats-container fade-in">
      <div class="stat-card">
        <div class="stat-icon purple">
          <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-value"><?php echo $total_packages; ?></div>
        <div class="stat-label">Total Packages</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-value"><?php echo $total_members; ?></div>
        <div class="stat-label">Active Members</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-value">
          <?php
          $popular_query = mysqli_query($connection, "SELECT package_id, COUNT(*) as count FROM members GROUP BY package_id ORDER BY count DESC LIMIT 1");
          if (mysqli_num_rows($popular_query) > 0) {
            echo mysqli_fetch_assoc($popular_query)['count'];
          } else {
            echo "0";
          }
          ?>
        </div>
        <div class="stat-label">Most Popular Plan</div>
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

    <!-- Add Package Form -->
    <div class="form-card fade-in">
      <h3><i class="fas fa-plus-circle"></i> Add New Package</h3>
      <form method="POST" action="">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="id"><i class="fas fa-hashtag"></i> Package ID</label>
              <input type="number" name="id" id="id" class="form-control" placeholder="Enter unique ID" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="name"><i class="fas fa-tag"></i> Package Name</label>
              <input type="text" name="name" id="name" class="form-control" placeholder="e.g., Premium Monthly"
                required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="duration"><i class="fas fa-clock"></i> Duration</label>
              <input type="text" name="duration" id="duration" class="form-control"
                placeholder="e.g., 1 MONTH, 3 MONTHS" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="price"><i class="fas fa-rupee-sign"></i> Price</label>
              <input type="number" name="price" id="price" class="form-control" placeholder="Enter price" required
                min="0" step="0.01">
            </div>
          </div>
        </div>
        <button type="submit" name="submit" class="btn-submit">
          <i class="fas fa-save"></i> Add Package
        </button>
      </form>
    </div>

    <!-- Packages List -->
    <div class="data-section fade-in">
      <h3><i class="fas fa-list"></i> All Packages</h3>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Package Name</th>
              <th>Duration</th>
              <th>Price</th>
              <th>Members</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                  <td><strong>#<?php echo $row['id']; ?></strong></td>
                  <td>
                    <i class="fas fa-box" style="color: #667eea; margin-right: 0.5rem;"></i>
                    <?php echo $row['name']; ?>
                  </td>
                  <td>
                    <span class="badge badge-info">
                      <i class="fas fa-calendar-alt"></i> <?php echo $row['duration']; ?>
                    </span>
                  </td>
                  <td>
                    <strong style="color: #11998e; font-size: 1.1rem;">
                      ₹<?php echo number_format($row['price'], 2); ?>
                    </strong>
                  </td>
                  <td>
                    <?php if ($row['member_count'] > 0): ?>
                      <span class="badge badge-success">
                        <i class="fas fa-users"></i> <?php echo $row['member_count']; ?> members
                      </span>
                    <?php else: ?>
                      <span class="badge badge-secondary">
                        <i class="fas fa-user-slash"></i> No members
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="packages.php?id=<?php echo $row['id']; ?>" class="btn-delete"
                      onclick="return confirm('Are you sure you want to delete this package?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </td>
                </tr>
                <?php
              endwhile;
            else:
              ?>
              <tr>
                <td colspan="6">
                  <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No packages found. Add your first package above!</p>
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