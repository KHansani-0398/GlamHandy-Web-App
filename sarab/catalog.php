<?php
session_start();
require_once 'config.php';
?>
<!-- com -->
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="H.Kularatne">
      <meta name="description" content="Online Arts & Crafts Store">
      <title>GlamHandy Arts & Crafts - Catalog</title>
      <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
                  <li class="nav-item"><a class="nav-link active" href="catalog.php">Catalog</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#chefs">Who we are</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#testimonials">Reviews</a></li>
                  <li class="nav-item"><a class="nav-link" href="index.html#contact-section">Contact</a></li>
               </ul>
               <div class="d-flex align-items-center gap-2">
                  <button id="navSearchBtn" title="Search"><i class="fas fa-search"></i></button>
                  <?php if(isset($_SESSION['email'])): ?>
                      <?php if(strtolower($_SESSION['email']) == 'admin@gmail.com'): ?>
                          <a href="Admin.php" id="dynNavAdmin" class="btn btn-sm btn-outline-danger rounded-pill me-1"><i class="fas fa-user-shield me-1"></i>Admin Panel</a>
                      <?php else: ?>
                          <a href="user.php" id="dynNavUser" class="text-dark small fw-bold me-2 text-decoration-none" title="Go to My Profile"><i class="fas fa-user-circle me-1" style="color:var(--primary);font-size:1.1rem;"></i><?php echo htmlspecialchars($_SESSION['email']); ?></a>
                      <?php endif; ?>
                  <?php else: ?>
                      <a href="login.php" class="nav-login me-1" style="color:var(--dark);font-weight:600;font-size:0.9rem;text-decoration:none;">Login</a>
                      <a href="register.php" class="nav-login me-2" style="color:var(--primary);font-weight:600;font-size:0.9rem;text-decoration:none;">Register</a>
                  <?php endif; ?>
                  <a href="catalog.php" class="nav-link nav-cta ms-1"><i class="fas fa-shopping-bag me-1"></i>Order Now</a>
               </div>
            </div>
         </div>
      </nav>

      <!-- SEARCH OVERLAY POPUP -->
      <div id="searchOv">
         <button class="sovclose" id="searchClose"><i class="fas fa-times"></i></button>
         <div class="sovbox">
            <h4>What do want to create today?</h4>
            <div class="sovinput">
               <input type="text" id="searchInput" placeholder="Search yarn, beads, hooks..." autocomplete="off"/>
               <button><i class="fas fa-search"></i></button>
            </div>
            <div class="sovcats">
               <div class="sovcat active" data-cat="all">
                  <img src="img/menu/1.jpg" alt=""/>All Items
               </div>
                <div class="sovcat" data-cat="yarn">
                   <img src="img/menu/1.jpg" alt=""/>Yarn &amp; Crochet
                </div>
                <div class="sovcat" data-cat="jewelry">
                   <img src="img/menu/2.jpg" alt=""/>Beads &amp; Jewellery
                </div>
                <div class="sovcat" data-cat="bags">
                   <img src="img/menu/3.jpg" alt=""/>Bag Accessories
                </div>
                <div class="sovcat" data-cat="tools">
                   <img src="img/menu/4.jpg" alt=""/>Craft Tools
                </div>
                <div class="sovcat" data-cat="materials">
                   <img src="img/menu/5.jpg" alt=""/>Craft Materials
                </div>
            </div>
         </div>
      </div>



      <!-- HERO / CATEGORIES -->
      <section id="category" style="padding-top: 50px;">
         <div class="container">
            <?php if (isset($_GET['order']) && $_GET['order'] === 'success'): ?>
               <div class="alert alert-success alert-dismissible fade show text-center mb-4" role="alert">
                  <i class="fas fa-check-circle me-2"></i> <strong>Order Placed Successfully!</strong> Your order has been recorded in the database.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>
            <?php elseif (isset($_GET['order']) && $_GET['order'] === 'failed'): ?>
               <div class="alert alert-danger alert-dismissible fade show text-center mb-4" role="alert">
                  <i class="fas fa-exclamation-triangle me-2"></i> <strong>Order Failed!</strong> Could not place your order. Please try again.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>
            <?php endif; ?>
            <div class="text-center mb-5" data-aos="fade-up">
               <span class="slbl">What We Offer</span>
               <h2 class="stitle">Browse by <span>Category</span></h2>
               <div class="sline"></div>
               <p class="sdesc mx-auto" style="max-width:480px;">Browse our extensive collection of craft supplies, DIY essentials, and handmade accessories at affordable prices</p>
            </div>
            <div class="row g-3 justify-content-center">
               <div class="col-6 col-sm-4 col-md-3 col-lg-2" data-aos="zoom-in" data-aos-delay="0">
                  <div class="catcard active" data-filter="all">
                     <img class="catimg" src="img/category/1.jpg" alt="All Items"/>
                     <div class="catnm">All Items</div>
                  </div>
               </div>
                <?php
                $con_cats = getDBConnection();
                $c_q = mysqli_query($con_cats, "SELECT * FROM categories ORDER BY Category_ID ASC");
                $delay = 70;
                if ($c_q && mysqli_num_rows($c_q) > 0) {
                    while ($cr = mysqli_fetch_assoc($c_q)) {
                        $c_name   = $cr['Category_Name'];
                        $c_img    = !empty($cr['Category_Image']) ? $cr['Category_Image'] : 'img/category/1.jpg';
                        $c_filter = !empty($cr['Category_Filter']) ? $cr['Category_Filter'] : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $c_name));
                        if ($c_filter === 'yarncrochet') $c_filter = 'yarn';
                        ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
                           <div class="catcard" data-filter="<?php echo htmlspecialchars($c_filter); ?>">
                              <img class="catimg" src="<?php echo htmlspecialchars($c_img); ?>" alt="<?php echo htmlspecialchars($c_name); ?>" onerror="this.src='img/category/1.jpg';"/>
                              <div class="catnm"><?php echo htmlspecialchars($c_name); ?></div>
                           </div>
                        </div>
                        <?php
                        $delay += 70;
                    }
                }
                ?>
            </div>
         </div>
      </section>

      <!-- MENU GRID -->
      <section id="menu">
         <div class="container">
            <?php if (isset($_GET['order'])): ?>
               <div class="mb-4">
                  <?php if ($_GET['order'] == 'success'): ?>
                     <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i><strong>Order Placed Successfully!</strong> Your order has been recorded in the database and is pending Admin approval.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                     </div>
                  <?php elseif ($_GET['order'] == 'failed'): ?>
                     <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>Failed to place order. Please try again!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                     </div>
                  <?php endif; ?>
               </div>
            <?php endif; ?>

            <div class="text-center mb-5" data-aos="fade-up">
               <span class="slbl">What do you want to craft today?</span>
               <h2 class="stitle">Our Live<span> Collection</span></h2>
               <div class="sline"></div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4" data-aos="fade-up">
               <div class="d-flex flex-wrap gap-1">
                   <button class="filtbtn active" data-f="all">All</button>
                    <?php
                    $c_q2 = mysqli_query($con_cats, "SELECT * FROM categories ORDER BY Category_ID ASC");
                    if ($c_q2 && mysqli_num_rows($c_q2) > 0) {
                        while ($cr2 = mysqli_fetch_assoc($c_q2)) {
                            $c_name2   = $cr2['Category_Name'];
                            $c_filter2 = !empty($cr2['Category_Filter']) ? $cr2['Category_Filter'] : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $c_name2));
                            if ($c_filter2 === 'yarncrochet') $c_filter2 = 'yarn';
                            echo '<button class="filtbtn" data-f="' . htmlspecialchars($c_filter2) . '">' . htmlspecialchars($c_name2) . '</button>';
                        }
                    }
                    ?>
                </div>
                <div class="d-flex align-items-center gap-2">
                   <label for="catalogSortSelect" class="fw-semibold text-secondary small mb-0" style="white-space:nowrap;"><i class="fas fa-sort-amount-down me-1 text-danger"></i>Sort By:</label>
                   <select id="catalogSortSelect" class="form-select form-select-sm shadow-sm" style="min-width: 200px; border-radius: 20px; border-color: #f0c0c6;">
                      <option value="default">Featured / Default</option>
                      <option value="price-asc">Price: Low to High</option>
                      <option value="price-desc">Price: High to Low</option>
                      <option value="newest">Newest First</option>
                   </select>
                </div>
             </div>

             <!-- Displaying the products entered from database -->
             <div class="row g-4" id="mgrid">
                <?php
                $con = getDBConnection();
                $query = mysqli_query($con, "SELECT * FROM products ORDER BY Product_ID DESC");

                if ($query && mysqli_num_rows($query) > 0) {
                     while ($row = mysqli_fetch_assoc($query)) {
                         $pid       = $row['Product_ID'];
                         $name      = $row['Product_Name'];
                         $cat       = $row['Category'];
                         $price     = $row['Price_LKR'];
                         $old_price = $row['Old_Price_LKR'];
                         $image     = $row['Image'];
                         $desc      = $row['Description'];
                         $tags      = $row['Tags'];
                        
                        // Category Filter Mapping
                        $catFilter = 'all';
                        if (stripos($cat, 'Yarn') !== false || stripos($cat, 'Crochet') !== false) $catFilter = 'yarn';
                        elseif (stripos($cat, 'Bead') !== false || stripos($cat, 'Jewel') !== false) $catFilter = 'jewelry';
                        elseif (stripos($cat, 'Bag') !== false) $catFilter = 'bags';
                        elseif (stripos($cat, 'Tool') !== false) $catFilter = 'tools';
                        elseif (stripos($cat, 'Material') !== false) $catFilter = 'materials';
                        ?>
                        <div class="col-sm-6 col-lg-4 mwrap" data-c="<?php echo $catFilter; ?>">
                           <div class="mcard"
                              data-id="<?php echo $pid; ?>"
                              data-img="<?php echo $image; ?>"
                              data-title="<?php echo $name; ?>"
                              data-cat="<?php echo $cat; ?>"
                              data-price="Rs.<?php echo $price; ?>" data-old="<?php echo !empty($old_price) ? 'Rs.' . $old_price : ''; ?>"
                              data-desc="<?php echo $desc; ?>"
                              data-tags="<?php echo $tags; ?>">
                             <div class="mimg">
                                <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" onerror="this.src='img/logo.png.jpeg';"/>
                                <div class="mbdg new"><i class="fas fa-star"></i> Live</div>
                                <div class="mhrt"><i class="far fa-heart"></i></div>
                             </div>
                             <div class="mbody">
                                <div class="mcat"><?php echo $cat; ?></div>
                                <div class="mtit"><?php echo $name; ?></div>
                                <div class="mdesc"><?php echo $desc; ?></div>
                                <div class="mfoot">
                                   <div>
                                      <div class="mprice">Rs.<?php echo $price; ?> <?php if(!empty($old_price)): ?><small>Rs.<?php echo $old_price; ?></small><?php endif; ?></div>
                                   </div>
                                   <form method="POST" action="orders.php" style="display:inline;">
                                      <input type="hidden" name="Product_ID" value="<?php echo $pid; ?>">
                                      <input type="hidden" name="Quantity" value="1">
                                      <button type="submit" name="place_order" class="madd" title="Order Now"><i class="fas fa-plus"></i></button>
                                   </form>
                                </div>
                             </div>
                          </div>
                       </div>
                        <?php
                    }
                } else {
                   echo "<div class='col-12 text-center py-5'><p class='text-muted fs-5'><i class='fas fa-info-circle me-2'></i>No products found in the database yet. Add products from Admin Panel!</p></div>";
               }
               ?>
            </div>
         </div>
      </section>

      <!-- MENU DETAIL POPUP MODAL -->
      <div id="menuPop">
         <div class="mpbox">
            <button class="mpclose" id="mpClose"><i class="fas fa-times"></i></button>
            <div class="mpimg"><img id="mpImg" src="" alt=""/></div>
            <div class="mpbody">
               <div id="mpCat"></div>
               <div id="mpTitle"></div>
               <div id="mpDesc"></div>
               <div id="mpPrice"></div>
               <div class="mpqty">
                  <button class="mpqbtn" id="mpMinus">-</button>
                  <span class="mpqnum" id="mpQnum">1</span>
                  <button class="mpqbtn" id="mpPlus">+</button>
                  <span style="font-size:.82rem;color:#aaa;margin-left:9px;">No. of Items</span>
               </div>
               <div class="mptags" id="mpTags"></div>
               <form method="POST" action="orders.php" id="orderSubmitForm" class="mt-3">
                  <input type="hidden" name="Product_ID" id="mpProductIdVal">
                  <input type="hidden" name="Quantity" id="mpQuantityVal" value="1">
                  <button type="submit" name="place_order" class="mpaddcart" id="mpAddCart"><i class="fas fa-shopping-cart me-2"></i>Add to Cart / Place Order</button>
               </form>
            </div>
         </div>
      </div>

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
                     <li><a href="index.html#blog"><i class="fas fa-chevron-right"></i>Blog</a></li>
                     <li><a href="index.html#contact-section"><i class="fas fa-chevron-right"></i>Contact</a></li>
                  </ul>
               </div>
               <div class="col-sm-6 col-lg-2">
                  <div class="ftit">Our Collection</div>
                  <ul class="flinks ps-0">
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Crochet &amp; Yarn</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Beads &amp; Jewellery</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Bag Accessories</a></li>
                     <li><a href="catalog.php"><i class="fas fa-chevron-right"></i>Patterns &amp; Books</a></li>
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
