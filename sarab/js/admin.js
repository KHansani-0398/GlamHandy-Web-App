// Default Products Seed Data
const initialProducts = [
    {
        id: 'prod-1',
        title: 'Milk Cotton Yarn(25g)',
        cat: 'Yarn & Crochet',
        categoryKey: 'yarn',
        price: 'Rs.185.00',
        oldPrice: 'Rs.200.00',
        img: 'img/menu/1.jpg',
        desc: 'Premium milk cotton yarn, soft texture, vibrant colours, and perfect for crochet, knitting, amigurumi, and handmade craft projects.',
        tags: ['Soft Touch', 'Premium Quality', 'Vibrant Colors'],
        badge: 'Hot'
    },
    {
        id: 'prod-2',
        title: 'Acryllic Beads',
        cat: 'Beads & Jewellery',
        categoryKey: 'jewelry',
        price: 'Rs.200.00',
        oldPrice: 'Rs.250.99',
        img: 'img/menu/2.jpg',
        desc: 'Mixed color acrylic beads in assorted shapes and sizes, perfect for handmade jewellery, bracelets, keychains, and creative DIY crafts.',
        tags: ['Assorted Colors', 'DIY Jewelry', 'Durable'],
        badge: 'New'
    },
    {
        id: 'prod-3',
        title: 'Wooden Crochet Bag Base',
        cat: 'Bag Accessories',
        categoryKey: 'bags',
        price: 'Rs.800.99',
        oldPrice: 'Rs.850.99',
        img: 'img/menu/3.jpg',
        desc: 'High-quality natural wooden base for creating custom handmade crochet bags, baskets, and structured craft accessories.',
        tags: ['Natural Wood', 'Bag Crafting', 'Top Quality'],
        badge: 'Best Seller'
    },
    {
        id: 'prod-4',
        title: 'Crochet Hook Set',
        cat: 'Craft Tools',
        categoryKey: 'tools',
        price: 'Rs.750.00',
        oldPrice: '',
        img: 'img/menu/4.jpg',
        desc: 'Colourful ergonomic crochet hooks perfect for creating handmade bags, toys, and crafts',
        tags: ['Ergonomic Grip', 'Durable Metal', 'Complete Set'],
        badge: ''
    },
    {
        id: 'prod-5',
        title: 'Decorative Feathers',
        cat: 'Craft Materials',
        categoryKey: 'materials',
        price: 'Rs.1200.99',
        oldPrice: 'Rs.1500.99',
        img: 'img/menu/5.jpg',
        desc: 'Colourful decorative feathers perfect for DIY crafts, dream catchers, decorations, and handmade projects',
        tags: ['Vibrant Shades', 'DIY Crafts', 'Light & Fluffy'],
        badge: 'New'
    }
];

// Initial Admin Team Seed Data
const initialAdminTeam = [
    { id: 'adm-1', name: 'Store Admin', email: 'admin@glamhandy.com', role: 'Super Admin' },
    { id: 'adm-2', name: 'Amaya Ranathunga', email: 'amaya.admin@glamhandy.com', role: 'Inventory Manager' }
];

function getProducts() {
    const stored = localStorage.getItem('glamhandy_products');
    if (!stored) {
        localStorage.setItem('glamhandy_products', JSON.stringify(initialProducts));
        return initialProducts;
    }
    return JSON.parse(stored);
}

function saveProductsList(products) {
    localStorage.setItem('glamhandy_products', JSON.stringify(products));
}

function getAdminTeam() {
    const stored = localStorage.getItem('glamhandy_admin_team');
    if (!stored) {
        localStorage.setItem('glamhandy_admin_team', JSON.stringify(initialAdminTeam));
        return initialAdminTeam;
    }
    return JSON.parse(stored);
}

function saveAdminTeamList(team) {
    localStorage.setItem('glamhandy_admin_team', JSON.stringify(team));
}

