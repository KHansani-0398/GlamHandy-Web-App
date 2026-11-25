AOS.init({
    duration: 680,
    once: true,
    offset: 55
});

/* NAVBAR SCROLL & ACTIVE LINK  */
window.addEventListener('scroll', function () {
    document.getElementById('nav').classList.toggle('scrolled', window.scrollY > 60);
    document.getElementById('btt').classList.toggle('show', window.scrollY > 300);
    document.querySelectorAll('section[id]').forEach(function (sec) {
        var top = sec.offsetTop - 110,
            bot = top + sec.offsetHeight;
        if (window.scrollY >= top && window.scrollY < bot) {
            document.querySelectorAll('.nav-link').forEach(function (l) {
                l.classList.remove('active');
            });
            var lnk = document.querySelector('.nav-link[href="#' + sec.id + '"]');
            if (lnk) lnk.classList.add('active');
        }
    });
});

/*  SMOOTH SCROLL + MOBILE NAV CLOSE  */
document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
        var href = this.getAttribute('href');
        if (href === '#') return;
        var t = document.querySelector(href);
        if (t) {
            e.preventDefault();
            // Close Bootstrap mobile navbar if open
            var navCollapse = document.getElementById('navmenu');
            if (navCollapse && navCollapse.classList.contains('show')) {
                var bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                } else {
                    navCollapse.classList.remove('show');
                }
            }
            // Scroll after slight delay to let navbar close
            setTimeout(function () {
                window.scrollTo({
                    top: t.offsetTop - 78,
                    behavior: 'smooth'
                });
            }, 50);
        }
    });
});


var searchOv = document.getElementById('searchOv');
var navSearchBtn = document.getElementById('navSearchBtn');

if (navSearchBtn) {
    navSearchBtn.addEventListener('click', function () {
        if (searchOv) {
            searchOv.classList.add('open');
            document.body.style.overflow = 'hidden';
            setTimeout(function () {
                var sInput = document.getElementById('searchInput');
                if (sInput) sInput.focus();
            }, 220);
        }
    });
}

const searchCloseEl = document.getElementById('searchClose');
if (searchCloseEl) {
    searchCloseEl.addEventListener('click', closeSearch);
}

// Close when clicking backdrop
if (searchOv) {
    searchOv.addEventListener('click', function (e) {
        if (e.target === searchOv) closeSearch();
    });
}

function closeSearch() {
    if (searchOv) searchOv.classList.remove('open');
    document.body.style.overflow = '';
}

// Search Products Function - Directs search query to catalog section
function searchProducts(query) {
    if (!query) return;
    query = query.trim().toLowerCase();

    // Check if on catalog page, otherwise redirect to catalog with search parameter
    if (!window.location.pathname.includes('catalog.php') && !window.location.pathname.includes('catalog.html')) {
        window.location.href = 'catalog.php?search=' + encodeURIComponent(query);
        return;
    }

    closeSearch();

    let foundCount = 0;
    document.querySelectorAll('.mwrap').forEach(function (w) {
        var card = w.querySelector('.mcard');
        if (!card) return;
        var title = (card.getAttribute('data-title') || '').toLowerCase();
        var cat = (card.getAttribute('data-cat') || '').toLowerCase();
        var desc = (card.getAttribute('data-desc') || '').toLowerCase();
        var tags = (card.getAttribute('data-tags') || '').toLowerCase();

        if (title.includes(query) || cat.includes(query) || desc.includes(query) || tags.includes(query)) {
            w.classList.remove('gone');
            w.style.opacity = '1';
            w.style.transform = 'translateY(0)';
            foundCount++;
        } else {
            w.classList.add('gone');
        }
    });

    const menuSec = document.getElementById('menu');
    if (menuSec) {
        setTimeout(function () {
            menuSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
    }
}

// Category buttons inside search box -> direct to relevant section in catalog
document.querySelectorAll('.sovcat').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var f = this.getAttribute('data-cat');
        if (!window.location.pathname.includes('catalog.php') && !window.location.pathname.includes('catalog.html')) {
            window.location.href = 'catalog.php?filter=' + encodeURIComponent(f);
            return;
        }
        document.querySelectorAll('.sovcat').forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        closeSearch();
        setTimeout(function () {
            filterMenu(f);
            const menuSec = document.getElementById('menu');
            if (menuSec) menuSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 200);
    });
});

