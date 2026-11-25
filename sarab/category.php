<?php
session_start();
require_once 'config.php';

// Fetch active categories from database
$cat_query = mysqli_query($con, "SELECT * FROM categories ORDER BY Category_ID ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Craft Categories - GlamHandy Arts & Crafts</title>
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
   <!-- Bootstrap 5.3 -->
   <link href="css/bootstrap.min.css" rel="stylesheet" />
   <!-- Font Awesome Icons -->
   <link rel="stylesheet" href="css/all.min.css" />
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
               <li class="nav-item"><a class="nav-link active" href="category.php">Categories</a></li>
               <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
               <li class="nav-item"><a class="nav-link" href="index.html#about">About</a></li>
               <li class="nav-item"><a class="nav-link" href="index.html#contact-section">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
               <?php if(isset($_SESSION['email'])): ?>
                   <?php if(strtolower($_SESSION['email']) == 'admin@gmail.com'): ?>
                       <a href="Admin.php" class="btn btn-sm btn-outline-danger rounded-pill me-1"><i class="fas fa-user-shield me-1"></i>Admin Panel</a>
                   <?php else: ?>
                       <a href="user.php" class="text-dark small fw-bold me-2 text-decoration-none"><i class="fas fa-user-circle me-1" style="color:var(--primary);"></i><?php echo htmlspecialchars($_SESSION['email']); ?></a>
                   <?php endif; ?>
               <?php else: ?>
                   <a href="login.php" class="nav-login me-1" style="color:var(--dark);font-weight:600;font-size:0.9rem;text-decoration:none;">Login</a>
                   <a href="register.php" class="nav-login me-2" style="color:var(--primary);font-weight:600;font-size:0.9rem;text-decoration:none;">Register</a>
               <?php endif; ?>
               <a href="catalog.php" class="nav-link nav-cta ms-1"><i class="fas fa-shopping-bag me-1"></i>Explore Store</a>
            </div>
         </div>
      </div>
   </nav>

   <!-- HEADER BANNER -->
   <section style="background: linear-gradient(135deg, #fff0f3 0%, #ffeef0 100%); padding: 50px 0; border-bottom: 1px solid #f0d8dc;">
      <div class="container text-center">
         <span class="slbl">Browse Craft Collections</span>
         <h1 class="stitle" style="font-family: 'Playfair Display', serif; font-size: 2.3rem; font-weight: 900; color: var(--dark);">
            Our Craft <span>Categories</span>
         </h1>
         <div class="sline"></div>
         <p class="text-muted mt-2 mx-auto" style="max-width: 600px; font-size: 0.95rem;">
            Explore craft categories loaded dynamically from our backend database. Select a category to discover supplies, tools, and accessories.
         </p>
      </div>
   </section>

   <!-- MAIN CATEGORIES GRID -->
   <section class="py-5" style="background: #fdf8f8; min-height: 60vh;">
      <div class="container">
         <div class="row g-4">
            <?php
            if ($cat_query && mysqli_num_rows($cat_query) > 0) {
                while ($c = mysqli_fetch_assoc($cat_query)) {
                    $cname  = $c['Category_Name'];
                    $cimg   = !empty($c['Category_Image']) ? $c['Category_Image'] : 'img/category/1.jpg';
                    $cdesc  = !empty($c['Description']) ? $c['Description'] : 'Explore items in ' . $cname;
                    $cfilter= !empty($c['Category_Filter']) ? $c['Category_Filter'] : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cname));

                    // Count total products under this category
                    $cnt_q = mysqli_query($con, "SELECT COUNT(*) as total FROM products WHERE Category = '" . mysqli_real_escape_string($con, $cname) . "'");
                    $prod_count = ($cnt_q && $cnt_r = mysqli_fetch_assoc($cnt_q)) ? $cnt_r['total'] : 0;
                    ?>
                    <div class="col-md-6 col-lg-4">
                       <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden" style="transition: transform 0.3s ease;">
                          <div style="height: 200px; overflow: hidden; position: relative;">
                             <img src="<?php echo htmlspecialchars($cimg); ?>" alt="<?php echo htmlspecialchars($cname); ?>" onerror="this.src='img/category/1.jpg';" style="width: 100%; height: 100%; object-fit: cover;" />
                             <span class="position-absolute top-0 end-0 bg-danger text-white px-3 py-1 m-3 rounded-pill fw-semibold small shadow-sm">
                                <?php echo $prod_count; ?> Products
                             </span>
                          </div>
                          <div class="card-body p-4 d-flex flex-column justify-content-between">
                             <div>
                                <h4 class="fw-bold text-dark mb-2" style="font-family:'Playfair Display', serif;"><?php echo htmlspecialchars($cname); ?></h4>
                                <p class="text-secondary small mb-3"><?php echo htmlspecialchars($cdesc); ?></p>
                             </div>
                             <div>
                                <a href="catalog.php?cat=<?php echo urlencode($cname); ?>" class="btn btn-outline-danger w-100 rounded-pill fw-semibold">
                                   <i class="fas fa-shopping-bag me-1"></i>View <?php echo htmlspecialchars($cname); ?>
                                </a>
                             </div>
                          </div>
                       </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-12 text-center py-5 text-muted'><h5>No categories found in database.</h5></div>";
            }
            ?>
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
                  <li><a href="category.php"><i class="fas fa-chevron-right"></i>Categories</a></li>
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