// Chart Data Sets
const visualGraphData = {
    monthly: [
        { label: 'Jan', value: 8500, percent: 35 },
        { label: 'Feb', value: 10200, percent: 42 },
        { label: 'Mar', value: 9400, percent: 38 },
        { label: 'Apr', value: 12500, percent: 51 },
        { label: 'May', value: 14800, percent: 60 },
        { label: 'Jun', value: 16200, percent: 66 },
        { label: 'Jul', value: 18500, percent: 75 },
        { label: 'Aug', value: 15300, percent: 62 },
        { label: 'Sep', value: 17200, percent: 70 },
        { label: 'Oct', value: 19400, percent: 79 },
        { label: 'Nov', value: 21000, percent: 85 },
        { label: 'Dec', value: 24500, percent: 100 }
    ],
    quarterly: [
        { label: 'Q1 (Jan-Mar)', value: 28100, percent: 43 },
        { label: 'Q2 (Apr-Jun)', value: 43500, percent: 67 },
        { label: 'Q3 (Jul-Sep)', value: 51000, percent: 78 },
        { label: 'Q4 (Oct-Dec)', value: 64900, percent: 100 }
    ],
    yearly: [
        { label: '2024', value: 85000, percent: 45 },
        { label: '2025', value: 128000, percent: 68 },
        { label: '2026 (YTD)', value: 187500, percent: 100 }
    ]
};

document.addEventListener('DOMContentLoaded', () => {
    // Render Product Table & Admin Team Table

    // Display welcome message: "Welcome to Admin Control Panel!"
    const welcomeEl = document.getElementById('adminWelcome');
    if (welcomeEl) {
        welcomeEl.textContent = 'Welcome to Admin Control Panel!';
    }

    // Render Product Table & Admin Team Table (PHP renders MySQL products)
    // renderAdminProducts();
    renderAdminTeamTable();

    // Render Visual Revenue Chart
    renderVisualRevenueBarChart('monthly');

    // Handle logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('currentUser');
            localStorage.removeItem('cartItems');
            window.location.href = 'index.html';
        });
    }
});

