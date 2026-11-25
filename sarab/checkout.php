<?php
session_start();

// Security / Anti-Cache Headers
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 1. Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php?error=login_required');
    exit();
}

// 2. Database Connection
require_once 'config.php';
$email = mysqli_real_escape_string($con, $_SESSION['email']);

// Fetch Logged-in User Info
$user_id      = 1;
$user_name    = "Valued Customer";
$user_address = "65/39, 7th Lane, Jothiala Rd, Malabe";
$u_q = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'");
if ($u_q && mysqli_num_rows($u_q) > 0) {
    $u_r          = mysqli_fetch_assoc($u_q);
    $user_id      = isset($u_r['User_ID']) ? $u_r['User_ID'] : (isset($u_r['id']) ? $u_r['id'] : 1);
    $user_name    = isset($u_r['Full_Name']) ? $u_r['Full_Name'] : "Valued Customer";
    $user_address = !empty($u_r['Delivery_Address']) ? $u_r['Delivery_Address'] : (!empty($u_r['Address']) ? $u_r['Address'] : "65/39, 7th Lane, Jothiala Rd, Malabe");
}

// Detect Price column in orders table
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

// Check if specific order_id is requested via GET parameter or POST submission
$target_order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : (isset($_POST['order_id']) ? intval($_POST['order_id']) : 0);

$payment_success = false;
$payment_msg     = "";
$paid_order_info = null;

// Handle Payment Submission & Receipt Upload
if (isset($_POST['submit_payment'])) {
    $pay_oid  = intval($_POST['order_id']);
    $paid_amt = floatval($_POST['paid_amount']);
    
    // Security check: verify order status is not Rejected or Cancelled
    $st_chk = mysqli_query($con, "SELECT Status FROM orders WHERE Order_ID='$pay_oid' AND User_ID='$user_id'");
    if ($st_chk && $sr = mysqli_fetch_assoc($st_chk)) {
        if (in_array(strtolower($sr['Status']), ['rejected', 'cancelled'])) {
            $payment_msg = "Order #$pay_oid has been rejected or cancelled. Payment upload is disabled.";
            $payment_success = false;
        } else if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] == 0) {
            $upload_dir = 'uploads/receipts/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION));
            $allowed  = array('jpg', 'jpeg', 'png', 'pdf');
            
            if (in_array($file_ext, $allowed)) {
                $new_filename = "receipt_" . $pay_oid . "_" . time() . "." . $file_ext;
                $target_file  = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $target_file)) {
                    $today_date = date('Y-m-d');
                    
                    // 1. Insert into 'customer_payments' table
                    $ins_pay = mysqli_query($con, "INSERT INTO customer_payments (Order_ID, User_ID, Paid_Amount, Receipt, Payment_Status, Paid_date) 
                                                   VALUES ('$pay_oid', '$user_id', '$paid_amt', '$target_file', 'Paid', '$today_date')");
                    
                    // 2. Update 'orders' table status to 'Payment Uploaded'
                    $up_order = mysqli_query($con, "UPDATE orders SET Status='Payment Uploaded' WHERE Order_ID='$pay_oid'");
                    
                    if ($ins_pay && $up_order) {
                        $payment_success = true;
                        $target_order_id = $pay_oid;
                        $payment_msg     = "Payment slip uploaded successfully! Order #$pay_oid is now waiting for Admin verification and approval.";
                    } else {
                        $payment_msg = "Database update error: " . mysqli_error($con);
                    }
                } else {
                    $payment_msg = "Error moving uploaded file to server directory.";
                }
            } else {
                $payment_msg = "Invalid file type. Please upload a JPG, PNG, or PDF file.";
            }
        }
    }
}

// Fetch Order Details
$order_item  = null;
$order_found = false;