// Trending tags click -> perform search and scroll to catalog section
document.querySelectorAll('.sovtrend .ttag').forEach(function (t) {
    t.addEventListener('click', function () {
        const val = this.textContent.trim();
        const sInput = document.getElementById('searchInput');
        if (sInput) sInput.value = val;
        searchProducts(val);
    });
});

// Search input Enter key & Submit button
const searchSubmitBtn = document.querySelector('.sovinput button');
if (searchSubmitBtn) {
    searchSubmitBtn.addEventListener('click', function () {
        const val = document.getElementById('searchInput').value;
        if (val) searchProducts(val);
    });
}

const searchInputEl = document.getElementById('searchInput');
if (searchInputEl) {
    searchInputEl.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value;
            if (val) searchProducts(val);
        }
    });
}


$(document).ready(function () {
    $('.magnific_popup').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false,
        disableOn: 300
    });
});


function filterMenu(cat) {
    var normCat = (cat === 'yarncrochet') ? 'yarn' : cat;
    // sync filter buttons
    document.querySelectorAll('.filtbtn').forEach(function (b) {
        var f = b.getAttribute('data-f');
        var normF = (f === 'yarncrochet') ? 'yarn' : f;
        b.classList.toggle('active', normF === normCat);
    });
    // sync category cards
    document.querySelectorAll('.catcard').forEach(function (c) {
        var f = c.getAttribute('data-filter');
        var normF = (f === 'yarncrochet') ? 'yarn' : f;
        c.classList.toggle('active', normF === normCat);
    });
    // show/hide menu cards
    document.querySelectorAll('.mwrap').forEach(function (w) {
        var c = w.getAttribute('data-c');
        var normC = (c === 'yarncrochet') ? 'yarn' : c;
        if (normCat === 'all' || normC === normCat || normC === 'all') {
            w.classList.remove('gone');
            w.style.opacity = '0';
            w.style.transform = 'translateY(16px)';
            setTimeout(function () {
                w.style.transition = 'opacity .38s,transform .38s';
                w.style.opacity = '1';
                w.style.transform = 'translateY(0)';
            }, 60);
        } else {
            w.classList.add('gone');
        }
    });
}