function openAddProductModal() {
    const modalTitle = document.getElementById('productModalTitle');
    if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-plus-circle text-danger me-2"></i>Add New Product';
    
    const form = document.getElementById('productForm');
    if (form) form.reset();
    
    const editId = document.getElementById('editProductId');
    if (editId) editId.value = '';

    const insertBtn = document.querySelector('button[name="insert"]');
    const updateBtn = document.querySelector('button[name="update"]');
    if (insertBtn) insertBtn.style.display = 'inline-block';
    if (updateBtn) updateBtn.style.display = 'none';

    const modalEl = document.getElementById('productModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function openEditProductModal(id, title, cat, price, oldPrice, img, desc, tags) {
    const modalTitle = document.getElementById('productModalTitle');
    if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-edit text-danger me-2"></i>Edit Product Details';

    document.getElementById('editProductId').value = id || '';
    document.getElementById('prodTitle').value = title || '';
    document.getElementById('prodCat').value = cat || 'Yarn & Crochet';
    document.getElementById('prodPrice').value = price || '';
    document.getElementById('prodOldPrice').value = oldPrice || '';
    document.getElementById('prodImg').value = img || '';
    document.getElementById('prodDesc').value = desc || '';
    document.getElementById('prodTags').value = tags || '';

    const insertBtn = document.querySelector('button[name="insert"]');
    const updateBtn = document.querySelector('button[name="update"]');
    if (insertBtn) insertBtn.style.display = 'none';
    if (updateBtn) updateBtn.style.display = 'inline-block';

    const modalEl = document.getElementById('productModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function openEditProductModalFromBtn(btn) {
    const id = btn.getAttribute('data-id');
    const title = btn.getAttribute('data-title');
    const cat = btn.getAttribute('data-cat');
    const price = btn.getAttribute('data-price');
    const oldPrice = btn.getAttribute('data-oldprice');
    const img = btn.getAttribute('data-img');
    const desc = btn.getAttribute('data-desc');
    const tags = btn.getAttribute('data-tags');
    openEditProductModal(id, title, cat, price, oldPrice, img, desc, tags);
}

// Render Visual Revenue Bar Chart
function renderVisualRevenueBarChart(period) {
    const container = document.getElementById('visualBarChartContainer');
    if (!container) return;

    let data = visualGraphData[period] || visualGraphData.monthly;

    // Tally dynamically with live MySQL database records if available
    if (window.dbCategoryTotals && Object.keys(window.dbCategoryTotals).length > 0 && window.dbTotalRevenue > 0) {
        const catKeys = Object.keys(window.dbCategoryTotals);
        const maxCatVal = Math.max(...Object.values(window.dbCategoryTotals), 1);
        
        data = catKeys.map(key => {
            const val = window.dbCategoryTotals[key];
            const pct = Math.max(15, Math.round((val / maxCatVal) * 100));
            return {
                label: key.split(' ')[0], // e.g. Yarn, Beads, Bag, Tools, Craft
                value: val,
                percent: pct
            };
        });

        const peakEl = document.getElementById('peakRevVal');
        const avgEl = document.getElementById('avgRevVal');
        if (peakEl) peakEl.textContent = 'Rs. ' + window.dbTotalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (avgEl) avgEl.textContent = 'Rs. ' + (window.dbTotalOrders > 0 ? (window.dbTotalRevenue / window.dbTotalOrders).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
    }

    container.innerHTML = data.map(item => {
        const valFormatted = item.value >= 1000 ? (item.value / 1000).toFixed(1) + 'k' : item.value;
        return `
            <div class="vbar-item">
               <div class="vbar-value">${valFormatted}</div>
               <div class="vbar-pill" style="height: ${item.percent}%;" title="${item.label}: Rs. ${item.value.toLocaleString()}"></div>
               <div class="vbar-label">${item.label}</div>
            </div>
        `;
    }).join('');

    if (!window.dbTotalRevenue) {
        const peakEl = document.getElementById('peakRevVal');
        const avgEl = document.getElementById('avgRevVal');
        if (period === 'monthly') {
            if (peakEl) peakEl.textContent = 'Rs. 24,500.00';
            if (avgEl) avgEl.textContent = 'Rs. 16,400.00';
        } else if (period === 'quarterly') {
            if (peakEl) peakEl.textContent = 'Rs. 64,900.00';
            if (avgEl) avgEl.textContent = 'Rs. 46,875.00';
        } else if (period === 'yearly') {
            if (peakEl) peakEl.textContent = 'Rs. 187,500.00';
            if (avgEl) avgEl.textContent = 'Rs. 133,500.00';
        }
    }
}

function updateVisualRevenueChart(period, btn) {
    document.querySelectorAll('#revenueChartTabs .nav-link').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderVisualRevenueBarChart(period);
}

// Render Admin Products Table
function renderAdminProducts() {
    const products = getProducts();
    const tbody = document.getElementById('adminProductTableBody');
    const totalCount = document.getElementById('totalProductsCount');
    
    if (totalCount) totalCount.textContent = products.length;
    if (!tbody) return;

    tbody.innerHTML = products.map(prod => {
        const tagBadges = (prod.tags || []).map(t => `<span class="badge-tag">${t}</span>`).join('');
        return `
            <tr>
               <td><img src="${prod.img}" alt="${prod.title}" onerror="this.src='img/menu/1.jpg'"/></td>
               <td>
                  <strong class="text-dark d-block">${prod.title}</strong>
                  <small class="text-muted" style="font-size:0.8rem;">${prod.desc.substring(0, 45)}...</small>
               </td>
               <td><span class="badge bg-light text-dark border">${prod.cat}</span></td>
               <td class="fw-bold text-danger">${prod.price}</td>
               <td class="text-muted text-decoration-line-through">${prod.oldPrice || '-'}</td>
               <td>${tagBadges}</td>
               <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick="openEditProductModal('${prod.id}')"><i class="fas fa-edit me-1"></i>Edit</button>
                  <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="deleteProduct('${prod.id}')"><i class="fas fa-trash-alt me-1"></i>Delete</button>
               </td>
            </tr>
        `;
    }).join('');
}

// Modal Control - Add Category
function openAddCategoryModal() {
    const modalTitle = document.getElementById('categoryModalTitle');
    if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-plus-circle text-danger me-2"></i>Add New Category';

    document.getElementById('editCategoryId').value = '';
    document.getElementById('catName').value = '';
    document.getElementById('catImg').value = 'img/category/1.jpg';
    document.getElementById('catDesc').value = '';

    const insertBtn = document.querySelector('button[name="insert_category"]');
    const updateBtn = document.querySelector('button[name="update_category"]');
    if (insertBtn) insertBtn.style.display = 'inline-block';
    if (updateBtn) updateBtn.style.display = 'none';

    const modalEl = document.getElementById('categoryModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function openEditCategoryModal(id, name, image, desc) {
    const modalTitle = document.getElementById('categoryModalTitle');
    if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-edit text-danger me-2"></i>Edit Category Details';

    document.getElementById('editCategoryId').value = id || '';
    document.getElementById('catName').value = name || '';
    document.getElementById('catImg').value = image || 'img/category/1.jpg';
    document.getElementById('catDesc').value = desc || '';

    const insertBtn = document.querySelector('button[name="insert_category"]');
    const updateBtn = document.querySelector('button[name="update_category"]');
    if (insertBtn) insertBtn.style.display = 'none';
    if (updateBtn) updateBtn.style.display = 'inline-block';

    const modalEl = document.getElementById('categoryModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function openEditCategoryModalFromBtn(btn) {
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const image = btn.getAttribute('data-image');
    const desc = btn.getAttribute('data-desc');
    openEditCategoryModal(id, name, image, desc);
}

// Modal Control - Add Product
function openAddProductModal() {
    document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-plus-circle text-danger me-2"></i>Add New Product';
    document.getElementById('editProductId').value = '';
    document.getElementById('prodTitle').value = '';
    document.getElementById('prodCat').value = 'Yarn & Crochet';
    document.getElementById('prodPrice').value = '';
    document.getElementById('prodOldPrice').value = '';
    document.getElementById('prodImg').value = 'img/menu/1.jpg';
    document.getElementById('prodDesc').value = '';
    document.getElementById('prodTags').value = '';

    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

// Save / Update Product
function saveProduct(e) {
    // Allow standard HTML POST form submission to Admin.php so PHP receives $_POST['insert'] / $_POST['update']
    return true;
}

// Delete Product
function deleteProduct(id) {
    if (confirm('Are you sure you want to remove this product from the store catalog?')) {
        let products = getProducts();
        products = products.filter(p => p.id !== id);
        saveProductsList(products);
        renderAdminProducts();
    }
}

// ================= ADMIN TEAM MANAGEMENT =================
function renderAdminTeamTable() {
    const team = getAdminTeam();
    const tbody = document.getElementById('adminTeamTableBody');
    if (!tbody) return;

    tbody.innerHTML = team.map(member => `
        <tr>
           <td>
              <strong class="text-dark d-block">${member.name}</strong>
           </td>
           <td class="text-muted small">${member.email}</td>
           <td><span class="badge bg-danger text-white px-2 py-1">${member.role}</span></td>
           <td class="text-end">
              ${member.email === 'admin@glamhandy.com' ? '<span class="text-muted small">Primary Admin</span>' : `<button class="btn btn-sm btn-outline-danger rounded-pill" onclick="deleteAdminMember('${member.id}')"><i class="fas fa-trash me-1"></i>Remove</button>`}
           </td>
        </tr>
    `).join('');
}

function openAddAdminModal() {
    document.getElementById('newAdminName').value = '';
    document.getElementById('newAdminEmail').value = '';
    document.getElementById('newAdminPass').value = '';
    document.getElementById('newAdminRole').value = 'Co-Admin';

    const modal = new bootstrap.Modal(document.getElementById('addAdminModal'));
    modal.show();
}

function saveNewAdminMember(e) {
    e.preventDefault();
    const name = document.getElementById('newAdminName').value.trim();
    const email = document.getElementById('newAdminEmail').value.trim().toLowerCase();
    const password = document.getElementById('newAdminPass').value;
    const role = document.getElementById('newAdminRole').value;

    let team = getAdminTeam();
    if (team.some(m => m.email === email)) {
        alert('An admin member with this email address already exists!');
        return;
    }

    const newAdmin = {
        id: 'adm-' + Date.now(),
        name,
        email,
        role
    };

    team.push(newAdmin);
    saveAdminTeamList(team);

    // Register admin credentials so they can log in
    const extraAdmins = JSON.parse(localStorage.getItem('glamhandy_extra_admins')) || [];
    extraAdmins.push({ email, password, role: 'admin', name });
    localStorage.setItem('glamhandy_extra_admins', JSON.stringify(extraAdmins));

    renderAdminTeamTable();

    const modalEl = document.getElementById('addAdminModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    alert(`Successfully added ${name} to the Admin Panel team!`);
}

function deleteAdminMember(id) {
    if (confirm('Are you sure you want to remove this admin member from the team?')) {
        let team = getAdminTeam();
        team = team.filter(m => m.id !== id);
        saveAdminTeamList(team);
        renderAdminTeamTable();
    }
}

// Change Admin Password
function handleAdminPasswordChange(e) {
    e.preventDefault();
    const np = document.getElementById('adminNewPass').value;
    const cp = document.getElementById('adminConfPass').value;

    if (np !== cp) {
        alert('New password and confirmation password do not match!');
        return;
    }

    document.getElementById('adminCurrPass').value = '';
    document.getElementById('adminNewPass').value = '';
    document.getElementById('adminConfPass').value = '';

    const alertEl = document.getElementById('adminPassAlert');
    if (alertEl) {
        alertEl.classList.remove('d-none');
        setTimeout(() => alertEl.classList.add('d-none'), 4000);
    }
}
