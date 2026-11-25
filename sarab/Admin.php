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

//for security
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'config.php';

$action_msg = "";
$action_target = "";

$product_action_msg = "";
$category_action_msg = "";
$order_action_msg = "";

// Product Catalogue Management Table Handlers
if (isset($_POST['insert'])) {
    $action_target = 'productSection';
    $con = getDBConnection() or die("couldn't connect to the server");
    $Product_Name = mysqli_real_escape_string($con, $_POST['Product_Name']);
    $category     = mysqli_real_escape_string($con, $_POST['Category']);
    $price        = mysqli_real_escape_string($con, $_POST['Price']);
    $old_price    = mysqli_real_escape_string($con, $_POST['Old_Price']);
    $image        = mysqli_real_escape_string($con, $_POST['Image']);
    $desc         = mysqli_real_escape_string($con, $_POST['Description']);
    $tags         = mysqli_real_escape_string($con, $_POST['Tags']);

    if (isset($_FILES['product_image_file']) && $_FILES['product_image_file']['error'] == 0) {
        $target_dir = "uploads/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["product_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["product_image_file"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }
    if (empty($image)) {
        $image = 'img/logo.png.jpeg';
    }

    $query = mysqli_query($con, "INSERT INTO products(Product_Name,Category,Price_LKR,Old_Price_LKR,Image,Description,Tags) VALUES('$Product_Name','$category','$price','$old_price','$image','$desc','$tags')");

    if ($query) {
        $product_action_msg = "<div class='alert alert-success alert-dismissible fade show mb-3' role='alert'><i class='fas fa-check-circle me-2'></i>Product Successfully Inserted!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $product_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'>No product added: " . mysqli_error($con) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

if (isset($_POST['update'])) {
    $action_target = 'productSection';
    $con = getDBConnection() or die("couldn't connect to the server");
    $pid          = intval($_POST['Product_ID']);
    $Product_Name = mysqli_real_escape_string($con, $_POST['Product_Name']);
    $category     = mysqli_real_escape_string($con, $_POST['Category']);
    $price        = mysqli_real_escape_string($con, $_POST['Price']);
    $old_price    = mysqli_real_escape_string($con, $_POST['Old_Price']);
    $image        = mysqli_real_escape_string($con, $_POST['Image']);
    $desc         = mysqli_real_escape_string($con, $_POST['Description']);
    $tags         = mysqli_real_escape_string($con, $_POST['Tags']);

    if (isset($_FILES['product_image_file']) && $_FILES['product_image_file']['error'] == 0) {
        $target_dir = "uploads/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["product_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["product_image_file"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }

    if (empty($image)) {
        $cur_res = mysqli_query($con, "SELECT Image FROM products WHERE Product_ID='$pid'");
        if ($cur_res && $cur_row = mysqli_fetch_assoc($cur_res)) {
            $image = $cur_row['Image'];
        }
    }

    $query = mysqli_query($con, "UPDATE products SET Product_Name='$Product_Name', Category='$category', Price_LKR='$price', Old_Price_LKR='$old_price', Image='$image', Description='$desc', Tags='$tags' WHERE Product_ID='$pid'");

    if ($query) {
        $product_action_msg = "<div class='alert alert-success alert-dismissible fade show mb-3' role='alert'><i class='fas fa-check-circle me-2'></i>Product Successfully Updated!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $product_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'>No product updated: " . mysqli_error($con) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

if (isset($_POST['delete'])) {
    $action_target = 'productSection';
    $pid = $_POST['Product_ID'];
    $con = getDBConnection() or die("couldn't connect to the server");
    $query = mysqli_query($con, "SELECT * FROM products WHERE Product_ID='".$pid."'");
    $nor = mysqli_num_rows($query);

    if ($nor < 1) {
        $product_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'>Invalid Product Entry<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $query = mysqli_query($con, "DELETE FROM products WHERE Product_ID='".$pid."'");
        if ($query > 0) {
            $product_action_msg = "<div class='alert alert-success alert-dismissible fade show mb-3' role='alert'><i class='fas fa-trash-alt me-2'></i>Product Successfully Deleted!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $product_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'>No product deleted: " . mysqli_error($con) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
}

if (isset($_GET['search_pid']) && $_GET['search_pid'] !== '') {
    $action_target = 'productSection';
}

// Category Management Handlers
if (isset($_POST['insert_category'])) {
    $action_target = 'categorySection';
    $con = getDBConnection() or die("couldn't connect to the server");
    $cat_name   = mysqli_real_escape_string($con, $_POST['Category_Name']);
    $cat_filter = mysqli_real_escape_string($con, strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['Category_Name'])));
    $cat_image  = mysqli_real_escape_string($con, $_POST['Category_Image']);
    $cat_desc   = mysqli_real_escape_string($con, $_POST['Category_Description']);

    if (isset($_FILES['category_image_file']) && $_FILES['category_image_file']['error'] == 0) {
        $target_dir = "uploads/categories/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["category_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["category_image_file"]["tmp_name"], $target_file)) {
            $cat_image = $target_file;
        }
    }
    if (empty($cat_image)) {
        $cat_image = 'img/category/1.jpg';
    }

    $q = mysqli_query($con, "INSERT INTO categories (Category_Name, Category_Image, Category_Filter, Description) VALUES ('$cat_name', '$cat_image', '$cat_filter', '$cat_desc')");
    if ($q) {
        $category_action_msg = "<div class='alert alert-success alert-dismissible fade show mb-3' role='alert'><i class='fas fa-check-circle me-2'></i>Category '$cat_name' Successfully Inserted!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $category_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'>Error Adding Category: " . mysqli_error($con) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

if (isset($_POST['update_category'])) {
    $action_target = 'categorySection';
    $con = getDBConnection() or die("couldn't connect to the server");
    $cid        = intval($_POST['Category_ID']);
    $cat_name   = mysqli_real_escape_string($con, $_POST['Category_Name']);
    $cat_filter = mysqli_real_escape_string($con, strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['Category_Name'])));
    $cat_image  = mysqli_real_escape_string($con, $_POST['Category_Image']);
    $cat_desc   = mysqli_real_escape_string($con, $_POST['Category_Description']);

    if (isset($_FILES['category_image_file']) && $_FILES['category_image_file']['error'] == 0) {
        $target_dir = "uploads/categories/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["category_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["category_image_file"]["tmp_name"], $target_file)) {
            $cat_image = $target_file;
        }
    }
    if (empty($cat_image)) {
        $cq = mysqli_query($con, "SELECT Category_Image FROM categories WHERE Category_ID='$cid'");
        if ($cq && $cr = mysqli_fetch_assoc($cq)) {
            $cat_image = $cr['Category_Image'];
        }
    }

    $q = mysqli_query($con, "UPDATE categories SET Category_Name='$cat_name', Category_Image='$cat_image', Category_Filter='$cat_filter', Description='$cat_desc' WHERE Category_ID='$cid'");
    if ($q) {
        $category_action_msg = "<div class='alert alert-success alert-dismissible fade show mb-3' role='alert'><i class='fas fa-check-circle me-2'></i>Category #$cid Successfully Updated!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $category_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'>Error Updating Category: " . mysqli_error($con) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

if (isset($_POST['delete_category'])) {
    $action_target = 'categorySection';
    $cid = intval($_POST['Category_ID']);
    $con = getDBConnection() or die("couldn't connect to the server");
    $q = mysqli_query($con, "DELETE FROM categories WHERE Category_ID='$cid'");
    if ($q) {
        $category_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'><i class='fas fa-trash-alt me-2'></i>Category #$cid removed from database!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Order Management Handlers
if (isset($_POST['delete_order']) || isset($_POST['reject_order'])) {
    $action_target = 'ordersSection';
    $order_id = intval($_POST['Order_ID']);
    $con = getDBConnection() or die("couldn't connect to the server");
    $query = mysqli_query($con, "DELETE FROM orders WHERE Order_ID='$order_id'");
    if ($query) {
        $order_action_msg = "<div class='alert alert-danger alert-dismissible fade show mb-3' role='alert'><i class='fas fa-trash-alt me-2'></i>Order #$order_id removed!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

if ((isset($_GET['search_oid']) && $_GET['search_oid'] !== '') || isset($_GET['date_range']) || isset($_GET['status_filter']) || isset($_GET['page'])) {
    $action_target = 'ordersSection';
}

if (isset($_GET['search_uid']) && $_GET['search_uid'] !== '') {
    $action_target = 'userLookupSection';
}

// Global Pending Orders Count for Nav Badges
$con_nav = getDBConnection();
$nav_pending_cnt = 0;
if ($con_nav) {
    $p_res = mysqli_query($con_nav, "SELECT COUNT(*) AS cnt FROM orders WHERE LOWER(Status)='pending' OR LOWER(Status)='payment uploaded' OR Status IS NULL OR Status=''");
    if ($p_res && $p_row = mysqli_fetch_assoc($p_res)) {
        $nav_pending_cnt = intval($p_row['cnt']);
    }
}

// Logout handler
if (isset($_POST['logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Admin Control Panel - GlamHandy Arts & Crafts</title>
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
   <!-- Bootstrap 5.3 -->
   <link href="css/bootstrap.min.css" rel="stylesheet" />
   <!-- Font Awesome Icons -->
   <link rel="stylesheet" href="css/all.min.css" />
   <!-- Style CSS -->
   <link rel="stylesheet" href="css/style.css" />
   <!-- Chart.js Library -->
   <script src="https://cdn.jsdelivr.net/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

   <script>
   document.addEventListener('DOMContentLoaded', function() {
       <?php if (!empty($action_target)): ?>
          const targetEl = document.getElementById('<?php echo $action_target; ?>');
          if (targetEl) {
             targetEl.scrollIntoView({ behavior: 'instant', block: 'start' });
          }
       <?php endif; ?>
   });
   window.addEventListener("pageshow", function (event) {
       if (event.persisted) {
           window.location.reload();
       }
   });
   </script>

   <style>
      .admin-table img {
         width: 48px;
         height: 48px;
         border-radius: 8px;
         object-fit: cover;
      }
      .badge-tag {
         background: #ffeef0;
         color: var(--primary);
         font-size: 0.75rem;
         padding: 3px 8px;
         border-radius: 12px;
         margin-right: 4px;
         display: inline-block;
      }
      .nav-pills-admin .nav-link {
         border-radius: 20px;
         color: #555;
         font-weight: 500;
         padding: 6px 18px;
         font-size: 0.85rem;
      }
      .nav-pills-admin .nav-link.active {
         background-color: var(--primary);
         color: #fff;
         box-shadow: 0 4px 12px rgba(232, 40, 26, 0.25);
      }
      .visual-bar-chart {
         display: flex;
         align-items: flex-end;
         justify-content: space-between;
         gap: 10px;
         height: 220px;
         padding-top: 30px;
         border-bottom: 2px dashed #f0d8dc;
         margin-bottom: 10px;
      }
      .vbar-item {
         flex: 1;
         display: flex;
         flex-direction: column;
         align-items: center;
         height: 100%;
         justify-content: flex-end;
      }
      .vbar-pill {
         width: 100%;
         max-width: 38px;
         background: linear-gradient(180deg, var(--primary) 0%, #ff758c 100%);
         border-radius: 8px 8px 0 0;
         transition: height 0.4s ease, background 0.3s ease;
         position: relative;
         cursor: pointer;
      }
      .vbar-pill:hover {
         background: linear-gradient(180deg, #d01d10 0%, #ff5277 100%);
      }
      .vbar-value {
         font-size: 0.7rem;
         font-weight: 700;
         color: var(--primary);
         margin-bottom: 4px;
      }
      .vbar-label {
         font-size: 0.78rem;
         color: #666;
         margin-top: 8px;
         font-weight: 600;
      }
      .cat-progress {
         height: 10px;
         border-radius: 10px;
         background: #eee;
         overflow: hidden;
         margin-top: 6px;
         margin-bottom: 16px;
      }
      .cat-progress-fill {
         height: 100%;
         border-radius: 10px;
         transition: width 0.5s ease;
      }
   </style>
</head>

<body>
   <?php
   $con_nav = getDBConnection();
   $nav_pending_cnt = 0;
   if ($con_nav) {
       $nav_res = mysqli_query($con_nav, "SELECT COUNT(*) AS cnt FROM orders WHERE LOWER(Status)='payment uploaded'");
       if ($nav_res && $nav_r = mysqli_fetch_assoc($nav_res)) {
           $nav_pending_cnt = intval($nav_r['cnt']);
       }
   }
   ?>
   <nav class="navbar navbar-expand-lg" id="nav" style="background: var(--dark); padding: 15px 0;">
      <div class="container">
         <a class="navbar-brand" href="index.html">
            <div class="blogo">
               <div>
                  <div class="bname" style="color: #fff;">Glam<span style="color: #ff00b6;">Handy</span></div>
                  <div class="bsub" style="color: #ccc;">Admin Control Panel</div>
               </div>
            </div>
         </a>
         <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="index.html" class="btn btn-outline-light btn-sm rounded-pill px-3 me-1"><i class="fas fa-home me-1"></i>Home</a>
            <a href="catalog.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-1"><i class="fas fa-store me-1"></i>Catalog</a>
            <a href="admin_orders.php" class="btn btn-danger btn-sm rounded-pill px-3 position-relative me-1">
               <i class="fas fa-bell me-1"></i>Pending Orders
               <?php if ($nav_pending_cnt > 0): ?>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-light">
                     <?php echo $nav_pending_cnt; ?>
                  </span>
               <?php endif; ?>
            </a>
            <span id="adminWelcome" class="fw-semibold" style="color: #fff; font-size:1.05rem;">
               <i class="fas fa-user-shield me-1 text-danger"></i> logged as: <?php echo $_SESSION["email"]; ?>
            </span>
            <form method="POST" action="Admin.php" style="display:inline;">
               <button type="submit" name="logout" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
            </form>
         </div>
      </div>
   </nav>

   <?php echo $action_msg; ?>

   <section class="admin-section" style="padding: 40px 0; background: #f8f9fa; min-height: 85vh;">
      <div class="container">

         <!-- STATS CARDS -->
         <?php
         $con = getDBConnection() or die("couldn't connect to the server");
         $p_cnt_res = mysqli_query($con, "SELECT COUNT(*) AS cnt FROM products");
         $p_cnt = ($p_cnt_res && $p_row = mysqli_fetch_assoc($p_cnt_res)) ? $p_row['cnt'] : 0;

         $o_cnt_res = mysqli_query($con, "SELECT COUNT(*) AS cnt FROM orders");
         $o_cnt = ($o_cnt_res && $o_row = mysqli_fetch_assoc($o_cnt_res)) ? $o_row['cnt'] : 0;

         $u_cnt_res = mysqli_query($con, "SELECT COUNT(*) AS cnt FROM users");
         $u_cnt = ($u_cnt_res && $u_row = mysqli_fetch_assoc($u_cnt_res)) ? $u_row['cnt'] : 0;

         // Get total revenue
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
         $rev_res = mysqli_query($con, "SELECT SUM(`$price_col`) AS rev FROM orders");
         $rev_val = ($rev_res && $r_row = mysqli_fetch_assoc($rev_res)) ? floatval($r_row['rev']) : 0;
         ?>
         <div class="row g-4 mb-5">
            <div class="col-md-3">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body text-center py-4">
                     <i class="fas fa-boxes fa-3x mb-3" style="color: var(--primary);"></i>
                     <h5 class="card-title text-secondary">Total Products</h5>
                     <h2 class="fw-bold" id="totalProductsCount"><?php echo $p_cnt; ?></h2>
                  </div>
               </div>
            </div>
            <div class="col-md-3">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body text-center py-4">
                     <i class="fas fa-shopping-bag fa-3x mb-3" style="color: var(--primary);"></i>
                     <h5 class="card-title text-secondary">Total Orders</h5>
                     <h2 class="fw-bold"><?php echo $o_cnt; ?></h2>
                  </div>
               </div>
            </div>
            <div class="col-md-3">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body text-center py-4">
                     <i class="fas fa-users fa-3x mb-3" style="color: var(--primary);"></i>
                     <h5 class="card-title text-secondary">Registered Users</h5>
                     <h2 class="fw-bold"><?php echo $u_cnt; ?></h2>
                  </div>
               </div>
            </div>
            <div class="col-md-3">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body text-center py-4">
                     <i class="fas fa-dollar-sign fa-3x mb-3" style="color: var(--primary);"></i>
                     <h5 class="card-title text-secondary">Revenue</h5>
                     <h2 class="fw-bold">Rs. <?php echo number_format($rev_val, 2); ?></h2>
                  </div>
               </div>
            </div>
         </div>

         <!-- ANALYTICS CHARTS SECTION -->
         <div class="row g-4 mb-5">
            <div class="col-lg-8">
               <div class="card shadow-sm border-0 rounded-4 h-100">
                  <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                        <div>
                           <h4 class="fw-bold mb-0" style="font-family:'Playfair Display', serif;"><i class="fas fa-chart-line me-2 text-danger"></i>Revenue Analytics</h4>
                           <p class="text-muted small mb-0">Track revenue growth over months, quarters, and years.</p>
                        </div>
                        <ul class="nav nav-pills nav-pills-admin" id="revenueChartTabs">
                           <li class="nav-item"><button class="nav-link active" onclick="updateVisualRevenueChart('monthly', this)">Monthly</button></li>
                           <li class="nav-item"><button class="nav-link" onclick="updateVisualRevenueChart('quarterly', this)">Quarterly</button></li>
                           <li class="nav-item"><button class="nav-link" onclick="updateVisualRevenueChart('yearly', this)">Yearly</button></li>
                        </ul>
                     </div>

                     <div class="row g-2 mb-3 text-center">
                        <div class="col-6">
                           <div class="p-2 rounded-3" style="background:#fff5f6;">
                              <small class="text-muted d-block">Peak Revenue</small>
                              <strong class="text-danger" id="peakRevVal">Rs. 24,500.00</strong>
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="p-2 rounded-3" style="background:#f0f7ff;">
                              <small class="text-muted d-block">Average Sales</small>
                              <strong class="text-primary" id="avgRevVal">Rs. 16,400.00</strong>
                           </div>
                        </div>
                     </div>

                     <div id="visualBarChartContainer" class="visual-bar-chart">
                        <!-- Dynamically Populated by js/admin.js -->
                     </div>
                  </div>
               </div>
            </div>

            <div class="col-lg-4">
               <div class="card shadow-sm border-0 rounded-4 h-100">
                  <div class="card-body p-4">
                     <h4 class="fw-bold mb-1" style="font-family:'Playfair Display', serif;"><i class="fas fa-chart-pie me-2 text-danger"></i>Sales by Craft Category</h4>
                     <p class="text-muted small mb-3">Database breakdown tallies matching orders table.</p>

                     <?php
                     $con = getDBConnection();
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

                     $cat_totals = [
                         'Yarn & Crochet'   => 0,
                         'Beads & Jewellery'=> 0,
                         'Bag Accessories'  => 0,
                         'Craft Tools'      => 0,
                         'Craft Materials'  => 0
                     ];
                     $total_cat_rev = 0;

                     $cat_calc_res = mysqli_query($con, "SELECT o.`$price_col` AS Total_Price, p.Category FROM orders o LEFT JOIN products p ON o.Product_ID = p.Product_ID");
                     if ($cat_calc_res && mysqli_num_rows($cat_calc_res) > 0) {
                         while ($r = mysqli_fetch_assoc($cat_calc_res)) {
                             $cat_name = !empty($r['Category']) ? $r['Category'] : 'Craft Materials';
                             $tot_val  = floatval($r['Total_Price']);
                             if (!isset($cat_totals[$cat_name])) {
                                 $cat_totals[$cat_name] = 0;
                             }
                             $cat_totals[$cat_name] += $tot_val;
                             $total_cat_rev += $tot_val;
                         }
                     }

                     // Fallback if no order history yet: calculate from catalog products table count
                     if ($total_cat_rev == 0) {
                         $p_cat_res = mysqli_query($con, "SELECT Category, COUNT(*) as cnt FROM products GROUP BY Category");
                         if ($p_cat_res) {
                             while ($pr = mysqli_fetch_assoc($p_cat_res)) {
                                 $cn = !empty($pr['Category']) ? $pr['Category'] : 'Craft Materials';
                                 $cnt = intval($pr['cnt']);
                                 $val = $cnt * 1000;
                                 $cat_totals[$cn] = $val;
                                 $total_cat_rev += $val;
                             }
                         }
                     }

                     $cat_colors = [
                         'Yarn & Crochet'    => '#e8281a',
                         'Beads & Jewellery' => '#1a73e8',
                         'Bag Accessories'   => '#fbbc04',
                         'Craft Tools'       => '#34a853',
                         'Craft Materials'   => '#9c27b0'
                     ];

                     foreach ($cat_totals as $cat_key => $cat_val): 
                         $pct = $total_cat_rev > 0 ? round(($cat_val / $total_cat_rev) * 100) : 0;
                         $color = isset($cat_colors[$cat_key]) ? $cat_colors[$cat_key] : '#e8281a';
                     ?>
                        <div class="mb-3">
                           <div class="d-flex justify-content-between small fw-bold">
                              <span><i class="fas fa-circle me-1" style="color:<?php echo $color; ?>;"></i><?php echo htmlspecialchars($cat_key); ?></span>
                              <span><?php echo $pct; ?>% (Rs. <?php echo number_format($cat_val, 2); ?>)</span>
                           </div>
                           <div class="cat-progress"><div class="cat-progress-fill" style="width:<?php echo $pct; ?>%; background:<?php echo $color; ?>;"></div></div>
                        </div>
                     <?php endforeach; ?>

                  </div>
               </div>
            </div>
         </div>

         <!-- SECTION QUICK NAVIGATION BUTTONS -->
         <div class="row mb-5">
            <div class="col-12">
               <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                  <h6 class="fw-bold text-secondary mb-3 text-uppercase" style="letter-spacing:1px; font-size:0.8rem;">
                     <i class="fas fa-compass me-2 text-danger"></i>Quick Section Navigation
                  </h6>
                  <div class="d-flex align-items-center justify-content-around flex-wrap gap-3">
                     <a href="#categorySection" class="btn btn-outline-danger rounded-pill px-4 py-3 fw-bold fs-6 flex-fill text-center shadow-sm">
                        <i class="fas fa-th-large me-2"></i>Category Management
                     </a>
                     <a href="#productSection" class="btn btn-outline-danger rounded-pill px-4 py-3 fw-bold fs-6 flex-fill text-center shadow-sm">
                        <i class="fas fa-tags me-2"></i>Product Management
                     </a>
                     <a href="#ordersSection" class="btn btn-outline-danger rounded-pill px-4 py-3 fw-bold fs-6 flex-fill text-center shadow-sm">
                        <i class="fas fa-shopping-bag me-2"></i>Orders Management
                     </a>
                  </div>
               </div>
            </div>
         </div>

         <!-- CATEGORY MANAGEMENT SECTION -->
         <div class="row mb-5" id="categorySection">
            <div class="col-12">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-3 border-bottom">
                        <div>
                           <h4 class="fw-bold mb-0" style="font-family:'Playfair Display', serif;"><i class="fas fa-th-large me-2 text-danger"></i>Category Management</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                           <a href="category.php" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-1"><i class="fas fa-external-link-alt me-1"></i>View Category Page</a>
                           <button class="btn btn-danger rounded-pill px-4 py-2" onclick="openAddCategoryModal()">
                              <i class="fas fa-plus-circle me-2"></i>Add New Category
                           </button>
                        </div>
                     </div>

                     <?php if (!empty($category_action_msg)) echo $category_action_msg; ?>

                     <div class="table-responsive">
                        <table class="table table-hover align-middle admin-table">
                           <thead class="table-light">
                              <tr>
                                 <th>Category ID</th>
                                 <th>Image</th>
                                 <th>Category Name</th>
                                 <th>Description</th>
                                 <th class="text-end">Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              $con = getDBConnection();
                              $cat_q = mysqli_query($con, "SELECT * FROM categories ORDER BY Category_ID ASC");
                              if ($cat_q && mysqli_num_rows($cat_q) > 0) {
                                  while ($cat_row = mysqli_fetch_assoc($cat_q)) {
                                      $cid      = $cat_row['Category_ID'];
                                      $cname    = $cat_row['Category_Name'];
                                      $cimg     = !empty($cat_row['Category_Image']) ? $cat_row['Category_Image'] : 'img/category/1.jpg';
                                      $cdesc    = $cat_row['Description'];
                                      ?>
                                      <tr>
                                         <td class="fw-semibold text-muted">#<?php echo $cid; ?></td>
                                         <td><img src="<?php echo htmlspecialchars($cimg); ?>" alt="<?php echo htmlspecialchars($cname); ?>" onerror="this.src='img/category/1.jpg';" style="width:48px;height:48px;border-radius:8px;object-fit:cover;"></td>
                                         <td class="fw-bold text-dark"><?php echo htmlspecialchars($cname); ?></td>
                                         <td class="text-muted small"><?php echo htmlspecialchars($cdesc ? $cdesc : '-'); ?></td>
                                         <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                               data-id="<?php echo $cid; ?>"
                                               data-name="<?php echo htmlspecialchars($cname, ENT_QUOTES); ?>"
                                               data-image="<?php echo htmlspecialchars($cimg, ENT_QUOTES); ?>"
                                               data-desc="<?php echo htmlspecialchars($cdesc, ENT_QUOTES); ?>"
                                               onclick="openEditCategoryModalFromBtn(this)">
                                               <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" action="Admin.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete category <?php echo addslashes($cname); ?>?');">
                                               <input type="hidden" name="Category_ID" value="<?php echo $cid; ?>">
                                               <button type="submit" name="delete_category" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                         </td>
                                      </tr>
                                      <?php
                                  }
                              } else {
                                  echo "<tr><td colspan='5' class='text-center py-4 text-muted'><i class='fas fa-info-circle me-1'></i>No categories found in database. Click 'Add New Category' to add one!</td></tr>";
                              }
                              ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- PRODUCT MANAGEMENT SECTION -->
         <div class="row mb-5" id="productSection">
            <div class="col-12">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body p-4">
                     <?php
                     $con = getDBConnection() or die("couldn't connect to the server");
                     
                     // Product Filters & Pagination Setup
                     $search_pid  = isset($_GET['search_pid']) ? intval($_GET['search_pid']) : 0;
                     $filter_pcat = isset($_GET['filter_pcat']) ? $_GET['filter_pcat'] : 'all';
                     $p_page      = isset($_GET['p_page']) ? max(1, intval($_GET['p_page'])) : 1;
                     $p_limit     = 20;
                     $p_offset    = ($p_page - 1) * $p_limit;

                     // Fetch distinct categories for the dropdown filter
                     $cat_opt_res = mysqli_query($con, "SELECT Category_Name FROM categories UNION SELECT DISTINCT Category AS Category_Name FROM products WHERE Category IS NOT NULL AND Category != '' ORDER BY Category_Name ASC");

                     $p_where_clauses = array();
                     if ($search_pid > 0) {
                         $p_where_clauses[] = "Product_ID = '$search_pid'";
                     }
                     if ($filter_pcat !== 'all' && !empty($filter_pcat)) {
                         $safe_pcat = mysqli_real_escape_string($con, $filter_pcat);
                         $p_where_clauses[] = "Category = '$safe_pcat'";
                     }

                     $p_where_sql = count($p_where_clauses) > 0 ? " WHERE " . implode(" AND ", $p_where_clauses) : "";

                     // Total products count for pagination
                     $p_count_res    = mysqli_query($con, "SELECT COUNT(*) AS cnt FROM products $p_where_sql");
                     $total_products = ($p_count_res && $p_crow = mysqli_fetch_assoc($p_count_res)) ? intval($p_crow['cnt']) : 0;
                     $p_total_pages  = max(1, ceil($total_products / $p_limit));
                     if ($p_page > $p_total_pages) $p_page = $p_total_pages;
                     $p_offset = ($p_page - 1) * $p_limit;

                     // Fetch 20 products for current page ordered by Product_ID DESC
                     $products_query = mysqli_query($con, "SELECT * FROM products $p_where_sql ORDER BY Product_ID DESC LIMIT $p_limit OFFSET $p_offset");
                     ?>

                     <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom">
                        <div>
                           <h4 class="fw-bold mb-1" style="font-family:'Playfair Display', serif;"><i class="fas fa-tags me-2 text-danger"></i>Product Catalog Management</h4>
                           <small class="text-muted">Showing <strong><?php echo $total_products > 0 ? min($total_products, $p_offset + 1) : 0; ?>-<?php echo min($total_products, $p_offset + $p_limit); ?></strong> of <strong><?php echo $total_products; ?></strong> total products</small>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                           <form method="GET" action="Admin.php#productSection" class="d-flex align-items-center gap-2 flex-wrap">
                              <!-- Search Product ID -->
                              <div class="input-group input-group-sm" style="width: 170px;">
                                 <input type="number" name="search_pid" class="form-control rounded-start-pill" placeholder="Product ID..." value="<?php echo $search_pid > 0 ? $search_pid : ''; ?>">
                                 <button type="submit" class="btn btn-outline-danger rounded-end-pill"><i class="fas fa-search"></i></button>
                              </div>

                              <!-- Category Filter -->
                              <select name="filter_pcat" class="form-select form-select-sm rounded-pill" style="width: 175px;" onchange="this.form.submit()">
                                 <option value="all" <?php if ($filter_pcat === 'all') echo 'selected'; ?>>All Categories</option>
                                 <?php
                                 if ($cat_opt_res && mysqli_num_rows($cat_opt_res) > 0) {
                                     while ($cat_opt = mysqli_fetch_assoc($cat_opt_res)) {
                                         $cn = $cat_opt['Category_Name'];
                                         $sel = ($filter_pcat === $cn) ? 'selected' : '';
                                         echo "<option value='" . htmlspecialchars($cn, ENT_QUOTES) . "' $sel>" . htmlspecialchars($cn) . "</option>";
                                     }
                                 }
                                 ?>
                              </select>

                              <?php if ($search_pid > 0 || $filter_pcat !== 'all'): ?>
                                 <a href="Admin.php#productSection" class="btn btn-light btn-sm rounded-pill text-muted px-2" title="Reset Filters"><i class="fas fa-undo me-1"></i>Reset</a>
                              <?php endif; ?>

                              <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm ms-1" onclick="openAddProductModal()">
                                 <i class="fas fa-plus-circle me-1"></i>Add New Product
                              </button>
                           </form>
                        </div>
                     </div>

                     <?php if (!empty($product_action_msg)) echo $product_action_msg; ?>

                     <div class="sticky-table-container border rounded-3 mb-3">
                         <table class="table table-hover align-middle admin-table mb-0">
                            <thead class="table-light">
                               <tr>
                                  <th>Product ID</th>
                                  <th>Image</th>
                                  <th>Product Name</th>
                                  <th>Category</th>
                                  <th>Current Price (LKR)</th>
                                  <th>Old Price (LKR)</th>
                                  <th>Tags</th>
                                  <th class="text-end">Actions</th>
                               </tr>
                            </thead>
                            <tbody id="adminProductTableBody">
                               <?php
                               if ($products_query && mysqli_num_rows($products_query) > 0) {
                                   while ($row = mysqli_fetch_assoc($products_query)) {
                                       $pid          = $row['Product_ID'];
                                       $Product_Name = $row['Product_Name'];
                                       $cat          = $row['Category'];
                                       $price        = $row['Price_LKR'];
                                       $old_price    = $row['Old_Price_LKR'];
                                       $image        = $row['Image'];
                                       $desc         = $row['Description'];
                                       $tags         = $row['Tags'];
                                       ?>
                                       <tr>
                                          <td class="fw-semibold text-muted">#<?php echo $pid; ?></td>
                                          <td><img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($Product_Name, ENT_QUOTES); ?>" onerror="this.src='img/logo.png.jpeg';" style="width:48px;height:48px;border-radius:8px;object-fit:cover;"></td>
                                          <td class="fw-bold"><?php echo htmlspecialchars($Product_Name); ?></td>
                                          <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($cat); ?></span></td>
                                          <td class="fw-bold text-danger"><?php echo htmlspecialchars($price); ?></td>
                                          <td class="text-muted text-decoration-line-through"><?php echo $old_price ? htmlspecialchars($old_price) : '-'; ?></td>
                                          <td>
                                             <?php
                                             if (!empty($tags)) {
                                                 $tagArray = explode(',', $tags);
                                                 foreach ($tagArray as $t) {
                                                     echo "<span class='badge-tag'>" . htmlspecialchars(trim($t)) . "</span>";
                                                 }
                                             } else {
                                                 echo "-";
                                             }
                                             ?>
                                          </td>
                                          <td class="text-end">
                                              <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                 data-id="<?php echo $pid; ?>" 
                                                 data-title="<?php echo htmlspecialchars($Product_Name, ENT_QUOTES); ?>" 
                                                 data-cat="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>" 
                                                 data-price="<?php echo htmlspecialchars($price, ENT_QUOTES); ?>" 
                                                 data-oldprice="<?php echo htmlspecialchars($old_price, ENT_QUOTES); ?>" 
                                                 data-img="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" 
                                                 data-desc="<?php echo htmlspecialchars($desc, ENT_QUOTES); ?>" 
                                                 data-tags="<?php echo htmlspecialchars($tags, ENT_QUOTES); ?>" 
                                                 onclick="openEditProductModalFromBtn(this)">
                                                 <i class="fas fa-edit"></i> Edit
                                              </button>
                                             <form method="POST" action="Admin.php#productSection" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                <input type="hidden" name="Product_ID" value="<?php echo $pid; ?>">
                                                <button type="submit" name="delete" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
                                             </form>
                                          </td>
                                       </tr>
                                       <?php
                                   }
                               } else {
                                   echo "<tr><td colspan='8' class='text-center py-4 text-muted'><i class='fas fa-info-circle me-1'></i>No products found matching criteria.</td></tr>";
                               }
                              ?>
                            </tbody>
                         </table>
                      </div>

                      <?php if ($p_total_pages > 1): ?>
                      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                         <div class="small text-muted">
                            Page <strong><?php echo $p_page; ?></strong> of <strong><?php echo $p_total_pages; ?></strong>
                         </div>
                         <nav>
                            <ul class="pagination pagination-sm mb-0">
                               <li class="page-item <?php if ($p_page <= 1) echo 'disabled'; ?>">
                                  <a class="page-link rounded-start-pill" href="Admin.php?search_pid=<?php echo $search_pid; ?>&filter_pcat=<?php echo urlencode($filter_pcat); ?>&p_page=<?php echo $p_page - 1; ?>#productSection">
                                     <i class="fas fa-chevron-left me-1"></i>Prev
                                  </a>
                               </li>

                               <?php
                               $start_p = max(1, $p_page - 2);
                               $end_p   = min($p_total_pages, $p_page + 2);
                               for ($i = $start_p; $i <= $end_p; $i++):
                               ?>
                                  <li class="page-item <?php if ($i == $p_page) echo 'active'; ?>">
                                     <a class="page-link" href="Admin.php?search_pid=<?php echo $search_pid; ?>&filter_pcat=<?php echo urlencode($filter_pcat); ?>&p_page=<?php echo $i; ?>#productSection"><?php echo $i; ?></a>
                                  </li>
                               <?php endfor; ?>

                               <li class="page-item <?php if ($p_page >= $p_total_pages) echo 'disabled'; ?>">
                                  <a class="page-link rounded-end-pill" href="Admin.php?search_pid=<?php echo $search_pid; ?>&filter_pcat=<?php echo urlencode($filter_pcat); ?>&p_page=<?php echo $p_page + 1; ?>#productSection">
                                     Next<i class="fas fa-chevron-right ms-1"></i>
                                  </a>
                               </li>
                            </ul>
                         </nav>
                      </div>
                      <?php endif; ?>
                  </div>
               </div>
            </div>
         </div>

         <!-- USER LOOKUP SECTION -->
         <div class="row mb-5" id="userLookupSection">
            <div class="col-12">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-3 border-bottom">
                        <div>
                           <h4 class="fw-bold mb-1" style="font-family:'Playfair Display', serif;"><i class="fas fa-search me-2 text-danger"></i>User Search &amp; Info Retrieval</h4>
                           <p class="text-muted small mb-0">Search a customer by User ID to retrieve their account details, contact info, and delivery address.</p>
                        </div>
                        <form method="GET" action="Admin.php" class="d-flex gap-2 align-items-center">
                           <input type="number" name="search_uid" class="form-control rounded-pill px-3" placeholder="Enter User ID (e.g. 1, 2)..." value="<?php echo isset($_GET['search_uid']) ? htmlspecialchars($_GET['search_uid']) : ''; ?>" style="width: 220px;">
                           <button type="submit" class="btn btn-danger rounded-pill px-4"><i class="fas fa-search me-1"></i>Search User</button>
                           <?php if (isset($_GET['search_uid']) && $_GET['search_uid'] !== ''): ?>
                              <a href="Admin.php" class="btn btn-outline-secondary rounded-pill px-3" title="Clear Search"><i class="fas fa-times me-1"></i>Clear</a>
                           <?php endif; ?>
                        </form>
                     </div>

                     <?php
                     if (isset($_GET['search_uid']) && intval($_GET['search_uid']) > 0) {
                         $suid = intval($_GET['search_uid']);
                         $con = getDBConnection() or die("couldn't connect to the server");

                         $u_id_col   = 'User_ID';
                         $name_col   = 'Full_Name';
                         $email_col  = 'Email';
                         $phone_col  = 'Telephone';
                         $addr_col   = 'Address';

                         $u_cols_res = mysqli_query($con, "SHOW COLUMNS FROM users");
                         if ($u_cols_res) {
                             while ($col = mysqli_fetch_assoc($u_cols_res)) {
                                 $fn = strtolower($col['Field']);
                                 if (in_array($fn, ['user_id', 'id', 'uid'])) $u_id_col = $col['Field'];
                                 if (in_array($fn, ['full_name', 'name', 'username'])) $name_col = $col['Field'];
                                 if (in_array($fn, ['email'])) $email_col = $col['Field'];
                                 if (in_array($fn, ['telephone', 'phone', 'mobile'])) $phone_col = $col['Field'];
                                 if (in_array($fn, ['address', 'delivery_address'])) $addr_col = $col['Field'];
                             }
                         }

                         $user_query = mysqli_query($con, "SELECT * FROM users WHERE `$u_id_col` = '$suid' LIMIT 1");
                         if ($user_query && mysqli_num_rows($user_query) > 0) {
                             $u_row  = mysqli_fetch_assoc($user_query);
                             $uid    = isset($u_row[$u_id_col]) ? $u_row[$u_id_col] : (isset($u_row['id']) ? $u_row['id'] : $suid);
                             $uname  = isset($u_row[$name_col]) ? $u_row[$name_col] : (isset($u_row['name']) ? $u_row['name'] : '-');
                             $uemail = isset($u_row[$email_col]) ? $u_row[$email_col] : '-';
                             $uphone = isset($u_row[$phone_col]) ? $u_row[$phone_col] : '-';
                             $uaddr  = !empty($u_row['Delivery_Address']) ? $u_row['Delivery_Address'] : (!empty($u_row['Address']) ? $u_row['Address'] : (isset($u_row[$addr_col]) && !empty($u_row[$addr_col]) ? $u_row[$addr_col] : 'Not specified'));
                             ?>
                             <div class="card border-0 rounded-4 shadow-sm my-2" style="background: linear-gradient(135deg, #ffffff 0%, #fff5f6 100%); border-left: 5px solid var(--primary) !important;">
                                <div class="card-body p-4">
                                   <div class="d-flex align-items-center gap-3 mb-3">
                                      <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 54px; height: 54px;">
                                         <i class="fas fa-user"></i>
                                      </div>
                                      <div>
                                         <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($uname); ?></h4>
                                         <span class="badge bg-danger rounded-pill px-3 py-1">User ID: #<?php echo $uid; ?></span>
                                      </div>
                                   </div>
                                   <div class="row g-3 mt-2">
                                      <div class="col-md-4">
                                         <div class="p-3 rounded-3 bg-white border">
                                            <small class="text-muted d-block fw-semibold mb-1"><i class="fas fa-envelope me-1 text-danger"></i>Email Address</small>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($uemail); ?></span>
                                         </div>
                                      </div>
                                      <div class="col-md-4">
                                         <div class="p-3 rounded-3 bg-white border">
                                            <small class="text-muted d-block fw-semibold mb-1"><i class="fas fa-phone me-1 text-danger"></i>Telephone</small>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($uphone); ?></span>
                                         </div>
                                      </div>
                                      <div class="col-md-4">
                                         <div class="p-3 rounded-3 bg-white border">
                                            <small class="text-muted d-block fw-semibold mb-1"><i class="fas fa-map-marker-alt me-1 text-danger"></i>Delivery Address</small>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($uaddr); ?></span>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                             </div>
                             <?php
                         } else {
                             ?>
                             <div class="alert alert-warning rounded-4 border-0 shadow-sm p-4 m-0">
                                <i class="fas fa-exclamation-triangle me-2 text-warning fs-5"></i>
                                <strong>No User Found!</strong> No registered user account exists with <strong>User ID #<?php echo $suid; ?></strong>.
                             </div>
                             <?php
                         }
                     } else {
                         ?>
                         <div class="text-center py-3 text-muted">
                            <i class="fas fa-search-location fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                            <span class="small">Enter a User ID in the search box above to retrieve customer account details and delivery address.</span>
                         </div>
                         <?php
                     }
                     ?>
                  </div>
               </div>
            </div>
         </div>

         <!-- ORDER MANAGEMENT SECTION -->
         <div class="row mb-5" id="ordersSection">
            <div class="col-12">
               <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body p-4">
                     <?php
                     $con = getDBConnection() or die("couldn't connect to the server");
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

                     // Filters & Search Params
                     $search_oid   = isset($_GET['search_oid']) ? intval($_GET['search_oid']) : 0;
                     $search_uid   = isset($_GET['search_uid']) ? intval($_GET['search_uid']) : 0;
                     $filter_date  = isset($_GET['date_range']) ? $_GET['date_range'] : 'all';
                     $filter_status = isset($_GET['status_filter']) ? $_GET['status_filter'] : 'all';
                     $page         = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                     $limit        = 20;
                     $offset       = ($page - 1) * $limit;

                     $where_clauses = array();
                     if ($search_oid > 0) {
                         $where_clauses[] = "Order_ID = '$search_oid'";
                     }
                     if ($search_uid > 0) {
                         $where_clauses[] = "User_ID = '$search_uid'";
                     }

                     // Date Range Filter
                     if ($filter_date === 'day') {
                         $where_clauses[] = "DATE(Order_Date) = CURDATE()";
                     } elseif ($filter_date === 'week') {
                         $where_clauses[] = "YEARWEEK(Order_Date, 1) = YEARWEEK(CURDATE(), 1)";
                     } elseif ($filter_date === 'month') {
                         $where_clauses[] = "YEAR(Order_Date) = YEAR(CURDATE()) AND MONTH(Order_Date) = MONTH(CURDATE())";
                     } elseif ($filter_date === 'year') {
                         $where_clauses[] = "YEAR(Order_Date) = YEAR(CURDATE())";
                     }

                     // Status Filter
                     if ($filter_status !== 'all' && !empty($filter_status)) {
                         $safe_status = mysqli_real_escape_string($con, $filter_status);
                         $where_clauses[] = "Status = '$safe_status'";
                     }

                     $where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";

                     // Total orders count for pagination
                     $count_res = mysqli_query($con, "SELECT COUNT(*) AS cnt FROM orders $where_sql");
                     $total_orders = ($count_res && $crow = mysqli_fetch_assoc($count_res)) ? intval($crow['cnt']) : 0;
                     $total_pages  = max(1, ceil($total_orders / $limit));
                     if ($page > $total_pages) $page = $total_pages;

                     // Fetch 20 orders for current page ordered by Order_Date DESC
                     $orders_query = mysqli_query($con, "SELECT Order_ID, User_ID, Product_ID, Quantity, `$price_col` AS Total_Price, Status, Order_Date FROM orders $where_sql ORDER BY Order_Date DESC, Order_ID DESC LIMIT $limit OFFSET $offset");
                     ?>

                     <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom">
                        <div>
                           <h4 class="fw-bold mb-1" style="font-family:'Playfair Display', serif;"><i class="fas fa-shopping-bag me-2 text-danger"></i>Orders Summary</h4>
                           <small class="text-muted">Showing <strong><?php echo min($total_orders, $offset + 1); ?>-<?php echo min($total_orders, $offset + $limit); ?></strong> of <strong><?php echo $total_orders; ?></strong> total orders</small>
                        </div>
                        
                        <form method="GET" action="Admin.php#ordersSection" class="d-flex align-items-center gap-2 flex-wrap">
                           <!-- Search Order ID -->
                           <div class="input-group input-group-sm" style="width: 170px;">
                              <input type="number" name="search_oid" class="form-control rounded-start-pill" placeholder="Order ID..." value="<?php echo $search_oid > 0 ? $search_oid : ''; ?>">
                              <button type="submit" class="btn btn-outline-danger rounded-end-pill"><i class="fas fa-search"></i></button>
                           </div>

                           <!-- Date Range Filter -->
                           <select name="date_range" class="form-select form-select-sm rounded-pill" style="width: 140px;" onchange="this.form.submit()">
                              <option value="all" <?php if ($filter_date === 'all') echo 'selected'; ?>>All Dates</option>
                              <option value="day" <?php if ($filter_date === 'day') echo 'selected'; ?>>Today</option>
                              <option value="week" <?php if ($filter_date === 'week') echo 'selected'; ?>>This Week</option>
                              <option value="month" <?php if ($filter_date === 'month') echo 'selected'; ?>>This Month</option>
                              <option value="year" <?php if ($filter_date === 'year') echo 'selected'; ?>>This Year</option>
                           </select>

                           <!-- Status Filter -->
                           <select name="status_filter" class="form-select form-select-sm rounded-pill" style="width: 145px;" onchange="this.form.submit()">
                              <option value="all" <?php if ($filter_status === 'all') echo 'selected'; ?>>All Statuses</option>
                              <option value="Pending" <?php if ($filter_status === 'Pending') echo 'selected'; ?>>Pending</option>
                              <option value="Accepted" <?php if ($filter_status === 'Accepted') echo 'selected'; ?>>Accepted</option>
                              <option value="Completed" <?php if ($filter_status === 'Completed') echo 'selected'; ?>>Completed</option>
                              <option value="Rejected" <?php if ($filter_status === 'Rejected') echo 'selected'; ?>>Rejected / Cancelled</option>
                           </select>

                           <?php if ($search_oid > 0 || $search_uid > 0 || $filter_date !== 'all' || $filter_status !== 'all'): ?>
                              <a href="Admin.php#ordersSection" class="btn btn-light btn-sm rounded-pill text-muted px-2" title="Reset Filters"><i class="fas fa-undo me-1"></i>Reset</a>
                           <?php endif; ?>

                           <a href="admin_orders.php" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold ms-1 shadow-sm d-inline-flex align-items-center gap-2">
                              <i class="fas fa-bell"></i>
                              <span>Pending Orders</span>
                              <?php if ($nav_pending_cnt > 0): ?>
                                 <span class="badge bg-white text-danger rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.78rem; font-weight: 800;"><?php echo $nav_pending_cnt; ?></span>
                              <?php endif; ?>
                           </a>
                        </form>
                     </div>

                     <?php if (!empty($order_action_msg)) echo $order_action_msg; ?>

                     <style>
                        .sticky-table-container {
                           max-height: 520px;
                           overflow-y: auto;
                           overflow-x: auto;
                        }
                        .sticky-table-container table thead th {
                           position: sticky;
                           top: 0;
                           z-index: 2;
                           background-color: #f8f9fa !important;
                           box-shadow: inset 0 -1px 0 #dee2e6;
                        }
                     </style>

                     <div class="sticky-table-container border rounded-3">
                        <table class="table table-hover align-middle admin-table text-center mb-0">
                           <thead class="table-light">
                              <tr>
                                 <th>Order ID</th>
                                 <th>Date &amp; Time</th>
                                 <th>User ID</th>

                                 <th>Quantity</th>
                                 <th>Total Price (LKR)</th>
                                 <th>Status</th>
                                 
                              </tr>
                           </thead>
                           <tbody id="adminOrdersTableBody">
                              <?php
                              if ($orders_query && mysqli_num_rows($orders_query) > 0) {
                                  while ($o_row = mysqli_fetch_assoc($orders_query)) {
                                      $oid       = $o_row['Order_ID'];
                                      $uid       = $o_row['User_ID'];
                                      $pid       = $o_row['Product_ID'];
                                      $qty       = $o_row['Quantity'];
                                      $tot_price = $o_row['Total_Price'];
                                      $status    = !empty($o_row['Status']) ? $o_row['Status'] : 'Pending';
                                      $odate     = !empty($o_row['Order_Date']) ? date('d M Y, h:i A', strtotime($o_row['Order_Date'])) : 'N/A';

                                      $status_class = 'bg-warning text-dark';
                                      if (strtolower($status) === 'accepted' || strtolower($status) === 'completed') {
                                          $status_class = 'bg-success';
                                      } elseif (strtolower($status) === 'rejected' || strtolower($status) === 'cancelled') {
                                          $status_class = 'bg-danger';
                                      }
                                      ?>
                                      <tr>
                                         <td class="fw-bold text-danger">#<?php echo $oid; ?></td>
                                         <td class="small text-muted fw-semibold"><?php echo htmlspecialchars($odate); ?></td>
                                         <td class="fw-semibold">#<?php echo $uid; ?></td>
                                         
                                         <td class="fw-bold"><?php echo $qty; ?></td>
                                         <td class="fw-bold text-dark">Rs. <?php echo number_format(floatval($tot_price), 2); ?></td>
                                         <td>
                                            <span class="badge <?php echo $status_class; ?> px-3 py-2 rounded-pill"><?php echo htmlspecialchars($status); ?></span>
                                         </td>
                                      </tr>
                                      <?php
                                  }
                              } else {
                                  echo "<tr><td colspan='7' class='text-center py-4 text-muted'><i class='fas fa-info-circle me-1'></i>No orders found matching selected filters.</td></tr>";
                              }
                              ?>
                           </tbody>
                        </table>
                     </div>

                     <!-- PAGINATION CONTROLS -->
                     <?php if ($total_pages > 1): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 flex-wrap gap-2">
                           <small class="text-muted">Page <strong><?php echo $page; ?></strong> of <strong><?php echo $total_pages; ?></strong></small>
                           <ul class="pagination pagination-sm mb-0">
                              <?php
                              $query_params = $_GET;
                              
                              // Prev Link
                              if ($page > 1) {
                                  $query_params['page'] = $page - 1;
                                  echo '<li class="page-item"><a class="page-link" href="Admin.php?' . http_build_query($query_params) . '#ordersSection">&laquo; Prev</a></li>';
                              } else {
                                  echo '<li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>';
                              }

                              // Page numbers
                              for ($p = 1; $p <= $total_pages; $p++) {
                                  $query_params['page'] = $p;
                                  $active_cls = ($p == $page) ? 'active' : '';
                                  echo '<li class="page-item ' . $active_cls . '"><a class="page-link" href="Admin.php?' . http_build_query($query_params) . '#ordersSection">' . $p . '</a></li>';
                              }

                              // Next Link
                              if ($page < $total_pages) {
                                  $query_params['page'] = $page + 1;
                                  echo '<li class="page-item"><a class="page-link" href="Admin.php?' . http_build_query($query_params) . '#ordersSection">Next &raquo;</a></li>';
                              } else {
                                  echo '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
                              }
                              ?>
                           </ul>
                        </div>
                     <?php endif; ?>

                  </div>
               </div>
            </div>
         </div>
         

   <!-- ADD / EDIT PRODUCT MODAL -->
   <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
               <h5 class="modal-title fw-bold" id="productModalTitle"><i class="fas fa-edit text-danger me-2"></i>Add / Edit Product</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm" method="POST" action="Admin.php" enctype="multipart/form-data">
               <div class="modal-body p-4">
                  <input type="hidden" name="Product_ID" id="editProductId" />
                  <div class="row g-3">
                     <div class="col-md-8">
                        <label class="form-label fw-semibold">Product Title *</label>
                        <input type="text" name="Product_Name" id="prodTitle" class="form-control p-3" placeholder="e.g. Milk Cotton Yarn(25g)" required />
                     </div>
                     <div class="col-md-4">
                        <label class="form-label fw-semibold">Category *</label>
                        <select name="Category" id="prodCat" class="form-select p-3" required>
                           <?php
                           $con_cats = getDBConnection();
                           $c_res = mysqli_query($con_cats, "SELECT * FROM categories ORDER BY Category_ID ASC");
                           if ($c_res && mysqli_num_rows($c_res) > 0) {
                               while ($c_row = mysqli_fetch_assoc($c_res)) {
                                   echo "<option value='" . htmlspecialchars($c_row['Category_Name']) . "'>" . htmlspecialchars($c_row['Category_Name']) . "</option>";
                               }
                           } else {
                               echo "<option value='Yarn & Crochet'>Yarn &amp; Crochet</option>";
                               echo "<option value='Beads & Jewellery'>Beads &amp; Jewellery</option>";
                               echo "<option value='Bag Accessories'>Bag Accessories</option>";
                               echo "<option value='Craft Tools'>Craft Tools</option>";
                               echo "<option value='Craft Materials'>Craft Materials</option>";
                           }
                           ?>
                        </select>
                     </div>
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Price (Rs.) *</label>
                        <input type="text" name="Price" id="prodPrice" class="form-control p-3" placeholder="e.g. Rs.185.00" required />
                     </div>
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Old Price (Optional)</label>
                        <input type="text" name="Old_Price" id="prodOldPrice" class="form-control p-3" placeholder="e.g. Rs.200.00" />
                     </div>
                     <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-file-image me-1 text-danger"></i>Upload Product Image File</label>
                        <input type="file" name="product_image_file" id="prodImgFile" class="form-control p-3 mb-2" accept="image/*" />
                        <label class="form-label fw-semibold text-muted small mb-1">Or Image File Path / URL (Fallback):</label>
                        <input type="text" name="Image" id="prodImg" class="form-control p-2" placeholder="img/menu/1.jpg" />
                     </div>
                     <div class="col-12">
                        <label class="form-label fw-semibold">Description *</label>
                        <textarea name="Description" id="prodDesc" class="form-control p-3" rows="3" placeholder="Product details and materials..." required></textarea>
                     </div>
                     <div class="col-12">
                        <label class="form-label fw-semibold">Craft Tags (comma separated)</label>
                        <input type="text" name="Tags" id="prodTags" class="form-control p-3" placeholder="e.g. Soft Touch, Premium Quality, Vibrant Colors" />
                     </div>
                  </div>
               </div>
               <div class="modal-footer border-top">
                  <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" name="insert" class="btn btn-primary rounded-pill px-4 me-2"><i class="fas fa-plus me-1"></i>Add Product</button>
                  <button type="submit" name="update" class="btn btn-danger rounded-pill px-4"><i class="fas fa-save me-1"></i>Update Product</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- ADD NEW ADMIN MEMBER MODAL -->
   <div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
               <h5 class="modal-title fw-bold"><i class="fas fa-user-plus text-danger me-2"></i>Add Person to Admin Panel</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addAdminForm" onsubmit="saveNewAdminMember(event)">
               <div class="modal-body p-4">
                  <div class="mb-3">
                     <label class="form-label fw-semibold">Admin Full Name *</label>
                     <input type="text" id="newAdminName" class="form-control p-3" placeholder="e.g. Amaya Ranathunga" required />
                  </div>
                  <div class="mb-3">
                     <label class="form-label fw-semibold">Admin Email Address *</label>
                     <input type="email" id="newAdminEmail" class="form-control p-3" placeholder="e.g. amaya.admin@glamhandy.com" required />
                  </div>
                  <div class="mb-3">
                     <label class="form-label fw-semibold">Initial Password *</label>
                     <input type="password" id="newAdminPass" class="form-control p-3" placeholder="Create password (min 6 chars)" required minlength="6" />
                  </div>
                  <div class="mb-3">
                     <label class="form-label fw-semibold">Admin Role *</label>
                     <select id="newAdminRole" class="form-select p-3">
                        <option value="Co-Admin">Co-Admin</option>
                        <option value="Inventory Manager">Inventory Manager</option>
                        <option value="Sales Manager">Sales Manager</option>
                        <option value="Super Admin">Super Admin</option>
                     </select>
                  </div>
               </div>
               <div class="modal-footer border-top">
                  <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger rounded-pill px-4"><i class="fas fa-check-circle me-1"></i>Add Admin Member</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- ADD / EDIT CATEGORY MODAL -->
   <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
               <h5 class="modal-title fw-bold" id="categoryModalTitle"><i class="fas fa-folder-plus text-danger me-2"></i>Add / Edit Category</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" method="POST" action="Admin.php" enctype="multipart/form-data">
               <div class="modal-body p-4">
                  <input type="hidden" name="Category_ID" id="editCategoryId" />
                  <div class="mb-3">
                     <label class="form-label fw-semibold">Category Name *</label>
                     <input type="text" name="Category_Name" id="catName" class="form-control p-3" placeholder="e.g. Yarn & Crochet" required />
                  </div>
                  <div class="mb-3">
                     <label class="form-label fw-semibold"><i class="fas fa-file-image me-1 text-danger"></i>Upload Category Image File</label>
                     <input type="file" name="category_image_file" id="catImgFile" class="form-control p-3 mb-2" accept="image/*" />
                     <label class="form-label fw-semibold text-muted small mb-1">Or Image File Path / URL (Fallback):</label>
                     <input type="text" name="Category_Image" id="catImg" class="form-control p-2" placeholder="img/category/1.jpg" />
                  </div>
                  <div class="mb-3">
                     <label class="form-label fw-semibold">Category Description</label>
                     <textarea name="Category_Description" id="catDesc" class="form-control p-3" rows="3" placeholder="Category highlights and details..."></textarea>
                  </div>
               </div>
               <div class="modal-footer border-top">
                  <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" name="insert_category" class="btn btn-primary rounded-pill px-4 me-2"><i class="fas fa-plus me-1"></i>Add Category</button>
                  <button type="submit" name="update_category" class="btn btn-danger rounded-pill px-4"><i class="fas fa-save me-1"></i>Update Category</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Bootstrap 5 bundle -->
   <script src="js/bootstrap.bundle.min.js"></script>
   <script>
   window.dbTotalRevenue = <?php echo floatval($rev_val); ?>;
   window.dbTotalOrders  = <?php echo intval($o_cnt); ?>;
   window.dbCategoryTotals = <?php echo json_encode(isset($cat_totals) ? $cat_totals : []); ?>;
   </script>
   <!-- Admin js (cache busted) -->
   <script src="js/admin.js?v=<?php echo time(); ?>"></script>
   <script>
   document.addEventListener('DOMContentLoaded', function() {
       // 1. Scroll Position Restoration for Full Page Reloads (Scroll to top on fresh login)
       if ('scrollRestoration' in history) {
           history.scrollRestoration = 'manual';
       }

       const isLoginRedirect = document.referrer.toLowerCase().includes('login') || (window.location.search === '' && window.location.hash === '');

       if (isLoginRedirect) {
           sessionStorage.removeItem('adminScrollPos');
           window.scrollTo(0, 0);
       } else {
           const savedScrollPos = sessionStorage.getItem('adminScrollPos');
           if (savedScrollPos !== null) {
               window.scrollTo(0, parseInt(savedScrollPos, 10));
               sessionStorage.removeItem('adminScrollPos');
           }
       }

       window.addEventListener('beforeunload', function() {
           sessionStorage.setItem('adminScrollPos', window.scrollY);
       });

       // 2. Generic AJAX Section Fetcher (Seamless update without flickering/jumping)
       function fetchSectionAjax(sectionId, url) {
           const section = document.getElementById(sectionId);
           if (!section) return;
           const cardBody = section.querySelector('.card-body');
           if (cardBody) cardBody.style.opacity = '0.5';

           fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
               .then(res => res.text())
               .then(html => {
                   const parser = new DOMParser();
                   const doc = parser.parseFromString(html, 'text/html');
                   const newContent = doc.querySelector('#' + sectionId + ' .card-body');
                   if (newContent && cardBody) {
                       cardBody.innerHTML = newContent.innerHTML;
                       cardBody.style.opacity = '1';
                       history.pushState(null, '', url);
                       if (sectionId === 'productSection') attachProductAjaxEvents();
                       if (sectionId === 'ordersSection') attachOrdersAjaxEvents();
                   } else {
                       window.location.href = url;
                   }
               })
               .catch(() => {
                   if (cardBody) cardBody.style.opacity = '1';
                   window.location.href = url;
               });
       }

       // Product Section AJAX Event Binders
       function attachProductAjaxEvents() {
           const productSection = document.getElementById('productSection');
           if (!productSection) return;

           const form = productSection.querySelector('form');
           if (form) {
               form.querySelectorAll('select').forEach(select => {
                   select.removeAttribute('onchange');
                   select.addEventListener('change', function() {
                       const formData = new FormData(form);
                       const params = new URLSearchParams(formData);
                       fetchSectionAjax('productSection', 'Admin.php?' + params.toString() + '#productSection');
                   });
               });

               form.addEventListener('submit', function(e) {
                   e.preventDefault();
                   const formData = new FormData(form);
                   const params = new URLSearchParams(formData);
                   fetchSectionAjax('productSection', 'Admin.php?' + params.toString() + '#productSection');
               });
           }

           productSection.querySelectorAll('.pagination a, a[title="Reset Filters"]').forEach(link => {
               link.addEventListener('click', function(e) {
                   e.preventDefault();
                   fetchSectionAjax('productSection', this.href);
               });
           });
       }

       // Orders Section AJAX Event Binders
       function attachOrdersAjaxEvents() {
           const ordersSection = document.getElementById('ordersSection');
           if (!ordersSection) return;

           const form = ordersSection.querySelector('form');
           if (form) {
               form.querySelectorAll('select').forEach(select => {
                   select.removeAttribute('onchange');
                   select.addEventListener('change', function() {
                       const formData = new FormData(form);
                       const params = new URLSearchParams(formData);
                       fetchSectionAjax('ordersSection', 'Admin.php?' + params.toString() + '#ordersSection');
                   });
               });

               form.addEventListener('submit', function(e) {
                   e.preventDefault();
                   const formData = new FormData(form);
                   const params = new URLSearchParams(formData);
                   fetchSectionAjax('ordersSection', 'Admin.php?' + params.toString() + '#ordersSection');
               });
           }

           ordersSection.querySelectorAll('.pagination a, a[title="Reset Filters"]').forEach(link => {
               link.addEventListener('click', function(e) {
                   e.preventDefault();
                   fetchSectionAjax('ordersSection', this.href);
               });
           });
       }

       attachProductAjaxEvents();
       attachOrdersAjaxEvents();
   });
   </script>
</body>
</html>
