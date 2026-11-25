<?php
session_start();

// Security / Anti-Cache Headers to prevent back button profile view after logout
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Handle Logout
if (isset($_POST['logout']) || isset($_GET['logout'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: login.php');
    exit();
}

// 1. Check if User is Logged In
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header('Location: login.php?error=login_required');
    exit();
}

// 2. Redirect Admin to Admin Panel
if (strtolower($_SESSION['email']) == "admin@gmail.com") {
    header('Location: Admin.php');
    exit();
}

// 3. Connect to MySQL Database
require_once 'config.php';
$email = $_SESSION['email'];

// Fetch Current Logged-in User Data
$user_id     = 1;
$full_name   = "Valued Customer";
$telephone   = "";
$user_avatar = 'img/testimonial/1.jpg';

$user_query = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'");
if ($user_query && mysqli_num_rows($user_query) > 0) {
    $user_rec    = mysqli_fetch_assoc($user_query);
    $user_id     = $user_rec['User_ID'];
    $full_name   = $user_rec['Full_Name'];
    $telephone   = isset($user_rec['Telephone']) ? $user_rec['Telephone'] : '';
    $user_address = isset($user_rec['Address']) ? $user_rec['Address'] : '';
    $user_avatar = !empty($user_rec['Image']) ? $user_rec['Image'] : 'img/testimonial/1.jpg';
} else {
    $user_id     = 1;
    $full_name   = "Valued Customer";
    $telephone   = "";
    $user_address = "";
    $user_avatar = 'img/testimonial/1.jpg';
}

$action_msg = "";

if (isset($_GET['order']) && $_GET['order'] === 'success') {
    $action_msg = "<div class='alert alert-success alert-dismissible fade show m-3' role='alert'><i class='fas fa-check-circle me-2'></i><strong>Order Placed Successfully!</strong> Your order has been recorded and is now visible in your profile order history below.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// Handle Cancel / Delete Order
if (isset($_POST['cancel_order'])) {
    $cancel_oid = intval($_POST['order_id']);
    $del_query  = mysqli_query($con, "DELETE FROM orders WHERE Order_ID='$cancel_oid'");
    if ($del_query) {
        $action_msg = "<div class='alert alert-warning alert-dismissible fade show m-3' role='alert'><i class='fas fa-trash-alt me-2'></i>Order #$cancel_oid has been cancelled and removed successfully.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Handle Profile Updates
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($con, $_POST['full_name']);
    $telephone = mysqli_real_escape_string($con, $_POST['telephone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    
    // Ensure Address and Delivery_Address columns exist in users table
    $u_chk = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'Address'");
    if ($u_chk && mysqli_num_rows($u_chk) == 0) {
        mysqli_query($con, "ALTER TABLE users ADD COLUMN Address VARCHAR(255) NULL");
    }
    $del_chk = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'Delivery_Address'");
    if ($del_chk && mysqli_num_rows($del_chk) == 0) {
        mysqli_query($con, "ALTER TABLE users ADD COLUMN Delivery_Address VARCHAR(255) NULL");
    }

    $update_fields = "Full_Name='$name', Telephone='$telephone', Address='$address', Delivery_Address='$address'";
    
    // Process profile picture file upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $file_name = $_FILES['profile_pic']['name'];
        $file_tmp  = $_FILES['profile_pic']['tmp_name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if (in_array($ext, $allowed)) {
            $new_name    = "profile_" . time() . "_" . rand(100, 999) . "." . $ext;
            $upload_dir  = "img/profile/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $target_path = $upload_dir . $new_name;
            if (move_uploaded_file($file_tmp, $target_path)) {
                $update_fields .= ", Image='$target_path'";
            }
        }
    }
    
    $u_stmt = mysqli_query($con, "UPDATE users SET $update_fields WHERE Email='$email'");
    if ($u_stmt) {
        $action_msg = "<div class='alert alert-success alert-dismissible fade show m-3' role='alert'><i class='fas fa-check-circle me-2'></i>Profile details updated successfully!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Handle Password Updates
if (isset($_POST['update_password'])) {
    $new_pass = mysqli_real_escape_string($con, $_POST['new_password']);
    $p_stmt = mysqli_query($con, "UPDATE users SET Password='$new_pass' WHERE Email='$email'");
    if ($p_stmt) {
        $action_msg = "<div class='alert alert-success alert-dismissible fade show m-3' role='alert'><i class='fas fa-check-circle me-2'></i>Password updated successfully!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}



// Detect Price Column in orders table
$price_col = 'Total_Price_(LKR)';
$c_check = mysqli_query($con, "SHOW COLUMNS FROM orders");
if ($c_check) {
    while ($cr = mysqli_fetch_assoc($c_check)) {
        if (in_array($cr['Field'], ['Total_Price_(LKR)', 'Total_Price_LKR', 'Total_Price', 'Total_Amount', 'Price_LKR', 'Price'])) {
            $price_col = $cr['Field'];
            break;
        }
    }
}

// Query User Orders from database
$user_orders = mysqli_query($con, "SELECT o.Order_ID, o.Product_ID, o.Quantity, o.`$price_col` AS Total_Price, o.Status, o.Order_Date, p.Product_Name, p.Image, p.Price_LKR 
FROM orders o LEFT JOIN products p ON o.Product_ID = p.Product_ID 
WHERE o.User_ID = '$user_id' ORDER BY o.Order_Date DESC, o.Order_ID DESC");
$order_count = $user_orders ? mysqli_num_rows($user_orders) : 0;
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="GlamHandy Team">
      <meta name="description" content="My Account - GlamHandy Arts & Crafts Store">
      <title>My Profile - GlamHandy Arts & Crafts</title>
      <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
      <!-- Bootstrap 5.3 -->
      <link href="css/bootstrap.min.css" rel="stylesheet"/>
      <!-- Font Awesome Icons -->
      <link rel="stylesheet" href="css/all.min.css"/>
      <!-- Style CSS -->
      <link rel="stylesheet" href="css/style.css"/>
      <style>
         .profile-nav-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 12px 18px;
            border: none;
            background: transparent;
            color: #555;
            font-weight: 500;
            border-radius: 10px;
            text-align: left;
            transition: all 0.25s ease;
         }
         .profile-nav-btn:hover {
            background: #fff0f3;
            color: var(--primary);
         }
         .profile-nav-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px rgba(232, 40, 26, 0.25);
         }
         .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0e6e8;
         }
         .avatar-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
         }
         .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
         }
         .tab-panel {
            display: none;
         }
         .tab-panel.active {
            display: block;
            animation: fadeIn 0.35s ease;
         }
         @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
         }
         .order-status-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 20px;
            text-transform: uppercase;
         }
      </style>
   </head>
   <body>
      <!-- TOP BAR -->
      <div id="topbar">
         <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
               <div class="top-contact d-flex flex-wrap">
                  <span><i class="fas fa-phone-alt"></i>076 344 5742</span>
                  <span><i class="fas fa-envelope"></i>info.glamhandy@gmail.com</span>
                  <span><i class="fas fa-map-marker-alt"></i>65/39, 7th Lane, Jothiala Rd, Malabe</span>
               </div>
               <div class="d-flex align-items-center gap-3">
                  <span class="ttag"><i class="fas fa-truck me-1"></i>Get your order delivered in just 2 days!</span>
                  <div class="tsoc">
                     <a href="https://web.facebook.com/info.glamhandycraftstore/?_rdc=1&_rdr" target="_blank"><i class="fab fa-facebook-f"></i></a>
                     <a href="https://www.instagram.com/info.glamhandyofficial/" target="_blank"><i class="fab fa-instagram"></i></a>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- NAVBAR -->
      <nav class="navbar navbar-expand-lg" id="nav">
         <div class="container">
            <a class="navbar-brand" href="index.html">
               <div class="blogo">
                  <div class="bico"><img src="img/logo.png.jpeg" alt="Logo"></div>
                  <div>
                     <div class="bname">Glam<span>Handy</span></div>
                     <div class="bsub">Craft Store</div>
                  </div>
               </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
               <i class="fas fa-bars" style="color:var(--primary);font-size:1.35rem;"></i>
            </button>
            <div class="collapse navbar-collapse" id="navmenu">
               <ul class="navbar-nav ms-auto me-3">
                  <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#about">About Us</a></li>
                  <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#contact-section">Contact</a></li>
               </ul>
               <div class="d-flex align-items-center gap-2">
                  <a href="user.php" id="dynNavUser" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                     <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($full_name); ?>
                  </a>
                  <a href="catalog.php" class="nav-link nav-cta ms-1"><i class="fas fa-shopping-bag me-1"></i>Order Now</a>
                  <form method="POST" action="user.php" style="display:inline;">
                     <button type="submit" name="logout" class="btn btn-sm btn-light border rounded-pill px-3" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
                  </form>
               </div>
            </div>
         </div>
      </nav>

      <?php echo $action_msg; ?>

      <!-- PAGE HEADER BANNER -->
      <section style="background: linear-gradient(135deg, #fff5f5 0%, #ffeef0 100%); padding: 45px 0; border-bottom: 1px solid #f0d8dc;">
         <div class="container text-center">
            <h1 class="stitle" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 900; color: var(--dark);">My <span>Account Dashboard</span></h1>
            <p class="text-muted mt-2" style="font-family: 'Poppins', sans-serif; max-width: 600px; margin: 0 auto; font-size: 0.95rem;">
               Welcome back, <strong><?php echo htmlspecialchars($full_name); ?></strong>! Track your orders, update your profile info, and manage your account.
            </p>
         </div>
      </section>

      <!-- PROFILE DASHBOARD SECTION -->
      <section style="padding: 50px 0; background: #fdf8f8; min-height: 75vh;">
         <div class="container">
            <div class="row g-4">

               <!-- SIDEBAR -->
               <div class="col-lg-4 col-xl-3">
                  <div class="profile-card p-4 text-center mb-4">
                     <div class="avatar-wrapper mb-3">
                        <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="User Avatar" class="avatar-img" onerror="this.src='img/testimonial/1.jpg';"/>
                     </div>
                     <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($full_name); ?></h5>
                     <p class="text-muted small mb-3"><i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($email); ?></p>
                     <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size:0.75rem;">VALUED CUSTOMER</span>
                  </div>

                  <div class="profile-card p-3">
                     <nav class="d-flex flex-column gap-1">
                        <button class="profile-nav-btn active" onclick="switchTab('tab-orders', this)">
                           <i class="fas fa-box"></i> My Orders &amp; Cart Status
                        </button>
                        <button class="profile-nav-btn" onclick="switchTab('tab-personal', this)">
                           <i class="fas fa-user"></i> Personal Profile Info
                        </button>
                        <button class="profile-nav-btn" onclick="switchTab('tab-security', this)">
                           <i class="fas fa-lock"></i> Security &amp; Password
                        </button>
                        <form method="POST" action="user.php" class="mt-2">
                           <button type="submit" name="logout" class="profile-nav-btn text-danger">
                              <i class="fas fa-sign-out-alt"></i> Log Out Account
                           </button>
                        </form>
                     </nav>
                  </div>
               </div>

               <!-- MAIN CONTENT PANELS -->
               <div class="col-lg-8 col-xl-9">

                  <!-- TAB 1: ORDER HISTORY & TRACKING -->
                  <div id="tab-orders" class="tab-panel active">
                     <div class="profile-card p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
                           <div>
                              <h4 class="fw-bold mb-0" style="font-family:'Playfair Display', serif;"><i class="fas fa-box-open me-2 text-danger"></i>My Craft Order History</h4>
                              <p class="text-muted small mb-0">View live status of orders placed from the catalog</p>
                           </div>
                           <span class="badge bg-danger px-3 py-2 rounded-pill"><?php echo $order_count; ?> Placed Orders</span>
                        </div>

                        <style>
                           /* 1. Add comfortable spacing between table columns */
                           .table th, .table td {
                               padding-left: 1.5rem !important;
                               padding-right: 1.5rem !important;
                           }

                           /* 2. Force headers, table cells, and buttons to STAY on 1 line */
                           .table th, 
                           .table td, 
                           .table .btn {
                               white-space: nowrap !important;
                           }

                           /* 3. Make the horizontal scrollbar thin & sleek */
                           .table-responsive::-webkit-scrollbar {
                               height: 6px !important;
                           }
                           .table-responsive::-webkit-scrollbar-thumb {
                               background-color: #e63946 !important;
                               border-radius: 10px;
                           }
                           .table-responsive::-webkit-scrollbar-track {
                               background-color: #ffeef0 !important;
                               border-radius: 10px;
                           }

                           /* 4. Make column titles NOT bold (lighter font) */
                           .table thead th {
                               font-weight: 500 !important;
                               color: #555555 !important;
                           }
                        </style>

                        <div class="table-responsive">
                           <table class="table table-hover align-middle text-nowrap">
                              <thead class="table-light text-nowrap">
                                  <tr>
                                     <th>Order ID</th>
                                     <th>Date &amp; Time</th>
                                     <th>Product Details</th>
                                     <th class="text-center">Quantity</th>
                                     <th>Total Price (LKR)</th>
                                     <th>Order Status</th>
                                     <th class="text-center">Action</th>
                                     <th class="text-end text-nowrap">Pay Now</th>
                                  </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 if ($user_orders && mysqli_num_rows($user_orders) > 0) {
                                     while ($row = mysqli_fetch_assoc($user_orders)) {
                                         $oid       = $row['Order_ID'];
                                         $p_name    = !empty($row['Product_Name']) ? $row['Product_Name'] : 'Craft Product #' . $row['Product_ID'];
                                         $p_img     = !empty($row['Image']) ? $row['Image'] : 'img/logo.png.jpeg';
                                         $qty       = $row['Quantity'];
                                         $tot_price = $row['Total_Price'];
                                         $status    = !empty($row['Status']) ? $row['Status'] : 'Pending';
                                         $odate     = !empty($row['Order_Date']) ? date('d M Y, h:i A', strtotime($row['Order_Date'])) : 'N/A';

                                          $status_badge = 'bg-warning text-dark';
                                          $status_label = $status;
                                          $st_lower    = strtolower($status);

                                          if ($st_lower === 'payment uploaded') {
                                              $status_badge = 'bg-info text-white';
                                              $status_label = 'Under Admin Review';
                                          } elseif ($st_lower === 'completed') {
                                              $status_badge = 'bg-success text-white';
                                              $status_label = 'Completed';
                                          } elseif ($st_lower === 'accepted' || $st_lower === 'approved') {
                                              $status_badge = 'bg-primary text-white';
                                              $status_label = 'Approved - Pending Payment';
                                          } elseif ($st_lower === 'rejected' || $st_lower === 'cancelled') {
                                              $status_badge = 'bg-danger text-white';
                                              $status_label = ($st_lower === 'rejected') ? 'Rejected by Admin' : 'Cancelled';
                                          }
                                          ?>
                                          <tr>
                                             <td class="fw-bold text-danger">#<?php echo $oid; ?></td>
                                             <td class="small text-muted fw-semibold"><?php echo htmlspecialchars($odate); ?></td>
                                             <td>
                                                <div class="d-flex align-items-center gap-2">
                                                   <img src="<?php echo htmlspecialchars($p_img); ?>" alt="<?php echo htmlspecialchars($p_name); ?>" onerror="this.src='img/logo.png.jpeg';" style="width:42px;height:42px;border-radius:8px;object-fit:cover;">
                                                   <div>
                                                      <div class="fw-semibold text-dark"><?php echo htmlspecialchars($p_name); ?></div>
                                                      <small class="text-muted">Item ID: #<?php echo $row['Product_ID']; ?></small>
                                                   </div>
                                                </div>
                                             </td>
                                             <td class="fw-bold text-center"><?php echo $qty; ?></td>
                                             <td class="fw-bold text-dark">Rs. <?php echo number_format(floatval($tot_price), 2); ?></td>
                                              <td>
                                                 <span class="badge <?php echo $status_badge; ?> order-status-badge">
                                                    <?php echo htmlspecialchars($status_label); ?>
                                                 </span>
                                              </td>
                                              <td class="text-center">
                                                 <?php if (in_array($st_lower, ['rejected', 'cancelled'])): ?>
                                                    <span class="text-muted small"><i class="fas fa-times-circle text-danger me-1"></i><?php echo ($st_lower === 'rejected') ? 'Rejected' : 'Cancelled'; ?></span>
                                                 <?php elseif ($st_lower !== 'completed'): ?>
                                                    <form method="POST" action="user.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel order #<?php echo $oid; ?>?');">
                                                       <input type="hidden" name="order_id" value="<?php echo $oid; ?>">
                                                       <button type="submit" name="cancel_order" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                                                          <i class="fas fa-trash-alt me-1"></i>Cancel Order
                                                       </button>
                                                    </form>
                                                 <?php else: ?>
                                                    <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Completed</span>
                                                 <?php endif; ?>
                                              </td>
                                              <td class="text-end">
                                                <?php if (in_array($st_lower, ['rejected', 'cancelled'])): ?>
                                                   <span class="badge bg-secondary text-white px-3 py-2 rounded-pill small">
                                                      <i class="fas fa-ban me-1"></i>Checkout Disabled
                                                   </span>
                                                <?php elseif ($st_lower === 'completed' || $st_lower === 'approved'): ?>
                                                   <a href="checkout.php?order_id=<?php echo $oid; ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                                                      <i class="fas fa-eye me-1"></i>View
                                                   </a>
                                                <?php elseif ($st_lower === 'payment uploaded'): ?>
                                                   <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill small">
                                                      <i class="fas fa-clock me-1"></i>Under Review
                                                   </span>
                                                <?php else: ?>
                                                   <a href="checkout.php?order_id=<?php echo $oid; ?>" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm fw-semibold">
                                                      <i class="fas fa-credit-card me-1"></i>Go to Checkout
                                                   </a>
                                                <?php endif; ?>
                                              </td>
                                          </tr>
                                         <?php
                                     }
                                 } else {
                                     echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='fas fa-shopping-bag fa-2x mb-3 d-block text-secondary'></i>You haven't placed any orders yet! <a href='catalog.php' class='text-danger fw-bold ms-1'>Browse Craft Catalog</a></td></tr>";
                                 }
                                 ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>

                  <!-- TAB 2: PERSONAL INFO & CONTACT -->
                  <div id="tab-personal" class="tab-panel">
                     <div class="profile-card p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                           <h4 class="fw-bold mb-0" style="font-family:'Playfair Display', serif;"><i class="fas fa-id-card me-2 text-danger"></i>Personal Details &amp; Account Info</h4>
                        </div>
                        <form method="POST" action="user.php" enctype="multipart/form-data">
                           <div class="row g-3">
                              <div class="col-md-6">
                                 <label class="form-label fw-semibold text-secondary">Full Name *</label>
                                 <input type="text" name="full_name" class="form-control p-3" value="<?php echo htmlspecialchars($full_name); ?>" required/>
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label fw-semibold text-secondary">Telephone Number</label>
                                 <input type="tel" name="telephone" class="form-control p-3" value="<?php echo htmlspecialchars($telephone); ?>"/>
                              </div>
                              <div class="col-12">
                                 <label class="form-label fw-semibold text-secondary">Email Address (Account ID)</label>
                                 <input type="email" class="form-control p-3" value="<?php echo htmlspecialchars($email); ?>" readonly disabled/>
                              </div>
                              <div class="col-12">
                                 <label class="form-label fw-semibold text-secondary"><i class="fas fa-map-marker-alt me-1 text-danger"></i>Delivery Address</label>
                                 <textarea name="address" class="form-control p-3" rows="2" placeholder="Enter your full shipping address for orders..."><?php echo htmlspecialchars($user_address); ?></textarea>
                              </div>
                              <div class="col-12 mt-3">
                                 <label class="form-label fw-semibold text-secondary"><i class="fas fa-camera me-1 text-danger"></i>Upload Profile Picture File</label>
                                 <input type="file" name="profile_pic" class="form-control p-3" accept="image/*"/>
                                 <small class="text-muted d-block mt-1">Select an image file (JPG, PNG, WEBP) from your computer to update your profile photo.</small>
                              </div>
                              <div class="col-12 mt-4 text-end">
                                 <button type="submit" name="update_profile" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-save me-2"></i>Save Profile Changes</button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>

                  <!-- TAB 3: SECURITY & PASSWORD -->
                  <div id="tab-security" class="tab-panel">
                     <div class="profile-card p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                           <h4 class="fw-bold mb-0" style="font-family:'Playfair Display', serif;"><i class="fas fa-shield-alt me-2 text-danger"></i>Security &amp; Password Management</h4>
                        </div>
                        <form method="POST" action="user.php">
                           <div class="row g-3" style="max-width: 550px;">
                              <div class="col-12">
                                 <label class="form-label fw-semibold text-secondary">New Password *</label>
                                 <input type="password" name="new_password" class="form-control p-3" placeholder="Enter new password (min. 6 characters)" required minlength="6"/>
                              </div>
                              <div class="col-12 mt-4">
                                 <button type="submit" name="update_password" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-lock me-2"></i>Update Password</button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </section>

      <!-- FOOTER -->
      <footer>
         <div class="container">
            <div class="row g-5">
               <div class="col-lg-4">
                  <div class="fnm">Glam<span>Handy</span></div>
                  <p class="fdesc">We bring together quality craft supplies at affordable prices, helping every creator turn ideas into beautiful handmade projects.</p>
                  <div class="fsoc">
                     <a href="https://web.facebook.com/info.glamhandycraftstore/?_rdc=1&_rdr" target="_blank"><i class="fab fa-facebook-f"></i></a>
                     <a href="https://www.instagram.com/info.glamhandyofficial/" target="_blank"><i class="fab fa-instagram"></i></a>
                  </div>
               </div>
               <div class="col-sm-6 col-lg-2">
                  <div class="ftit">Quick Links</div>
                  <ul class="flinks ps-0">
                     <li><a href="index.html#hero"><i class="fas fa-chevron-right"></i>Home</a></li>
                     <li><a href="index.html#about"><i class="fas fa-chevron-right"></i>About Us</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Our Collection</a></li>
                     <li><a href="index.html#contact-section"><i class="fas fa-chevron-right"></i>Contact</a></li>
                  </ul>
               </div>
               <div class="col-sm-6 col-lg-2">
                  <div class="ftit">Our Collection</div>
                  <ul class="flinks ps-0">
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Crochet &amp; Yarn</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Beads &amp; Jewellery</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Bag Accessories</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Craft Tools</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Craft Materials</a></li>
                  </ul>
               </div>
               <div class="col-lg-4">
                  <div class="ftit">Get In Touch</div>
                  <div class="fci">
                     <div class="fciico"><i class="fas fa-map-marker-alt"></i></div>
                     <div class="fciinfo"><strong>Address</strong>65/39, 7th Lane, Jothiala Rd, Malabe</div>
                  </div>
                  <div class="fci">
                     <div class="fciico"><i class="fas fa-phone-alt"></i></div>
                     <div class="fciinfo"><strong>Phone</strong>076 344 5742</div>
                  </div>
                  <div class="fci">
                     <div class="fciico"><i class="fas fa-envelope"></i></div>
                     <div class="fciinfo"><strong>Email</strong>info.glamhandy@gmail.com</div>
                  </div>
               </div>
            </div>
         </div>
      </footer>

      <!-- JS Libraries -->
      <script src="js/jquery-3.7.1.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script>
         if ('scrollRestoration' in history) {
             history.scrollRestoration = 'manual';
         }

         function saveScrollPos() {
             sessionStorage.setItem('scrollPos_' + window.location.pathname, window.scrollY);
         }

         window.addEventListener('beforeunload', saveScrollPos);
         document.addEventListener('submit', saveScrollPos);
         document.addEventListener('click', function(e) {
             const target = e.target.closest('a, button, [type="submit"]');
             if (target) {
                 saveScrollPos();
             }
         });

         function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.profile-nav-btn').forEach(b => b.classList.remove('active'));
            const targetPanel = document.getElementById(tabId);
            if (targetPanel) targetPanel.classList.add('active');
            if (btn) btn.classList.add('active');
            sessionStorage.setItem('userActiveTab', tabId);
         }

         document.addEventListener('DOMContentLoaded', function() {
            const activeTab = sessionStorage.getItem('userActiveTab');
            if (activeTab && document.getElementById(activeTab)) {
               const btn = document.querySelector(`.profile-nav-btn[onclick*="${activeTab}"]`);
               switchTab(activeTab, btn);
            }

            const savedPos = sessionStorage.getItem('scrollPos_' + window.location.pathname);
            if (savedPos !== null) {
                const pos = parseInt(savedPos, 10);
                window.scrollTo(0, pos);
                requestAnimationFrame(function() { window.scrollTo(0, pos); });
                setTimeout(function() { window.scrollTo(0, pos); }, 50);
                setTimeout(function() { window.scrollTo(0, pos); }, 200);
                setTimeout(function() {
                    sessionStorage.removeItem('scrollPos_' + window.location.pathname);
                }, 600);
            }
         });
      </script>
   </body>
</html>
