<?php
include 'config.php';

$success_msg = '';
$error_msg = '';

// Handle new transaction
if (isset($_POST["submit"])) {
  $member_id = mysqli_real_escape_string($connection, $_POST["member_id"]);
  $admin_id = mysqli_real_escape_string($connection, $_POST["admin_id"]);
  $mode = mysqli_real_escape_string($connection, $_POST["mode"]);
  $amount = mysqli_real_escape_string($connection, $_POST["amount"]);
  $date = date('Y-m-d'); // Auto-set to today's date

  // Get the next available transaction ID
  $result = mysqli_query($connection, "SELECT IFNULL(MAX(transaction_id), 1000) + 1 AS next_id FROM transaction");
  $row = mysqli_fetch_assoc($result);
  $next_id = $row['next_id'];

  $ins = "INSERT INTO transaction (transaction_id, admin_id, mode, date) VALUES ('$next_id', '$admin_id', '$mode [$amount]', '$date')";

  if (mysqli_query($connection, $ins)) {
    // Update member's transaction_id
    mysqli_query($connection, "UPDATE members SET transaction_id = '$next_id' WHERE id = '$member_id'");
    $success_msg = "Payment processed successfully! Transaction ID: #" . $next_id;
  } else {
    $error_msg = "Error: " . mysqli_error($connection);
  }
}

// Handle transaction deletion
if (isset($_GET['delete_id'])) {
  $delete_id = mysqli_real_escape_string($connection, $_GET['delete_id']);
  $delete = mysqli_query($connection, "DELETE FROM `transaction` WHERE `transaction_id` = '$delete_id'");
  if ($delete) {
    $success_msg = "Transaction deleted successfully!";
  }
}

// Get statistics
$total_revenue_query = mysqli_query($connection, "SELECT COUNT(*) as total_transactions FROM transaction");
$total_revenue_data = mysqli_fetch_assoc($total_revenue_query);
$total_transactions = $total_revenue_data['total_transactions'];

