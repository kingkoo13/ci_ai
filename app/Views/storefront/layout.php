<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Storefront</title>
    <!-- FontAwesome & Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Storefront style sheet -->
    <link href="<?= base_url('assets/css/storefront.css') ?>" rel="stylesheet">
</head>
<body class="page-layout-<?= $pageLayout ?? '1column' ?>">

    <!-- Top header panel links -->
    <div class="panel header">
        <div class="header-links">
            <?php if (session()->get('customer_logged_in')) : ?>
                <li><span>Welcome, <strong><?= esc(session()->get('customer_firstname') . ' ' . session()->get('customer_lastname')) ?></strong>!</span></li>
                <li><a href="<?= base_url('customer/account') ?>"><i class="fa-regular fa-user"></i> My Account</a></li>
                <li><a href="<?= base_url('customer/account/logout') ?>"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a></li>
            <?php else : ?>
                <li><span>Default welcome msg!</span></li>
                <li><a href="<?= base_url('customer/account/login') ?>">Sign In</a></li>
                <li><a href="<?= base_url('customer/account/register') ?>">Create an Account</a></li>
            <?php endif; ?>
            <li><a href="<?= base_url('admin/dashboard') ?>" target="_blank" style="color: var(--color-primary); font-weight:700;"><i class="fa-solid fa-gauge-high"></i> Go to Admin Panel</a></li>
        </div>
    </div>

    <!-- Main Header -->
    <header class="header content">
        <a href="<?= base_url('/') ?>" class="logo-text">LUMA<span>CI</span></a>

        <div class="search-minicart-wrapper">
            <!-- Search -->
            <form action="<?= base_url('catalog/search') ?>" class="search-form" method="GET" style="display:none;">
                <input type="text" name="q" placeholder="Search entire store here..." class="search-input">
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <!-- Minicart -->
            <div class="minicart-wrapper" id="minicart-wrapper">
                <div class="minicart-action" onclick="toggleMinicart()">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php 
                    $cartSession = session()->get('cart') ?: [];
                    $totalItems = 0;
                    foreach ($cartSession as $item) {
                        $totalItems += $item['qty'];
                    }
                    ?>
                    <span class="minicart-badge" id="minicart-count"><?= $totalItems ?></span>
                </div>

                <!-- Minicart Popover markup -->
                <div id="minicart-popover" popover="auto" style="display:none;">
                    <!-- Dynamically populated via AJAX fetch -->
                    <div class="minicart-header">
                        <span>My Cart</span>
                        <button type="button" onclick="document.getElementById('minicart-popover').hidePopover()" style="border:none; background:none; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div id="minicart-content">
                        <p style="color:var(--color-text-muted); font-style:italic;">Loading cart items...</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation categories menu bar -->
    <div class="nav-sections">
        <ul class="navigation-list">
            <li class="navigation-item"><a href="<?= base_url('/') ?>">Home</a></li>
            <?php 
            $db = \Config\Database::connect();
            $navCategories = $db->table('categories')
                                 ->where('parent_id', 1)
                                 ->where('is_active', 1)
                                 ->get()
                                 ->getResult();
            foreach ($navCategories as $cat) : 
            ?>
                <li class="navigation-item"><a href="<?= base_url('category/' . $cat->id) ?>"><?= esc($cat->name) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Alert Notifications messages block -->
    <div class="alert-messages">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
    </div>

    <!-- Layout Grid columns wrapper -->
    <div class="page-main">
        <div class="columns-wrapper">
            
            <!-- Left Sidebar -->
            <?php if (isset($pageLayout) && ($pageLayout === '2columns-left' || $pageLayout === '3columns')) : ?>
                <aside class="sidebar left" aria-label="Left sidebar options">
                    <?= $this->renderSection('sidebar_left') ?>
                </aside>
            <?php endif; ?>

            <!-- Main Content Area -->
            <main class="column main">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Right Sidebar -->
            <?php if (isset($pageLayout) && ($pageLayout === '2columns-right' || $pageLayout === '3columns')) : ?>
                <aside class="sidebar right" aria-label="Right sidebar options">
                    <?= $this->renderSection('sidebar_right') ?>
                </aside>
            <?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-box">
                <div class="footer-box-title">About Store</div>
                <p style="color:var(--color-text-light); line-height:1.6;">We recreate Magento's dynamic UI layout configurations using lightweight and secure CodeIgniter 4 controllers.</p>
            </div>
            <div class="footer-box">
                <div class="footer-box-title">Custom Links</div>
                <ul>
                    <li><a href="<?= base_url('page/about-us') ?>">About Us</a></li>
                    <li><a href="<?= base_url('page/privacy-policy') ?>">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-box">
                <div class="footer-box-title">Contact Support</div>
                <p style="color:var(--color-text-light);">Email: support@example.com<br>Phone: +1 (555) 123-4567</p>
            </div>
        </div>
        <div style="max-width:1280px; margin:25px auto 0; padding-top:15px; border-top:1px solid var(--color-border-light); text-align:center; color:var(--color-text-muted); font-size:12px;">
            &copy; <?= date('Y') ?> LUMACI Storefront. All rights reserved.
        </div>
    </footer>

    <!-- Minicart dynamic javascript scripts -->
    <script>
        function toggleMinicart() {
            const popover = document.getElementById('minicart-popover');
            if (!popover) return;

            // Toggle show
            if (popover.matches(':popover-open')) {
                popover.hidePopover();
            } else {
                popover.style.display = 'block';
                popover.showPopover();
                
                // Fetch dynamic cart contents via AJAX
                const content = document.getElementById('minicart-content');
                content.innerHTML = '<p style="color:var(--color-text-muted); font-style:italic; padding:10px;">Loading items...</p>';
                
                fetch('<?= base_url('cart/minicart') ?>')
                    .then(response => response.text())
                    .then(html => {
                        content.innerHTML = html;
                    })
                    .catch(err => {
                        content.innerHTML = '<p style="color:var(--color-danger); padding:10px;">Failed to load cart.</p>';
                    });
            }
        }
    </script>
</body>
</html>
