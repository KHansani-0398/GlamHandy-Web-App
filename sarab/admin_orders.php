<?php
session_start();

if (!isset($_SESSION["email"])) {
    header('Location: login.php');
    exit();
}

if (strtolower($_SESSION["email"]) != "admin@gmail.com") {
    header('Location: user.php');
    exit();
}

// Security headers
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$action_msg = "";
require_once 'config.php';

// Helper to determine Total Price column in orders table
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

// Action Handlers
if (isset($_POST['accept_order'])) {
    $order_id = intval($_POST['Order_ID']);
    $query = mysqli_query($con, "UPDATE orders SET Status='Completed' WHERE Order_ID='$order_id'");
    if ($query) {
        $action_msg = "<div class='alert alert-success alert-dismissible fade show m-3' role='alert'><i class='fas fa-check-circle me-2'></i>Order #$order_id has been Verified &amp; Accepted (Completed)!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

if (isset($_POST['reject_order'])) {
    $order_id = intval($_POST['Order_ID']);
    $query = mysqli_query($con, "UPDATE orders SET Status='Rejected' WHERE Order_ID='$order_id'");
    if ($query) {
        $action_msg = "<div class='alert alert-danger alert-dismissible fade show m-3' role='alert'><i class='fas fa-times-circle me-2'></i>Order #$order_id has been Rejected.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Pending Orders Verification - GlamHandy Admin</title>
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
   <!-- Bootstrap 5.3 -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
   <!-- FontAwesome 6 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

   <style>
      :root {
         --pink: #ff00b6;
         --dark: #212529;
         --bg: #f8f9fa;
      }
      body {
         font-family: 'Poppins', sans-serif;
         background-color: var(--bg);
         color: #333;
      }
      .bname {
         font-family: 'Playfair Display', serif;
         font-weight: 900;
         font-size: 1.5rem;
      }
      .bname span { color: var(--pink); }
      .bsub {
         font-size: 0.72rem;
         letter-spacing: 1px;
         text-transform: uppercase;
      }
      .order-card {
         border-radius: 16px;
         border: none;
         background: #fff;
         box-shadow: 0 4px 18px rgba(0,0,0,0.04);
         transition: transform 0.2s ease, box-shadow 0.2s ease;
      }
      .order-card:hover {
         box-shadow: 0 8px 24px rgba(0,0,0,0.08);
      }
   </style>
</head>

<body>
   <!-- NAVBAR -->
   <nav class="navbar navbar-expand-lg" style="background: var(--dark); padding: 15px 0;">
      <div class="container">
         <a class="navbar-brand" href="Admin.php">
            <div class="blogo">
               <div>
                  <div class="bname" style="color: #fff;">Glam<span style="color: #ff00b6;">Handy</span></div>
                  <div class="bsub" style="color: #ccc;">Orders Center</div>
               </div>
            </div>
         </a>
         <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="Admin.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-1"><i class="fas fa-arrow-left me-1"></i>Admin Dashboard</a>
            <span class="fw-semibold text-white small ms-2">
               <i class="fas fa-user-shield me-1 text-danger"></i> logged as: <?php echo $_SESSION["email"]; ?>
            </span>
            <form method="POST" action="Admin.php" style="display:inline;">
               <button type="submit" name="logout" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
            </form>
         </div>
      </div>
   </nav>

   <?php echo $action_msg; ?>

   <section class="py-4">
      <div class="container">
         
         <!-- PAGE HEADER -->
         <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-2 border-bottom">
            <div>
               <h2 class="fw-bold mb-1" style="font-family:'Playfair Display', serif;">
                  <i class="fas fa-receipt text-danger me-2"></i>Pending Orders Verification
               </h2>
               <p class="text-muted small mb-0">Review payment slips uploaded by customers and choose to Accept or Reject pending orders.</p>
            </div>
         </div>

         <!-- ORDERS LIST -->
         <div class="row g-4">
            <?php
            $orders_sql = "SELECT o.Order_ID, o.User_ID, o.Product_ID, o.Quantity, o.`$price_col` AS Total_Price, o.Status,
                                  u.Full_Name, u.Email, u.Telephone, u.Address, u.Delivery_Address,
                                  p.Product_Name, p.Image,
                                  cp.Receipt, cp.Paid_Amount
                           FROM orders o
                           LEFT JOIN users u ON o.User_ID = u.User_ID
                           LEFT JOIN products p ON o.Product_ID = p.Product_ID
                           LEFT JOIN customer_payments cp ON o.Order_ID = cp.Order_ID
                           WHERE LOWER(o.Status) = 'payment uploaded'
                           ORDER BY o.Order_ID DESC";
            
            $orders_res = mysqli_query($con, $orders_sql);

            if ($orders_res && mysqli_num_rows($orders_res) > 0) {
                while ($row = mysqli_fetch_assoc($orders_res)) {
                    $oid       = $row['Order_ID'];
                    $uid       = $row['User_ID'];
                    $uname     = !empty($row['Full_Name']) ? $row['Full_Name'] : 'Customer #' . $uid;
                    $uemail    = !empty($row['Email']) ? $row['Email'] : '-';
                    $uphone    = !empty($row['Telephone']) ? $row['Telephone'] : '-';
                    $uaddr     = !empty($row['Delivery_Address']) ? $row['Delivery_Address'] : (!empty($row['Address']) ? $row['Address'] : 'Not specified');
                    
                    $pname     = !empty($row['Product_Name']) ? $row['Product_Name'] : 'Product #' . $row['Product_ID'];
                    $pimg      = !empty($row['Image']) ? $row['Image'] : 'img/logo.png.jpeg';
                    $qty       = $row['Quantity'];
                    $tot_price = floatval($row['Total_Price']);

                    $receipt   = $row['Receipt'];
                    $paid_amt  = floatval($row['Paid_Amount']);
                    ?>
                    <div class="col-12">
                       <div class="card order-card p-4">
                          <div class="row align-items-center g-3">
                             
                             <!-- Order & Customer Info -->
                             <div class="col-lg-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                   <span class="badge bg-danger rounded-pill px-3 py-1 fs-6">Order #<?php echo $oid; ?></span>
                                   <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">Payment Uploaded</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($uname); ?></h5>
                                <div class="small text-muted mb-1"><i class="fas fa-id-badge me-1"></i>User ID: #<?php echo $uid; ?></div>
                                <div class="small text-muted mb-1"><i class="fas fa-phone text-danger me-1"></i><?php echo htmlspecialchars($uphone); ?></div>
                                <div class="small text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i><?php echo htmlspecialchars($uaddr); ?></div>
                             </div>

                             <!-- Product Info -->
                             <div class="col-lg-4">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: #fdf8f8; border: 1px solid #f0d8dc;">
                                   <img src="<?php echo htmlspecialchars($pimg); ?>" alt="Product" onerror="this.src='img/logo.png.jpeg';" style="width: 55px; height: 55px; object-fit: cover; border-radius: 8px;">
                                   <div>
                                      <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($pname); ?></h6>
                                      <div class="small text-muted mb-1">Quantity: <strong><?php echo $qty; ?></strong> unit(s)</div>
                                      <div class="fw-bold text-danger">Total: Rs. <?php echo number_format($tot_price, 2); ?></div>
                                      <?php if ($paid_amt > 0): ?>
                                         <div class="small text-success"><i class="fas fa-check-circle me-1"></i>Paid Amount: Rs. <?php echo number_format($paid_amt, 2); ?></div>
                                      <?php endif; ?>
                                   </div>
                                </div>
                             </div>

                             <!-- Actions & Receipt Modal Trigger -->
                             <div class="col-lg-4 text-lg-end">
                                <div class="d-flex flex-column gap-2 align-items-lg-end">
                                   <?php if (!empty($receipt)): ?>
                                      <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold mb-1" data-bs-toggle="modal" data-bs-target="#receiptModal<?php echo $oid; ?>">
                                         <i class="fas fa-file-invoice me-1"></i>View Payment Slip
                                      </button>
                                   <?php else: ?>
                                      <span class="text-muted small mb-2"><i class="fas fa-exclamation-triangle text-warning me-1"></i>No Slip File Attached</span>
                                   <?php endif; ?>

                                   <div class="d-flex gap-2 w-100 justify-content-lg-end">
                                      <form method="POST" action="admin_orders.php" style="display:inline;">
                                         <input type="hidden" name="Order_ID" value="<?php echo $oid; ?>">
                                         <button type="submit" name="accept_order" class="btn btn-success rounded-pill px-4 fw-bold"><i class="fas fa-check me-1"></i>Accept</button>
                                      </form>

                                      <form method="POST" action="admin_orders.php" style="display:inline;" onsubmit="return confirm('Reject Order #<?php echo $oid; ?>?');">
                                         <input type="hidden" name="Order_ID" value="<?php echo $oid; ?>">
                                         <button type="submit" name="reject_order" class="btn btn-outline-danger rounded-pill px-3 fw-bold"><i class="fas fa-times me-1"></i>Reject</button>
                                      </form>
                                   </div>
                                </div>
                             </div>

                          </div>
                       </div>
                    </div>

                    <!-- MODAL FOR PAYMENT SLIP PREVIEW -->
                    <?php if (!empty($receipt)): ?>
                       <div class="modal fade text-start" id="receiptModal<?php echo $oid; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-lg">
                             <div class="modal-content rounded-4 border-0">
                                <div class="modal-header border-bottom bg-light">
                                   <h5 class="modal-title fw-bold text-dark mb-0" style="font-family:'Playfair Display', serif;">
                                      <i class="fas fa-receipt text-danger me-2"></i>Payment Receipt Slip - Order #<?php echo $oid; ?>
                                   </h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                   <?php
                                   $ext = strtolower(pathinfo($receipt, PATHINFO_EXTENSION));
                                   if ($ext === 'pdf'):
                                   ?>
                                      <iframe src="<?php echo htmlspecialchars($receipt); ?>" style="width:100%; height:420px; border:none;"></iframe>
                                   <?php else: ?>
                                      <img src="<?php echo htmlspecialchars($receipt); ?>" alt="Payment Slip" class="img-fluid rounded shadow" style="max-height: 420px; object-fit: contain;">
                                   <?php endif; ?>

                                   <div class="d-flex justify-content-center gap-3 mt-4 pt-3 border-top">
                                      <form method="POST" action="admin_orders.php">
                                         <input type="hidden" name="Order_ID" value="<?php echo $oid; ?>">
                                         <button type="submit" name="accept_order" class="btn btn-success rounded-pill px-4 fw-bold"><i class="fas fa-check me-1"></i>Accept Order</button>
                                      </form>

                                      <form method="POST" action="admin_orders.php" onsubmit="return confirm('Reject Order #<?php echo $oid; ?>?');">
                                         <input type="hidden" name="Order_ID" value="<?php echo $oid; ?>">
                                         <button type="submit" name="reject_order" class="btn btn-outline-danger rounded-pill px-4 fw-bold"><i class="fas fa-times me-1"></i>Reject Order</button>
                                      </form>
                                   </div>
                                </div>
                             </div>
                          </div>
                       </div>
                    <?php endif; ?>
                    <?php
                }
            } else {
                ?>
                <div class="col-12">
                   <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
                      <div class="mb-3">
                         <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                      </div>
                      <h4 class="fw-bold text-dark mb-2" style="font-family:'Playfair Display', serif;">No Pending Orders</h4>
                      <p class="text-secondary mb-0">There are no customer payment receipts waiting for verification right now.</p>
                   </div>
                </div>
                <?php
            }
            ?>
         </div>

      </div>
   </section>

   <!-- JS Libraries -->
   <script src="js/jquery-3.7.1.min.js"></script>
   <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