if ($target_order_id > 0) {
    $o_query = mysqli_query($con, "SELECT o.Order_ID, o.User_ID, o.Product_ID, o.Quantity, o.`$price_col` AS Total_Price, o.Status, p.Product_Name, p.Image, p.Price_LKR FROM orders o LEFT JOIN products p ON o.Product_ID = p.Product_ID WHERE o.Order_ID = '$target_order_id' AND o.User_ID = '$user_id'");
    if ($o_query && mysqli_num_rows($o_query) > 0) {
        $order_item  = mysqli_fetch_assoc($o_query);
        $order_found = true;
    }
}

// Fallback: If no order ID specified or found, get the user's latest active (non-rejected) order
if (!$order_found) {
    $o_query = mysqli_query($con, "SELECT o.Order_ID, o.User_ID, o.Product_ID, o.Quantity, o.`$price_col` AS Total_Price, o.Status, p.Product_Name, p.Image, p.Price_LKR FROM orders o LEFT JOIN products p ON o.Product_ID = p.Product_ID WHERE o.User_ID = '$user_id' ORDER BY o.Order_ID DESC LIMIT 1");
    if ($o_query && mysqli_num_rows($o_query) > 0) {
        $order_item  = mysqli_fetch_assoc($o_query);
        $order_found = true;
    }
}