// Catalog Dynamic Rendering & Sorting (Price Low-High, Price High-Low, Newest)
function renderCatalogGrid() {
    const grid = document.getElementById('mgrid');
    if (!grid) return;

    // If on catalog.php, preserve the live MySQL products rendered by PHP
    const isPhpCatalog = window.location.pathname.toLowerCase().includes('catalog.php');

    if (isPhpCatalog) {
        document.querySelectorAll('#mgrid .mcard').forEach(function (card) {
            card.addEventListener('click', function (e) {
                if (e.target.closest('.mhrt')) {
                    e.stopPropagation();
                    var ico = e.target.closest('.mhrt').querySelector('i');
                    ico.classList.toggle('fas');
                    ico.classList.toggle('far');
                    return;
                }
                openMenuPop(this);
            });
        });
        return;
    }

    const storedProds = localStorage.getItem('glamhandy_products');
    if (!storedProds) return;

    let products = JSON.parse(storedProds);
    const sortSelect = document.getElementById('catalogSortSelect');
    const sortVal = sortSelect ? sortSelect.value : 'default';

    if (sortVal === 'price-asc') {
        products.sort((a, b) => parseFloat(a.price.replace(/[^0-9.]/g, '')) - parseFloat(b.price.replace(/[^0-9.]/g, '')));
    } else if (sortVal === 'price-desc') {
        products.sort((a, b) => parseFloat(b.price.replace(/[^0-9.]/g, '')) - parseFloat(a.price.replace(/[^0-9.]/g, '')));
    } else if (sortVal === 'newest') {
        products.sort((a, b) => (b.badge === 'New' ? 1 : 0) - (a.badge === 'New' ? 1 : 0));
    }

    const catKeyMap = {
        'Yarn & Crochet': 'yarn',
        'Beads & Jewellery': 'jewelry',
        'Bag Accessories': 'bags',
        'Craft Tools': 'tools',
        'Craft Materials': 'materials'
    };

    grid.innerHTML = products.map((prod) => {
        const cKey = prod.categoryKey || catKeyMap[prod.cat] || 'yarn';
        const badgeHtml = prod.badge ? `<div class="mbdg ${prod.badge.toLowerCase().includes('new') ? 'new' : (prod.badge.toLowerCase().includes('hot') ? 'hot' : '')}"><i class="fas fa-star"></i> ${prod.badge}</div>` : '';
        const oldHtml = prod.oldPrice ? `<small>${prod.oldPrice}</small>` : '';

        return `
            <div class="col-sm-6 col-lg-4 mwrap" data-c="${cKey}">
               <div class="mcard"
                  data-img="${prod.img}"
                  data-title="${prod.title}"
                  data-cat="${prod.cat}"
                  data-price="${prod.price}" data-old="${prod.oldPrice || ''}"
                  data-desc="${prod.desc}"
                  data-tags="${(prod.tags || []).join(',')}">
                  <div class="mimg">
                     <img src="${prod.img}" alt="${prod.title}" onerror="this.src='img/menu/1.jpg'"/>
                     ${badgeHtml}
                     <div class="mhrt"><i class="far fa-heart"></i></div>
                  </div>
                  <div class="mbody">
                     <div class="mcat">${prod.cat}</div>
                     <div class="mtit">${prod.title}</div>
                     <div class="mdesc">${prod.desc}</div>
                     <div class="mfoot">
                        <div>
                           <div class="mprice">${prod.price} ${oldHtml}</div>
                        </div>
                        <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                     </div>
                  </div>
               </div>
            </div>
        `;
    }).join('');

    // Re-bind click listeners for newly created product cards
    document.querySelectorAll('#mgrid .mcard').forEach(function (card) {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.mhrt')) {
                e.stopPropagation();
                var ico = e.target.closest('.mhrt').querySelector('i');
                ico.classList.toggle('fas');
                ico.classList.toggle('far');
                return;
            }
            openMenuPop(this);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const sortSelect = document.getElementById('catalogSortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', renderCatalogGrid);
    }
    renderCatalogGrid();

    // Check URL parameters for search or filter redirection
    const urlParams = new URLSearchParams(window.location.search);
    const filterParam = urlParams.get('filter');
    const searchParam = urlParams.get('search');

    if (filterParam) {
        setTimeout(function () {
            filterMenu(filterParam);
            const menuSec = document.getElementById('menu');
            if (menuSec) menuSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 250);
    } else if (searchParam) {
        const sInput = document.getElementById('searchInput');
        if (sInput) sInput.value = searchParam;
        setTimeout(function () {
            searchProducts(searchParam);
        }, 250);
    }

    // Filter buttons
    document.querySelectorAll('.filtbtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filtbtn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterMenu(this.getAttribute('data-f'));
        });
    });

    // Category section cards -> scroll + filter
    document.querySelectorAll('.catcard').forEach(function (card) {
        card.addEventListener('click', function () {
            var f = this.getAttribute('data-filter');
            window.scrollTo({
                top: document.getElementById('menu').offsetTop - 80,
                behavior: 'smooth'
            });
            setTimeout(function () {
                filterMenu(f);
            }, 480);
        });
    });
});


var menuPop = document.getElementById('menuPop');
var mpQty = 1;
var currentPopupItem = null;

