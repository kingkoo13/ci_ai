<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>System Configuration<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= base_url('admin/stores/configuration/save') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Stores <span>/</span> Configuration
            </div>
            <h1 class="page-title">Configuration</h1>
        </div>
        <div class="page-actions">
            <button type="submit" class="btn btn-primary">Save Config</button>
        </div>
    </div>

    <!-- Tabbed configuration panel -->
    <div class="tabs-container">
        <!-- Sidebar Navigation tabs -->
        <aside class="tabs-sidebar" aria-label="Configuration categories">
            <ul class="tab-nav">
                <li class="tab-nav-item active" onclick="switchTab(event, 'general-panel')">General Settings</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'catalog-panel')">Catalog Options</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'sales-panel')">Sales Shipping</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'customers-panel')">Customers Options</li>
            </ul>
        </aside>

        <!-- Right Panel inputs -->
        <div class="tabs-content">
            <!-- 1. General Panel -->
            <div id="general-panel" class="tab-panel active">
                <h2 class="form-section-title">General Store Information</h2>
                
                <div class="form-group">
                    <label for="general__store_information__name">Store Name</label>
                    <div class="form-control-wrapper">
                        <input type="text" id="general__store_information__name" name="general__store_information__name" value="<?= esc($config['general/store_information/name'] ?? '') ?>" class="form-control" placeholder="Store Name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="general__store_information__phone">Store Contact Phone</label>
                    <div class="form-control-wrapper">
                        <input type="text" id="general__store_information__phone" name="general__store_information__phone" value="<?= esc($config['general/store_information/phone'] ?? '') ?>" class="form-control" placeholder="+1 (555) 000-0000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="trans_email__ident_general__email">General Contact Email</label>
                    <div class="form-control-wrapper">
                        <input type="email" id="trans_email__ident_general__email" name="trans_email__ident_general__email" value="<?= esc($config['trans_email/ident_general/email'] ?? '') ?>" class="form-control" placeholder="store@example.com">
                    </div>
                </div>
            </div>

            <!-- 2. Catalog Panel -->
            <div id="catalog-panel" class="tab-panel">
                <h2 class="form-section-title">Catalog Options & Thresholds</h2>

                <div class="form-group">
                    <label for="catalog__frontend__grid_per_page">Products per Page on Grid</label>
                    <div class="form-control-wrapper">
                        <input type="number" id="catalog__frontend__grid_per_page" name="catalog__frontend__grid_per_page" value="<?= esc($config['catalog/frontend/grid_per_page'] ?? '20') ?>" class="form-control" style="width: 150px;">
                        <span class="form-note">Limit of products displayed in dashboard grids.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catalog__inventory__out_of_stock_threshold">Out of Stock Threshold</label>
                    <div class="form-control-wrapper">
                        <input type="number" id="catalog__inventory__out_of_stock_threshold" name="catalog__inventory__out_of_stock_threshold" value="<?= esc($config['catalog/inventory/out_of_stock_threshold'] ?? '0') ?>" class="form-control" style="width: 150px;">
                        <span class="form-note">Restock warnings when quantity drops below.</span>
                    </div>
                </div>
            </div>

            <!-- 3. Sales Panel -->
            <div id="sales-panel" class="tab-panel">
                <h2 class="form-section-title">Sales & Flat Rate Shipping</h2>

                <div class="form-group">
                    <label for="sales__shipping__flat_rate_active">Enable Flat Rate Shipping</label>
                    <div class="form-control-wrapper">
                        <select id="sales__shipping__flat_rate_active" name="sales__shipping__flat_rate_active" class="form-control" style="width: 150px;">
                            <option value="1" <?= ($config['sales/shipping/flat_rate_active'] ?? '1') === '1' ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= ($config['sales/shipping/flat_rate_active'] ?? '1') === '0' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sales__shipping__flat_rate_price">Flat Rate Shipping Price ($)</label>
                    <div class="form-control-wrapper">
                        <input type="number" step="0.01" id="sales__shipping__flat_rate_price" name="sales__shipping__flat_rate_price" value="<?= esc($config['sales/shipping/flat_rate_price'] ?? '10.00') ?>" class="form-control" style="width: 150px;">
                    </div>
                </div>
            </div>

            <!-- 4. Customers Panel -->
            <div id="customers-panel" class="tab-panel">
                <h2 class="form-section-title">Customer Configurations</h2>

                <div class="form-group">
                    <label for="customers__create_account__default_group">Default Customer Group</label>
                    <div class="form-control-wrapper">
                        <select id="customers__create_account__default_group" name="customers__create_account__default_group" class="form-control" style="width: 250px;">
                            <option value="1" <?= ($config['customers/create_account/default_group'] ?? '1') === '1' ? 'selected' : '' ?>>General</option>
                            <option value="2" <?= ($config['customers/create_account/default_group'] ?? '1') === '2' ? 'selected' : '' ?>>Wholesale</option>
                            <option value="3" <?= ($config['customers/create_account/default_group'] ?? '1') === '3' ? 'selected' : '' ?>>VIP</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Configuration tabs controller
    function switchTab(event, panelId) {
        const navItems = document.querySelectorAll('.tab-nav-item');
        navItems.forEach(item => item.classList.remove('active'));
        event.currentTarget.classList.add('active');

        const panels = document.querySelectorAll('.tab-panel');
        panels.forEach(p => p.classList.remove('active'));
        document.getElementById(panelId).classList.add('active');
    }
</script>
<?= $this->endSection() ?>