// Calculate price variables if order found
if ($order_found && $order_item) {
    $tot_amount = floatval($order_item['Total_Price']);
    $item_qty   = intval($order_item['Quantity']);
    $unit_price = $item_qty > 0 ? ($tot_amount / $item_qty) : $tot_amount;
    $shipping   = ($tot_amount > 2500 || $tot_amount == 0) ? 0.00 : 350.00;
    $grand_total = $tot_amount + $shipping;

    // Automatically display summary card ONLY IF a payment record exists and order is completed or payment uploaded
    $st_lower = strtolower($order_item['Status']);
    $chk_p = mysqli_query($con, "SELECT Payment_ID FROM customer_payments WHERE Order_ID='" . $order_item['Order_ID'] . "'");
    $has_payment_record = ($chk_p && mysqli_num_rows($chk_p) > 0);

    if ($has_payment_record && in_array($st_lower, ['completed', 'payment uploaded'])) {
        $payment_success = true;
        if (empty($payment_msg)) {
            if ($st_lower === 'payment uploaded') {
                $payment_msg = "Payment slip uploaded successfully! Order #" . $order_item['Order_ID'] . " is currently under review by Admin.";
            } else {
                $payment_msg = "Payment confirmed and approved by Admin! Order #" . $order_item['Order_ID'] . " is set for delivery.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="GlamHandy Team">
      <meta name="description" content="Checkout & Payment - GlamHandy Arts & Crafts Store">
      <title>Checkout &amp; Cart Payment - GlamHandy</title>
      <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
      <!-- Bootstrap 5.3 -->
      <link href="css/bootstrap.min.css" rel="stylesheet"/>
      <!-- Font Awesome Icons -->
      <link rel="stylesheet" href="css/all.min.css"/>
      <!-- Style CSS -->
      <link rel="stylesheet" href="css/style.css"/>
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
                  <li class="nav-item"><a class="nav-link" href="index.html#about">About</a></li>
                  <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#contact-section">Contact</a></li>
               </ul>
               <div class="d-flex align-items-center gap-2">
                  <a href="user.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                     <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($user_name); ?>
                  </a>
                  <a href="checkout.php" class="nav-link nav-cta ms-1"><i class="fas fa-box me-1"></i>Checkout</a>
               </div>
            </div>
         </div>
      </nav>

      <!-- PAGE HEADER -->
      <section style="background: linear-gradient(135deg, #fff0f3 0%, #ffeef0 100%); padding: 45px 0; border-bottom: 1px solid #f0d8dc;">
         <div class="container text-center">
            <h1 class="stitle" style="font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 900; color: var(--dark);">
               Checkout &amp; Payment
            </h1>
            <p class="text-muted mt-2" style="font-family: 'Poppins', sans-serif; max-width: 600px; margin: 0 auto; font-size: 0.95rem;">
               Review your approved order summary, enter delivery details, and complete your payment.
            </p>
         </div>
      </section>

      <!-- CHECKOUT MAIN CONTENT SECTION -->
      <section class="checkout-section py-5" style="background: #fdf8f8; min-height: 70vh;">
         <div class="container">
            <?php if ($payment_success): ?>
               <!-- SUCCESS PAYMENT BANNER CARD -->
               <div class="card shadow border-0 rounded-4 p-5 text-center mx-auto" style="max-width: 680px; background:#fff;">
                  <div class="mb-4">
                     <i class="fas fa-check-circle text-success" style="font-size: 4.5rem;"></i>
                  </div>
                  <h3 class="fw-bold mb-2" style="font-family:'Playfair Display', serif; color: var(--dark);">Payment Successful!</h3>
                  <p class="text-secondary fs-6 mb-4"><?php echo htmlspecialchars($payment_msg); ?></p>
                  
                  <div class="bg-light p-4 rounded-3 text-start mb-4 border">
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order ID:</span>
                        <strong class="text-danger">#<?php echo $order_item['Order_ID']; ?></strong>
                     </div>
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Product Name:</span>
                        <strong class="text-dark"><?php echo htmlspecialchars($order_item['Product_Name']); ?></strong>
                     </div>
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Quantity:</span>
                        <strong><?php echo $item_qty; ?> unit(s)</strong>
                     </div>
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Paid:</span>
                        <strong class="text-success fs-5">Rs. <?php echo number_format($grand_total, 2); ?></strong>
                     </div>
                     <hr class="my-2" />
                     <div class="d-flex justify-content-between align-items-center mb-0">
                        <span class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i>Delivery Address:</span>
                        <strong class="text-dark small text-end" style="max-width: 300px;"><?php echo htmlspecialchars($user_address); ?></strong>
                     </div>
                  </div>

                  <div class="d-flex justify-content-center gap-3 flex-wrap">
                     <a href="user.php" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-user-circle me-1"></i>Go to My Profile</a>
                     <a href="catalog.php" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-shopping-bag me-1"></i>Continue Shopping</a>
                  </div>
               </div>
            <?php elseif ($order_found && $order_item && in_array(strtolower($order_item['Status']), ['rejected', 'cancelled'])): ?>
               <!-- REJECTED / CANCELLED ORDER CHECKOUT DISABLED VIEW -->
               <div class="card shadow border-0 rounded-4 p-5 text-center mx-auto" style="max-width: 680px; background:#fff;">
                  <div class="mb-4">
                     <i class="fas fa-ban text-danger" style="font-size: 4.5rem;"></i>
                  </div>
                  <h3 class="fw-bold mb-2" style="font-family:'Playfair Display', serif; color: var(--dark);">Checkout &amp; Payment Disabled</h3>
                  <p class="text-secondary fs-6 mb-4">
                     Order <strong>#<?php echo $order_item['Order_ID']; ?></strong> has been <strong><?php echo htmlspecialchars(ucfirst($order_item['Status'])); ?></strong> by Admin. Checkout and payment uploads are unavailable for this order.
                  </p>

                  <div class="bg-light p-3 rounded-3 text-start mb-4 border" style="max-width: 500px; margin: 0 auto;">
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order ID:</span>
                        <strong class="text-danger">#<?php echo $order_item['Order_ID']; ?></strong>
                     </div>
                     <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Product Name:</span>
                        <strong class="text-dark"><?php echo htmlspecialchars($order_item['Product_Name']); ?></strong>
                     </div>
                     <div class="d-flex justify-content-between mb-0">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-danger text-white"><?php echo htmlspecialchars(ucfirst($order_item['Status'])); ?></span>
                     </div>
                  </div>
                  
                  <div class="d-flex justify-content-center gap-3 flex-wrap">
                     <a href="user.php" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-user-circle me-1"></i>Go to My Profile</a>
                     <a href="catalog.php" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-shopping-bag me-1"></i>Browse Catalog</a>
                  </div>
               </div>
            <?php elseif ($order_found && $order_item): ?>
               <!-- CHECKOUT FORM & SUMMARY -->
               <div class="row g-4">
                  <!-- LEFT COLUMN: PAYMENT METHOD & INSTRUCTIONS -->
                  <div class="col-lg-7">
                     <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                           <h4 class="fw-bold mb-4 pb-2 border-bottom" style="font-family:'Playfair Display', serif;">
                              Select Payment Method
                           </h4>
                           
                           <!-- Payment Method Selectors -->
                           <div class="mb-4">
                              <div class="form-check p-3 border rounded-3 bg-light mb-3">
                                 <input class="form-check-input ms-1 me-2" type="radio" name="payment_type" id="payOffline" value="offline" checked onclick="togglePaymentView('offline')">
                                 <label class="form-check-label fw-bold text-dark" for="payOffline">
                                    Online Payment Slip Upload
                                 </label>
                              </div>

                              <div class="form-check p-3 border rounded-3 bg-light">
                                 <input class="form-check-input ms-1 me-2" type="radio" name="payment_type" id="payOnline" value="online" onclick="togglePaymentView('online')">
                                 <label class="form-check-label fw-bold text-dark" for="payOnline">
                                    Online Card Payment
                                 </label>
                              </div>
                           </div>

                             <!-- ONLINE PAYMENT SLIP UPLOAD PANEL -->
                             <div id="offlinePaymentPanel" class="p-4 rounded-3 border" style="background: #fff8f8; border-color: #f5c6cb !important;">
                                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-file-invoice-dollar me-2 text-danger"></i>Online Payment Slip Upload</h5>
                                <p class="text-dark lh-lg mb-3">
                                   Please pay the total amount of <strong>Rs. <?php echo number_format($grand_total, 2); ?></strong> to our bank account below and upload your payment slip to complete your order.
                                </p>

                                <!-- BANK ACCOUNT DETAILS CARD -->
                                <div class="card border-0 rounded-3 mb-4 p-3" style="background:#ffffff; border-left: 4px solid var(--primary) !important; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                   <h6 class="fw-bold text-danger mb-3"><i class="fas fa-university me-2"></i>Bank Transfer Account Info</h6>
                                   <div class="row g-3 small">
                                      <div class="col-6">
                                         <span class="text-muted d-block fw-semibold mb-1">Bank Name</span>
                                         <strong class="text-dark fs-6">Commercial Bank</strong>
                                      </div>
                                      <div class="col-6">
                                         <span class="text-muted d-block fw-semibold mb-1">Account Name</span>
                                         <strong class="text-dark fs-6">GlamHandy Craft Store</strong>
                                      </div>
                                      <div class="col-6">
                                         <span class="text-muted d-block fw-semibold mb-1">Account Number</span>
                                         <strong class="text-danger fs-6">8009 1234 5678</strong>
                                      </div>
                                      <div class="col-6">
                                         <span class="text-muted d-block fw-semibold mb-1">Branch</span>
                                         <strong class="text-dark fs-6">Malabe Branch</strong>
                                      </div>
                                   </div>
                                </div>

                                <!-- form to upload payment -->
                                <form action="checkout.php" method="POST" enctype="multipart/form-data" class="mt-3">
                                   <input type="hidden" name="order_id" value="<?php echo $order_item['Order_ID']; ?>">
                                   <input type="hidden" name="paid_amount" value="<?php echo $grand_total; ?>">

                                   <div class="mb-3">
                                      <label class="form-label text-secondary fw-semibold">Upload Payment Slip (JPG, PNG, PDF):</label>
                                      <input type="file" name="receipt_file" class="form-control p-3" accept="image/*,.pdf" required>
                                   </div>

                                   <button type="submit" name="submit_payment" class="btn btn-success rounded-pill fw-bold px-4 py-3 w-100 shadow-sm fs-6">
                                      <i class="fas fa-upload me-2"></i>Submit Payment Slip
                                   </button>
                                </form>
                             </div>

                             <!-- ONLINE CARD PAYMENT PANEL -->
                             <div id="onlinePaymentPanel" class="p-4 rounded-3 border text-center d-none" style="background: #eef7ff; border-color: #b8daff !important;">
                                <div class="my-3">
                                   <i class="fas fa-credit-card text-primary" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="fw-bold text-primary mb-2">Online Card Payment</h5>
                                <p class="text-secondary mb-0">Automated online card payments are currently under maintenance. Please use the Online Payment Slip Upload option.</p>
                             </div>

                         </div>
                      </div>
                   </div>

                   <script>
                   function togglePaymentView(type) {
                      if (type === 'offline') {
                         document.getElementById('offlinePaymentPanel').classList.remove('d-none');
                         document.getElementById('onlinePaymentPanel').classList.add('d-none');
                      } else {
                         document.getElementById('offlinePaymentPanel').classList.add('d-none');
                         document.getElementById('onlinePaymentPanel').classList.remove('d-none');
                      }
                   }
                   </script>

                  <!-- RIGHT COLUMN: ORDER SUMMARY -->
                  <div class="col-lg-5">
                     <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                           <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                              <h4 class="fw-bold mb-0" style="font-family:'Playfair Display', serif;">
                                 Order Summary
                              </h4>
                              <span class="badge bg-danger rounded-pill px-3 py-2">Order #<?php echo $order_item['Order_ID']; ?></span>
                           </div>

                           <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background:#fff7f8; border: 1px solid #f0d8dc;">
                              <img src="<?php echo htmlspecialchars(!empty($order_item['Image']) ? $order_item['Image'] : 'img/logo.png.jpeg'); ?>" alt="Product" onerror="this.src='img/logo.png.jpeg';" style="width: 65px; height: 65px; object-fit: cover; border-radius: 10px;" />
                              <div>
                                 <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars(!empty($order_item['Product_Name']) ? $order_item['Product_Name'] : 'Craft Product #' . $order_item['Product_ID']); ?></h6>
                                 <div class="small text-muted mb-1">Quantity: <strong><?php echo $item_qty; ?></strong> unit(s)</div>
                                 <div class="fw-bold text-danger">Rs. <?php echo number_format($unit_price, 2); ?> / unit</div>
                              </div>
                           </div>

                           <div class="d-flex justify-content-between mb-2">
                              <span class="text-secondary">Subtotal</span>
                              <strong class="text-dark">Rs. <?php echo number_format($tot_amount, 2); ?></strong>
                           </div>
                           
                           <div class="d-flex justify-content-between mb-3">
                              <span class="text-secondary">Islandwide Delivery Fee</span>
                              <strong class="<?php echo $shipping == 0 ? 'text-success' : 'text-dark'; ?>">
                                 <?php echo $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 2); ?>
                              </strong>
                           </div>

                           <hr />

                           <div class="d-flex justify-content-between align-items-center mb-4">
                              <span class="h5 fw-bold text-dark mb-0">Total Amount</span>
                              <span class="h4 fw-bold text-danger mb-0">Rs. <?php echo number_format($grand_total, 2); ?></span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            <?php else: ?>
               <!-- NO ORDER FOUND CARD -->
               <div class="card shadow-sm border-0 rounded-4 p-5 text-center mx-auto" style="max-width: 600px; background:#fff;">
                  <i class="fas fa-shopping-bag fa-4x text-secondary mb-3"></i>
                  <h4 class="fw-bold text-dark mb-2">No Active Order Found</h4>
                  <p class="text-muted mb-4">You do not have any accepted order pending checkout at the moment.</p>
                  <a href="catalog.php" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold"><i class="fas fa-store me-2"></i>Browse Craft Catalog</a>
               </div>
            <?php endif; ?>
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
                     <li><a href="index.html"><i class="fas fa-chevron-right"></i>Home</a></li>
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
   </body>
</html>
