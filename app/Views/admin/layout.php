<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | Magento 2 CI Admin</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for Dashboard Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Layout Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="sidebar-logo">
                <a href="<?= base_url('admin/dashboard') ?>"><i class="fa-solid fa-cart-shopping"></i> <span class="menu-label">MAGNETO <span>CI</span></span></a>
            </div>
            
            <nav class="sidebar-menu">
                <ul>
                    <!-- Dashboard -->
                    <li class="menu-item <?= ($menu == 'dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/dashboard') ?>" class="menu-link">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span class="menu-label">Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Sales Group -->
                    <li class="menu-item <?= ($menu == 'sales') ? 'active' : '' ?>">
                        <div class="menu-link" onclick="toggleSubmenu(this)">
                            <i class="fa-solid fa-credit-card"></i>
                            <span class="menu-label">Sales</span>
                        </div>
                        <ul class="submenu-list">
                            <li class="submenu-item <?= ($submenu == 'orders') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/sales/orders') ?>" class="submenu-link">Orders</a>
                            </li>
                            <li class="submenu-item <?= ($submenu == 'invoices') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/sales/invoices') ?>" class="submenu-link">Invoices</a>
                            </li>
                            <li class="submenu-item <?= ($submenu == 'shipments') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/sales/shipments') ?>" class="submenu-link">Shipments</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Catalog Group -->
                    <li class="menu-item <?= ($menu == 'catalog') ? 'active' : '' ?>">
                        <div class="menu-link" onclick="toggleSubmenu(this)">
                            <i class="fa-solid fa-tags"></i>
                            <span class="menu-label">Catalog</span>
                        </div>
                        <ul class="submenu-list">
                            <li class="submenu-item <?= ($submenu == 'products') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/catalog/products') ?>" class="submenu-link">Products</a>
                            </li>
                            <li class="submenu-item <?= ($submenu == 'categories') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/catalog/categories') ?>" class="submenu-link">Categories</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Customers Group -->
                    <li class="menu-item <?= ($menu == 'customers') ? 'active' : '' ?>">
                        <div class="menu-link" onclick="toggleSubmenu(this)">
                            <i class="fa-solid fa-users"></i>
                            <span class="menu-label">Customers</span>
                        </div>
                        <ul class="submenu-list">
                            <li class="submenu-item <?= ($submenu == 'all_customers') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/customers') ?>" class="submenu-link">All Customers</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Content (CMS) Group -->
                    <li class="menu-item <?= ($menu == 'content') ? 'active' : '' ?>">
                        <div class="menu-link" onclick="toggleSubmenu(this)">
                            <i class="fa-solid fa-file-lines"></i>
                            <span class="menu-label">Content</span>
                        </div>
                        <ul class="submenu-list">
                            <li class="submenu-item <?= ($submenu == 'pages') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/content/pages') ?>" class="submenu-link">Pages</a>
                            </li>
                            <li class="submenu-item <?= ($submenu == 'blocks') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/content/blocks') ?>" class="submenu-link">Blocks</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Stores Group -->
                    <li class="menu-item <?= ($menu == 'stores') ? 'active' : '' ?>">
                        <div class="menu-link" onclick="toggleSubmenu(this)">
                            <i class="fa-solid fa-store"></i>
                            <span class="menu-label">Stores</span>
                        </div>
                        <ul class="submenu-list">
                            <li class="submenu-item <?= ($submenu == 'configuration') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/stores/configuration') ?>" class="submenu-link">Configuration</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- System Group -->
                    <li class="menu-item <?= ($menu == 'system') ? 'active' : '' ?>">
                        <div class="menu-link" onclick="toggleSubmenu(this)">
                            <i class="fa-solid fa-gears"></i>
                            <span class="menu-label">System</span>
                        </div>
                        <ul class="submenu-list">
                            <li class="submenu-item <?= ($submenu == 'cache') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/system/cache') ?>" class="submenu-link">Cache Management</a>
                            </li>
                            <li class="submenu-item <?= ($submenu == 'roles') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/system/roles') ?>" class="submenu-link">User Roles</a>
                            </li>
                            <li class="submenu-item <?= ($submenu == 'users') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/system/users') ?>" class="submenu-link">All Users</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Layout Right Pane -->
        <div class="main-container">
            <!-- Header Bar -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="toggle-sidebar-btn" id="toggle-sidebar" aria-label="Toggle Navigation Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <!-- Global Search Box -->
                    <search class="admin-search">
                        <form action="<?= base_url('admin/catalog/products') ?>" method="GET">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search" placeholder="Search catalog..." name="q" value="<?= esc($q ?? '') ?>">
                        </form>
                    </search>
                </div>
                
                <div class="header-right">
                    <!-- Notifications and Alerts -->
                    <div class="header-nav-icons">
                        <div class="nav-icon-wrapper" title="System Messages">
                            <i class="fa-regular fa-bell"></i>
                            <span class="badge">1</span>
                        </div>
                    </div>
                    
                    <!-- Profile dropdown -->
                    <div class="user-profile" id="user-profile-menu">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <span><?= esc(session()->get('admin_firstname') . ' ' . session()->get('admin_lastname')) ?></span>
                        <i class="fa-solid fa-caret-down"></i>
                        
                        <div id="profile-dropdown" popover="auto" style="margin-top: 45px; right: 25px; border: 1px solid var(--color-border); border-radius: var(--border-radius); padding: 10px; background: white; box-shadow: var(--shadow-md);">
                            <ul style="display: flex; flex-direction: column; gap: 8px;">
                                <li><a href="<?= base_url('admin/system/roles') ?>" style="color: var(--color-text);"><i class="fa-solid fa-user-gear"></i> Profile Settings</a></li>
                                <hr style="border: 0; border-top: 1px solid var(--color-border-light);">
                                <li><a href="<?= base_url('admin/logout') ?>" style="color: var(--color-danger);"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content Wrapper -->
            <main class="content-body">
                <!-- Session messages -->
                <div class="messages-container">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success">
                            <i class="fa-solid fa-circle-check"></i> <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-circle-xmark"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Main page body -->
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
    
    <!-- Sidebar Collapsing and Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');
        
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Restore collapse state on page load
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }

        // Toggle submenus manually
        function toggleSubmenu(element) {
            const menuItem = element.parentElement;
            menuItem.classList.toggle('active');
        }

        // Popover for profile dropdown using native Popover target
        const profileBtn = document.getElementById('user-profile-menu');
        const profileDropdown = document.getElementById('profile-dropdown');
        if (profileBtn && profileDropdown) {
            profileBtn.setAttribute('popovertarget', 'profile-dropdown');
            profileBtn.addEventListener('click', () => {
                profileDropdown.togglePopover();
            });
        }
    </script>
</body>
</html>