function openMenuPop(card) {
    var pid = card.getAttribute('data-id');
    var img = card.getAttribute('data-img');
    var title = card.getAttribute('data-title');
    var cat = card.getAttribute('data-cat');
    var price = card.getAttribute('data-price');
    var old = card.getAttribute('data-old');
    var rating = parseFloat(card.getAttribute('data-rating'));
    var reviews = card.getAttribute('data-reviews');
    var cal = card.getAttribute('data-cal');
    var time = card.getAttribute('data-time');
    var desc = card.getAttribute('data-desc');
    var tags = card.getAttribute('data-tags') || '';

    if (document.getElementById('mpProductIdVal')) {
        document.getElementById('mpProductIdVal').value = pid;
    }

    document.getElementById('mpImg').setAttribute('src', img);
    document.getElementById('mpCat').textContent = cat;
    document.getElementById('mpTitle').textContent = title;

    if (document.getElementById('mpStars')) {
        document.getElementById('mpStars').innerHTML = '';
    }

    document.getElementById('mpDesc').textContent = desc;

    document.getElementById('mpPrice').innerHTML =
        price + (old ? '<small style="color:#ccc;text-decoration:line-through;margin-left:8px;font-size:1rem;">' + old + '</small>' : '');

    if (document.getElementById('mpMeta')) {
        document.getElementById('mpMeta').innerHTML = '';
    }

    document.getElementById('mpTags').innerHTML =
        tags.split(',').filter(Boolean).map(function (t) {
            return '<span class="mptag">' + t.trim() + '</span>';
        }).join('');

    mpQty = 1;
    document.getElementById('mpQnum').textContent = 1;
    if (document.getElementById('mpQuantityVal')) {
        document.getElementById('mpQuantityVal').value = 1;
    }
    document.getElementById('mpAddCart').innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
    document.getElementById('mpAddCart').style.background = '';

    currentPopupItem = {
        title: title,
        price: parseFloat(price.replace(/[^0-9.]/g, '')), // Extract number from price string
        img: img
    };

    menuPop.classList.add('open');
    document.body.style.overflow = 'hidden';
}

// Card click open popup
document.querySelectorAll('.mcard').forEach(function (card) {
    card.addEventListener('click', function () {
        openMenuPop(this);
    });
});

// + button  open popup (stop propagation to avoid double firing)
document.querySelectorAll('.madd').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        openMenuPop(this.closest('.mcard'));
    });
});

// Heart toggle (no popup)
document.querySelectorAll('.mhrt').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var ico = this.querySelector('i');
        ico.classList.toggle('far');
        ico.classList.toggle('fas');
        this.style.color = ico.classList.contains('fas') ? 'var(--primary)' : '#ccc';
    });
});

// Close popup
const mpCloseEl = document.getElementById('mpClose');
if (mpCloseEl) {
    mpCloseEl.addEventListener('click', closeMenuPop);
}
if (menuPop) {
    menuPop.addEventListener('click', function (e) {
        if (e.target === this) closeMenuPop();
    });
}

function closeMenuPop() {
    if (menuPop) menuPop.classList.remove('open');
    document.body.style.overflow = '';
}

// Qty +/-
const mpPlusEl = document.getElementById('mpPlus');
if (mpPlusEl) {
    mpPlusEl.addEventListener('click', function () {
        mpQty++;
        document.getElementById('mpQnum').textContent = mpQty;
        if (document.getElementById('mpQuantityVal')) {
            document.getElementById('mpQuantityVal').value = mpQty;
        }
    });
}
const mpMinusEl = document.getElementById('mpMinus');
if (mpMinusEl) {
    mpMinusEl.addEventListener('click', function () {
        if (mpQty > 1) {
            mpQty--;
            document.getElementById('mpQnum').textContent = mpQty;
            if (document.getElementById('mpQuantityVal')) {
                document.getElementById('mpQuantityVal').value = mpQty;
            }
        }
    });
}

// Add to cart button
const mpAddCartEl = document.getElementById('mpAddCart');
if (mpAddCartEl) {
    mpAddCartEl.addEventListener('click', function (e) {
        const userStr = localStorage.getItem('currentUser');
        if (!userStr) {
            e.preventDefault();
            alert("Please log in to add items to your cart.");
            const pid = document.getElementById('mpProductIdVal') ? document.getElementById('mpProductIdVal').value : '';
            const targetUrl = "catalog.php" + (pid ? "?open_product=" + pid : "");
            window.location.href = "login.php?notice=login_required&product_id=" + pid + "&redirect=" + encodeURIComponent(targetUrl);
            return;
        }

        // Store item in localStorage for cart counter
        let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
        if (typeof currentPopupItem !== 'undefined' && currentPopupItem) {
            cart.push({
                title: currentPopupItem.title,
                price: currentPopupItem.price,
                img: currentPopupItem.img,
                qty: mpQty
            });
            localStorage.setItem('cartItems', JSON.stringify(cart));
        }

        // Let form submit naturally to orders.php (records order in database & opens profile)
        const form = document.getElementById('orderSubmitForm');
        if (form) {
            form.submit();
        }
    });
}