$recent_transactions_query = mysqli_query($connection, "SELECT COUNT(*) as recent FROM transaction WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$recent_data = mysqli_fetch_assoc($recent_transactions_query);
$recent_count = $recent_data['recent'];

// Get all transactions with member details
$query = "SELECT t.*, a.username, 
          (SELECT m.name FROM members m WHERE m.transaction_id = t.transaction_id LIMIT 1) as member_name,
          (SELECT m.id FROM members m WHERE m.transaction_id = t.transaction_id LIMIT 1) as member_id
          FROM transaction t 
          LEFT JOIN admin a ON t.admin_id = a.id 
          ORDER BY t.transaction_id DESC";
$transactions = mysqli_query($connection, $query);

// Get members for dropdown
$members_query = "SELECT m.id, m.name, p.name as package_name, p.price 
                  FROM members m 
                  LEFT JOIN packages p ON m.package_id = p.id 
                  ORDER BY m.name";
$members = mysqli_query($connection, $members_query);

// Get admins for dropdown
$admins_query = "SELECT id, username FROM admin ORDER BY username";
$admins = mysqli_query($connection, $admins_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Billing & Payments - Gym Management System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding-bottom: 50px;
    }

    /* Navbar Styling */
    .navbar {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 1rem 2rem;
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: #667eea !important;
    }

    .nav-link {
      color: #4a5568 !important;
      font-weight: 500;
      margin: 0 0.5rem;
      transition: all 0.3s ease;
      position: relative;
    }

    .nav-link:hover {
      color: #667eea !important;
      transform: translateY(-2px);
    }

    .nav-link.active {
      color: #667eea !important;
    }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 50%;
      transform: translateX(-50%);
      width: 30px;
      height: 3px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      border-radius: 2px;
    }

    /* Container */
    .main-container {
      max-width: 1400px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    /* Page Header */
    .page-header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .page-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }

    .page-header p {
      color: #718096;
      font-size: 1.1rem;
    }

    /* Statistics Cards */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 1rem;
    }

    .stat-icon.purple {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .stat-icon.green {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      color: white;
    }

    .stat-icon.orange {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #718096;
      font-size: 0.95rem;
      font-weight: 500;
    }

    /* Payment Form Card */
    .payment-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }

    .payment-card h3 {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .payment-card h3 i {
      color: #667eea;
    }

    /* Form Styling */
    .form-group label {
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }

    .form-control,
    .form-control:focus {
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f7fafc;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: white;
    }

    select.form-control {
      cursor: pointer;
    }

    /* Payment Method Buttons */
    .payment-methods {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .payment-method-btn {
      padding: 1rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      background: #f7fafc;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
      position: relative;
    }

    .payment-method-btn:hover {
      border-color: #667eea;
      background: rgba(102, 126, 234, 0.05);
    }

    .payment-method-btn input[type="radio"] {
      position: absolute;
      opacity: 0;
    }

    .payment-method-btn input[type="radio"]:checked+label {
      color: #667eea;
    }

    .payment-method-btn.active {
      border-color: #667eea;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }

    .payment-method-btn i {
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    .payment-method-btn label {
      font-weight: 600;
      margin: 0;
      cursor: pointer;
    }

    /* Submit Button */
    .btn-submit {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 1rem 2.5rem;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
      width: 100%;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    /* Alerts */
    .alert {
      border-radius: 12px;
      border: none;
      padding: 1rem 1.5rem;
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    .alert-success {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      color: white;
    }

    .alert-danger {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    /* Transaction History */
    .transaction-history {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .transaction-history h3 {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .transaction-history h3 i {
      color: #667eea;
    }

    /* Table Styling */
    .table-responsive {
      border-radius: 12px;
      overflow: hidden;
    }

    .table {
      margin-bottom: 0;
    }

    .table thead th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      padding: 1rem;
    }

    .table tbody tr {
      transition: all 0.3s ease;
      border-bottom: 1px solid #e2e8f0;
    }

    .table tbody tr:hover {
      background: rgba(102, 126, 234, 0.05);
      transform: scale(1.01);
    }

    .table tbody td {
      padding: 1rem;
      vertical-align: middle;
      color: #4a5568;
      font-weight: 500;
    }

    /* Badge Styling */
    .badge {
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.85rem;
    }

    .badge-cash {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      color: white;
    }

    .badge-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .badge-upi {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    /* Action Buttons */
    .btn-delete {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-delete:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .payment-methods {
        grid-template-columns: 1fr;
      }

      .page-header h1 {
        font-size: 2rem;
      }

      .stat-value {
        font-size: 2rem;
      }
    }

    /* Loading Animation */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in {
      animation: fadeIn 0.5s ease-out;
    }
  </style>
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
          <a class="nav-link" href="trainers.php"><i class="fas fa-user-tie"></i> Trainers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="members.php"><i class="fas fa-users"></i> Members</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="transaction.php"><i class="fas fa-credit-card"></i> Billing</a>
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
      <h1><i class="fas fa-wallet"></i> Billing & Payments</h1>
      <p>Manage transactions, process payments, and track revenue</p>
    </div>

    <!-- Statistics -->
    <div class="stats-container fade-in">
      <div class="stat-card">
        <div class="stat-icon purple">
          <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-value"><?php echo $total_transactions; ?></div>
        <div class="stat-label">Total Transactions</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-value"><?php echo $recent_count; ?></div>
        <div class="stat-label">Last 30 Days</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-value"><?php echo mysqli_num_rows($members); ?></div>
        <div class="stat-label">Active Members</div>
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

    <!-- Payment Form -->
    <div class="payment-card fade-in">
      <h3><i class="fas fa-money-check-alt"></i> Process New Payment</h3>
      <form action="" method="POST" id="paymentForm">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="member_id"><i class="fas fa-user"></i> Select Member</label>
              <select name="member_id" id="member_id" class="form-control" required>
                <option value="">-- Choose Member --</option>
                <?php
                mysqli_data_seek($members, 0); // Reset pointer
                while ($member = mysqli_fetch_assoc($members)):
                  ?>
                  <option value="<?php echo $member['id']; ?>" data-package="<?php echo $member['package_name']; ?>"
                    data-price="<?php echo $member['price']; ?>">
                    <?php echo $member['name']; ?> - <?php echo $member['package_name']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="admin_id"><i class="fas fa-user-shield"></i> Processed By</label>
              <select name="admin_id" id="admin_id" class="form-control" required>
                <option value="">-- Select Admin --</option>
                <?php while ($admin = mysqli_fetch_assoc($admins)): ?>
                  <option value="<?php echo $admin['id']; ?>">
                    <?php echo $admin['username']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label><i class="fas fa-rupee-sign"></i> Payment Amount</label>
          <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount" required
            min="1" step="0.01">
          <small class="text-muted" id="packageInfo"></small>
        </div>

        <div class="form-group">
          <label><i class="fas fa-credit-card"></i> Payment Method</label>
          <div class="payment-methods">
            <div class="payment-method-btn" onclick="selectPayment('cash', this)">
              <i class="fas fa-money-bill-wave" style="color: #38ef7d;"></i>
              <label>Cash</label>
            </div>
            <div class="payment-method-btn" onclick="selectPayment('card', this)">
              <i class="fas fa-credit-card" style="color: #667eea;"></i>
              <label>Card</label>
            </div>
            <div class="payment-method-btn" onclick="selectPayment('upi', this)">
              <i class="fas fa-mobile-alt" style="color: #f5576c;"></i>
              <label>UPI</label>
            </div>
          </div>
          <input type="hidden" name="mode" id="mode" required>
        </div>

        <button type="submit" name="submit" class="btn-submit">
          <i class="fas fa-check-circle"></i> Process Payment
        </button>
      </form>
    </div>

    <!-- Transaction History -->
    <div class="transaction-history fade-in">
      <h3><i class="fas fa-history"></i> Transaction History</h3>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Transaction ID</th>
              <th>Member</th>
              <th>Amount</th>
              <th>Payment Method</th>
              <th>Date</th>
              <th>Processed By</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (mysqli_num_rows($transactions) > 0):
              while ($row = mysqli_fetch_assoc($transactions)):
                // Extract amount from mode field
                preg_match('/\[(.*?)\]/', $row['mode'], $matches);
                $amount = isset($matches[1]) ? $matches[1] : 'N/A';
                $method = strtolower(trim(explode('[', $row['mode'])[0]));
                ?>
                <tr>
                  <td><strong>#<?php echo $row['transaction_id']; ?></strong></td>
                  <td>
                    <?php echo $row['member_name'] ? $row['member_name'] : '<em>N/A</em>'; ?>
                    <?php if ($row['member_id']): ?>
                      <br><small class="text-muted">ID: <?php echo $row['member_id']; ?></small>
                    <?php endif; ?>
                  </td>
                  <td><strong>₹<?php echo $amount; ?></strong></td>
                  <td>
                    <?php if (strpos($method, 'cash') !== false): ?>
                      <span class="badge badge-cash"><i class="fas fa-money-bill-wave"></i> Cash</span>
                    <?php elseif (strpos($method, 'card') !== false): ?>
                      <span class="badge badge-card"><i class="fas fa-credit-card"></i> Card</span>
                    <?php elseif (strpos($method, 'upi') !== false): ?>
                      <span class="badge badge-upi"><i class="fas fa-mobile-alt"></i> UPI</span>
                    <?php else: ?>
                      <span class="badge badge-secondary"><?php echo ucfirst($method); ?></span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                  <td><?php echo $row['username'] ? $row['username'] : 'N/A'; ?></td>
                  <td>
                    <a href="transaction.php?delete_id=<?php echo $row['transaction_id']; ?>" class="btn-delete"
                      onclick="return confirm('Are you sure you want to delete this transaction?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </td>
                </tr>
                <?php
              endwhile;
            else:
              ?>
              <tr>
                <td colspan="7" class="text-center" style="padding: 2rem;">
                  <i class="fas fa-inbox" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                  <p style="color: #718096; font-size: 1.1rem;">No transactions found</p>
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
    // Payment method selection
    function selectPayment(method, element) {
      // Remove active class from all buttons
      document.querySelectorAll('.payment-method-btn').forEach(btn => {
        btn.classList.remove('active');
      });

      // Add active class to selected button
      element.classList.add('active');

      // Set hidden input value
      document.getElementById('mode').value = method;
    }

    // Auto-fill amount when member is selected
    document.getElementById('member_id').addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const price = selectedOption.getAttribute('data-price');
      const packageName = selectedOption.getAttribute('data-package');

      if (price && price !== 'null') {
        document.getElementById('amount').value = price;
        document.getElementById('packageInfo').textContent = 'Package: ' + packageName + ' - ₹' + price;
      } else {
        document.getElementById('amount').value = '';
        document.getElementById('packageInfo').textContent = '';
      }
    });

    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function (e) {
      const mode = document.getElementById('mode').value;
      if (!mode) {
        e.preventDefault();
        alert('Please select a payment method');
        return false;
      }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
      $('.alert').fadeOut('slow');
    }, 5000);
  </script>
</body>

</html>