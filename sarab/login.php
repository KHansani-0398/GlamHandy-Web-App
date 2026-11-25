<?php
session_start();
require_once 'config.php';

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="H.Kularatne">
      <meta name="description" content="Online Arts & Crafts Store">
      <title>GlamHandy Arts & Crafts - Login</title>
      <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet"/>
      <!-- Bootstrap 5.3 -->
      <link href="css/bootstrap.min.css" rel="stylesheet"/>
      <!-- AOS Animate on Scroll -->
      <link href="css/aos.css" rel="stylesheet"/>
      <!-- Swiper -->
      <link href="css/swiper-bundle.min.css" rel="stylesheet"/>
      <!-- all min css -->
      <link rel="stylesheet" href="css/all.min.css"/>
      <!-- magnific CSS -->
      <link rel="stylesheet" href="css/magnific-popup.css"/>
      <!-- Style CSS -->
      <link rel="stylesheet" href="css/style.css" />
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
               <ul class="navbar-nav mx-auto">
                  <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#about">About</a></li>
                  <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#chefs">Who we are</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#testimonials">Reviews</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#contact-section">Contact</a></li>
               </ul>
               <div class="d-flex align-items-center gap-2">
                  <button id="navSearchBtn" title="Search"><i class="fas fa-search"></i></button>
                  <a href="login.php" class="nav-login me-1" style="color:var(--dark);font-weight:600;font-size:0.9rem;text-decoration:none;">Login</a>
                  <a href="register.php" class="nav-login me-2" style="color:var(--primary);font-weight:600;font-size:0.9rem;text-decoration:none;">Register</a>
                  <a href="catalog.php" class="nav-link nav-cta ms-1"><i class="fas fa-shopping-bag me-1"></i>Order Now</a>
               </div>
            </div>
         </div>
      </nav>

<section class="login-section py-5">
    <div class="container d-flex justify-content-center">
        <div class="card shadow-sm border-0" style="width: 100%; max-width: 400px;">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Login</h2>

                <?php if (isset($_GET['error']) || isset($_GET['redirect'])): ?>
                    <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <?php 
                            if ($_GET['error'] == 'email') {
                                echo "Incorrect Email address entered!";
                            } elseif ($_GET['error'] == 'password') {
                                echo "Incorrect Password entered!";
                            } elseif ($_GET['error'] == 'login_required') {
                                echo "Please log in to add items to your cart or place an order!";
                            } else {
                                echo "Incorrect Username or Password entered!";
                            }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php elseif (isset($_GET['redirect'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Please log in to continue your action.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (isset($_GET['product_id']) || isset($_SESSION['pending_product_id'])): 
                    $pid = isset($_GET['product_id']) ? intval($_GET['product_id']) : intval($_SESSION['pending_product_id']);
                    $con_p = getDBConnection();
                    $p_q = mysqli_query($con_p, "SELECT Product_Name FROM products WHERE Product_ID='$pid'");
                    $p_name = ($p_q && $pr = mysqli_fetch_assoc($p_q)) ? $pr['Product_Name'] : 'Product #' . $pid;
                 ?>
                    <div class="alert alert-info rounded-4 border-3 border-info shadow-sm mb-4 p-3" role="alert" style="background:#eef7ff; border-left: 5px solid #0dcaf0 !important;">
                       <i class="fas fa-shopping-bag text-primary me-2 fs-5"></i>
                       <strong>Almost there!</strong> Please log in to complete your order for <strong><?php echo htmlspecialchars($p_name); ?></strong>. Once logged in, you will be taken straight to checkout!
                    </div>
                 <?php endif; ?>

                <form id="loginForm" method="POST" action="form.php" autocomplete="off">
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ''); ?>">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter your email" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                           <label for="loginPassword" class="form-label mb-0">Password</label>
                           <a href="forgot-password.html" style="color: var(--primary); font-size: 0.85rem; font-weight: 500; text-decoration: none;">Forgot your password?</a>
                        </div>
                        <input type="password" class="form-control mt-1" id="loginPassword" name="PASS" placeholder="Enter your password" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg" style="background-color: var(--primary); border: none;">Login</button>
                    </div>
                </form>

                <script>
                window.addEventListener("pageshow", function (event) {
                    if (event.persisted) {
                        window.location.reload();
                    }
                    var form = document.getElementById('loginForm');
                    if (form) form.reset();
                });
                </script>

                <p class="text-center mt-4 mb-0">Don't have an account?
                    <a href="register.php" style="color: var(--primary); font-weight: 500;">Register here</a>
                </p>
            </div>
        </div>
    </div>
</section>

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
                     <li><a href="index.html#blog"><i class="fas fa-chevron-right"></i>Blog</a></li>
                     <li><a href="index.html#contact-section"><i class="fas fa-chevron-right"></i>Contact</a></li>
                  </ul>
               </div>
               <div class="col-sm-6 col-lg-2">
                  <div class="ftit">Our Collection</div>
                  <ul class="flinks ps-0">
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Crochet & Yarn</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Beads & Jewellery</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Bag Accessories</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Patterns & Books</a></li>
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
                  <div class="fci">
                     <div class="fciico"><i class="fas fa-clock"></i></div>
                     <div class="fciinfo"><strong>Hours</strong>Mon - Tue: 9 AM - 8 PM | Wed - Sun: 9 AM - 11 PM</div>
                  </div>
               </div>
            </div>
         </div>
         <div class="fbot">
            <div class="container">
               <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <p>&copy; 2026 <span>GlamHandy Craft Store</span>. All Rights Reserved.</p>
                  <div><a href="privacy.html">Privacy Policy</a><a href="terms.html">Terms &amp; Conditions</a></div>
               </div>
            </div>
         </div>
      </footer>

      <!-- Back to top -->
      <button id="btt" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-chevron-up"></i></button>
    
      <!-- JS Libraries -->
      <script src="js/jquery-3.7.1.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/aos.js"></script>
      <script src="js/swiper-bundle.min.js"></script>
      <script src="js/jquery.magnific-popup.min.js"></script>
      <script src="js/main.js"></script>
   </body>
</html>