const resBtnEl = document.getElementById('resBtn');
if (resBtnEl) {
    resBtnEl.addEventListener('click', function () {
        var btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
        btn.disabled = true;
        setTimeout(function () {
            btn.innerHTML = '<i class="fas fa-calendar-check"></i> Confirm Reservation';
            btn.disabled = false;
            var ok = document.getElementById('resOk');
            ok.style.display = 'block';
            ok.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }, 1500);
    });
}



var galPop = document.getElementById('galPop');
var galData = [];
var galIdx = 0;

document.querySelectorAll('.gitem').forEach(function (item) {
    galData.push({
        img: item.getAttribute('data-gimg'),
        title: item.getAttribute('data-gtitle'),
        desc: item.getAttribute('data-gdesc')
    });
    item.addEventListener('click', function () {
        openGal(parseInt(this.getAttribute('data-gi')));
    });
});

function openGal(i) {
    galIdx = i;
    var g = galData[i];
    var gpImg = document.getElementById('gpImg');
    var gpTitle = document.getElementById('gpTitle');
    var gpDesc = document.getElementById('gpDesc');
    if (gpImg) gpImg.setAttribute('src', g.img);
    if (gpTitle) gpTitle.textContent = g.title;
    if (gpDesc) gpDesc.innerHTML = g.desc;
    if (galPop) {
        galPop.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

const gpCloseEl = document.getElementById('gpClose');
if (gpCloseEl) {
    gpCloseEl.addEventListener('click', closeGal);
}
if (galPop) {
    galPop.addEventListener('click', function (e) {
        if (e.target === this) closeGal();
    });
}

function closeGal() {
    if (galPop) galPop.classList.remove('open');
    document.body.style.overflow = '';
}

const gpPrevEl = document.getElementById('gpPrev');
if (gpPrevEl) {
    gpPrevEl.addEventListener('click', function () {
        openGal((galIdx - 1 + galData.length) % galData.length);
    });
}
const gpNextEl = document.getElementById('gpNext');
if (gpNextEl) {
    gpNextEl.addEventListener('click', function () {
        openGal((galIdx + 1) % galData.length);
    });
}

/*  ESC key closes everything */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeSearch();
        closeMenuPop();
        closeGal();
        if (typeof $.magnificPopup !== 'undefined') $.magnificPopup.close();
    }
});


if (document.querySelector('.tesSwiper')) {
    new Swiper('.tesSwiper', {
        slidesPerView: 3,
        spaceBetween: 25,
        loop: true,
        grabCursor: true,
        observer: true,
        observeParents: true,
        speed: 800,
        autoplay: {
            delay: 4500,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.tes-next',
            prevEl: '.tes-prev'
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            480: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 25
            }
        }
    });
}


var cH = 8,
    cM = 45,
    cS = 30;
setInterval(function () {
    cS--;
    if (cS < 0) {
        cS = 59;
        cM--;
    }
    if (cM < 0) {
        cM = 59;
        cH--;
    }
    if (cH < 0) {
        cH = 8;
        cM = 45;
        cS = 30;
    }
    document.getElementById('cdH').textContent = String(cH).padStart(2, '0');
    document.getElementById('cdM').textContent = String(cM).padStart(2, '0');
    document.getElementById('cdS').textContent = String(cS).padStart(2, '0');
}, 1000);

/* â”€â”€ NEWSLETTER â”€â”€ */
const nlBtnEl = document.getElementById('nlBtn');
if (nlBtnEl) {
    nlBtnEl.addEventListener('click', function () {
        var email = document.getElementById('nlEmail').value;
        if (email && email.includes('@')) {
            var btn = this;
            btn.textContent = 'âœ“ Subscribed!';
            btn.style.background = '#4ade80';
            btn.style.color = '#222';
            document.getElementById('nlEmail').value = '';
            setTimeout(function () {
                btn.textContent = 'Subscribe';
                btn.style.background = '';
                btn.style.color = '';
            }, 3000);
        }
    });
}

