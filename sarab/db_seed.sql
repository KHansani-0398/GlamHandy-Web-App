-- ============================================================
-- GlamHandy Arts & Crafts Database Seeding File
-- Target Database: glamhandy
-- ============================================================

-- ------------------------------------------------------------
-- USERS TABLE SEEDING (5 Users with comments above each)
-- Column names: Full_Name, Email, Telephone, Delivery_Address, Password, Image
-- ------------------------------------------------------------

-- User 1
INSERT INTO users (Full_Name, Email, Telephone, Delivery_Address, Password, Image) 
VALUES ('Kavindi Perera', 'kavindi.perera@gmail.com', '0771234567', 'No. 12, Main Street, Colombo 03', 'user1234', '');

-- User 2
INSERT INTO users (Full_Name, Email, Telephone, Delivery_Address, Password, Image) 
VALUES ('Nipuna Silva', 'nipuna.silva@gmail.com', '0712345678', 'No. 45, Station Road, Kandy', 'user1234', '');

-- User 3
INSERT INTO users (Full_Name, Email, Telephone, Delivery_Address, Password, Image) 
VALUES ('Dilini Fernando', 'dilini.fernando@gmail.com', '0753456789', 'No. 88, Galle Road, Panadura', 'user1234', '');

-- User 4
INSERT INTO users (Full_Name, Email, Telephone, Delivery_Address, Password, Image) 
VALUES ('Sahan Jayasinghe', 'sahan.jayasinghe@gmail.com', '0784567890', 'No. 15/A, Lake Drive, Rajagiriya', 'user1234', '');

-- User 5
INSERT INTO users (Full_Name, Email, Telephone, Delivery_Address, Password, Image) 
VALUES ('Amaya Ranathunga', 'amaya.ranathunga@gmail.com', '0765678901', 'No. 102, Temple Road, Gampaha', 'user1234', '');


-- ------------------------------------------------------------
-- PRODUCTS TABLE SEEDING (2 Products under each of the 5 categories)
-- Note: Image fields are left blank for user to upload images.
-- ------------------------------------------------------------

-- Category: Yarn & Crochet - Product 1
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Milk Cotton Yarn (50g)', 'Yarn & Crochet', '250.00', '300.00', '', 'Ultra-soft 5-ply milk cotton yarn perfect for amigurumi, baby clothes, blankets, and plush crochet crafts.', 'Soft Touch, Premium Quality, Vibrant Colors');

-- Category: Yarn & Crochet - Product 2
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Velvet Chenille Chunky Yarn (100g)', 'Yarn & Crochet', '580.00', '650.00', '', 'Super fluffy and cozy chunky velvet chenille yarn designed for amigurumi toys and warm winter scarves.', 'Chunky Yarn, Extra Soft, Plush Feel');

-- Category: Beads & Jewellery - Product 1
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Pastel Acrylic Flower Beads Set (50g)', 'Beads & Jewellery', '320.00', '380.00', '', 'Assorted pastel matte flower-shaped acrylic beads ideal for bracelet making, phone charms, and DIY craft keychains.', 'Pastel Beads, DIY Jewellery, Craft Supplies');

-- Category: Beads & Jewellery - Product 2
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Glass Seed Beads Kit (12 Colors)', 'Beads & Jewellery', '750.00', '890.00', '', 'High-shine 2mm glass seed beads in a 12-grid storage box for intricate waist beads, rings, and embroidery art.', 'Glass Beads, Multi Color, Jewellery Making');

-- Category: Bag Accessories - Product 1
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Oval Wooden Crochet Bag Base (20x15cm)', 'Bag Accessories', '450.00', '520.00', '', 'Smooth laser-cut natural wooden base with pre-drilled holes for sturdy bottom support in crocheted tote bags.', 'Wood Base, Bag Shaping, Durable');

-- Category: Bag Accessories - Product 2
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Gold Metal Chain Strap with Leather Weave (120cm)', 'Bag Accessories', '890.00', '1050.00', '', 'Luxury gold metal chain strap threaded with soft faux leather and swivel lobster clasps for handbags.', 'Gold Chain, Bag Strap, Premium Hardware');

-- Category: Craft Tools - Product 1
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Ergonomic Soft Rubber Handle Crochet Hook Set (8 Sizes)', 'Craft Tools', '1250.00', '1500.00', '', 'Non-slip cushioned ergonomic aluminum crochet hooks ranging from 2.5mm to 6.0mm to reduce hand fatigue.', 'Ergonomic Hooks, Soft Grip, Complete Set');

-- Category: Craft Tools - Product 2
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Precision Craft Scissors & Snips Pair', 'Craft Tools', '480.00', '550.00', '', 'Ultra-sharp stainless steel thread snips and vintage craft scissors for clean thread trimming and fabric cutting.', 'Sharp Blades, Thread Cutter, Sewing Essential');

-- Category: Craft Materials - Product 1
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Polymer Soft Clay Starter Block Set (24 Colors)', 'Craft Materials', '1450.00', '1700.00', '', 'Non-toxic bakeable polymer clay blocks in vibrant shades for miniature sculpting, keychains, and hair clips.', 'Polymer Clay, Bakeable, Non-Toxic');

-- Category: Craft Materials - Product 2
INSERT INTO products (Product_Name, Category, Price_LKR, Old_Price_LKR, Image, Description, Tags)
VALUES ('Natural Jute Twine Cord Roll (100m)', 'Craft Materials', '380.00', '450.00', '', 'Eco-friendly 2mm 3-ply natural rustic jute string for gift wrapping, macrame wall hangings, and botanical crafts.', 'Eco Friendly, Rustic Twine, Gift Wrapping');