/*  NUMBER COUNTER ANIMATION*/
var numAnimated = false;
window.addEventListener('scroll', function () {
    var hero = document.getElementById('hero');
    if (!numAnimated && hero && window.scrollY > hero.offsetHeight - 300) {
        numAnimated = true;
        document.querySelectorAll('.snum').forEach(function (el) {
            var txt = el.textContent;
            var num = parseInt(txt);
            var suf = txt.replace(/[0-9]/g, '');
            if (isNaN(num)) return;
            var start = 0;
            var step = Math.ceil(num / 55);
            var iv = setInterval(function () {
                start += step;
                if (start >= num) {
                    start = num;
                    clearInterval(iv);
                }
                el.textContent = start + suf;
            }, 1400 / 55);
        });
    }
});
// Initialize cart counter on load
document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
    let count = cart.reduce((acc, item) => acc + item.qty, 0);
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) {
        cartCountEl.textContent = count;
    }
});

// Handle logged-in UI state & session synchronization
document.addEventListener('DOMContentLoaded', () => {
    // Sync PHP session state with client UI
    fetch('check_session.php')
        .then(res => res.json())
        .then(data => {
            if (data && data.loggedIn) {
                localStorage.setItem('currentUser', JSON.stringify(data));
                updateNavUserUI(data);
            } else {
                localStorage.removeItem('currentUser');
            }
        })
        .catch(err => {
            const userStr = localStorage.getItem('currentUser');
            if (userStr) {
                updateNavUserUI(JSON.parse(userStr));
            }
        });

    // Attach logout click listener to logout buttons
    document.querySelectorAll('.btn-logout-action, a[href="login.php"].btn-logout').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            logoutUser();
        });
    });
});

function updateNavUserUI(user) {
    const navLoginBtns = document.querySelectorAll('.nav-login');
    const navContainer = document.querySelector('#nav .d-flex.align-items-center.gap-2');

    if (!navContainer) return;

    if (user.role === 'admin') {
        navLoginBtns.forEach(btn => btn.style.display = 'none');
        if (!document.getElementById('dynNavAdmin') && !document.getElementById('adminWelcome') && !navContainer.querySelector('a[href="Admin.php"]')) {
            const a = document.createElement('a');
            a.id = 'dynNavAdmin';
            a.href = 'Admin.php';
            a.className = 'btn btn-sm btn-outline-danger rounded-pill me-1';
            a.innerHTML = '<i class="fas fa-user-shield me-1"></i>Admin Panel';
            const ctaBtn = navContainer.querySelector('.nav-cta');
            if (ctaBtn) {
                navContainer.insertBefore(a, ctaBtn);
            } else {
                navContainer.appendChild(a);
            }
        }
    } else {
        navLoginBtns.forEach(btn => btn.style.display = 'none');
        if (!document.getElementById('dynNavUser') && !navContainer.querySelector('a[href="user.php"]')) {
            const a = document.createElement('a');
            a.id = 'dynNavUser';
            a.href = 'user.php';
            a.className = 'text-dark small fw-bold me-2 text-decoration-none';
            a.title = 'Go to My Profile';
            a.innerHTML = '<i class="fas fa-user-circle me-1" style="color:var(--primary);font-size:1.15rem;"></i>' + (user.email || 'User Profile');
            const ctaBtn = navContainer.querySelector('.nav-cta');
            if (ctaBtn) {
                navContainer.insertBefore(a, ctaBtn);
            } else {
                navContainer.appendChild(a);
            }
        }
    }
}

// Global Logout function that clears session and returns to Homepage (index.html)
function logoutUser() {
    localStorage.removeItem('currentUser');
    localStorage.removeItem('cartItems');
    window.location.href = 'logout.php';
}

// Protect ordering buttons when logged out
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nav-cta').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!localStorage.getItem('currentUser')) {
                e.preventDefault();
                alert('Please log in to add items to cart and order craft supplies.');
                const currentPg = window.location.pathname.split('/').pop() || 'catalog.php';
                window.location.href = 'login.php?error=login_required&redirect=' + encodeURIComponent(currentPg);
            }
        });
    });

    // Auto-open exact product modal if open_product parameter is in URL
    const urlParams = new URLSearchParams(window.location.search);
    const openPid = urlParams.get('open_product');
    if (openPid) {
        const targetCard = document.querySelector('.mcard[data-id="' + openPid + '"]');
        if (targetCard) {
            setTimeout(function () {
                openMenuPop(targetCard);
            }, 350);
        }
    }
});
