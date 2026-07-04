<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Products Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Catalog <span>/</span> Products
        </div>
        <h1 class="page-title">Products</h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/catalog/products/new') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Product</a>
    </div>
</div>

<!-- Search & Filters Container -->
<form id="filter-form" action="<?= base_url('admin/catalog/products') ?>" method="GET">
    <div class="grid-filters">
        <div class="filter-group">
            <label for="filter_id">ID</label>
            <input type="text" id="filter_id" name="filter_id" value="<?= esc($filter_id ?? '') ?>" class="filter-input" style="width: 70px;">
        </div>
        <div class="filter-group">
            <label for="filter_sku">SKU</label>
            <input type="text" id="filter_sku" name="filter_sku" value="<?= esc($filter_sku ?? '') ?>" class="filter-input" style="width: 120px;">
        </div>
        <div class="filter-group">
            <label for="filter_name">Name</label>
            <input type="text" id="filter_name" name="filter_name" value="<?= esc($filter_name ?? '') ?>" class="filter-input" style="width: 180px;">
        </div>
        <div class="filter-group">
            <label>Price ($)</label>
            <div style="display:flex; gap: 5px;">
                <input type="number" step="0.01" name="filter_price_min" value="<?= esc($filter_price_min ?? '') ?>" placeholder="Min" class="filter-input" style="width: 80px;">
                <input type="number" step="0.01" name="filter_price_max" value="<?= esc($filter_price_max ?? '') ?>" placeholder="Max" class="filter-input" style="width: 80px;">
            </div>
        </div>
        <div class="filter-group">
            <label>Qty</label>
            <div style="display:flex; gap: 5px;">
                <input type="number" name="filter_qty_min" value="<?= esc($filter_qty_min ?? '') ?>" placeholder="Min" class="filter-input" style="width: 70px;">
                <input type="number" name="filter_qty_max" value="<?= esc($filter_qty_max ?? '') ?>" placeholder="Max" class="filter-input" style="width: 70px;">
            </div>
        </div>
        <div class="filter-group">
            <label for="filter_status">Status</label>
            <select id="filter_status" name="filter_status" class="filter-input" style="width: 110px;">
                <option value="">Any</option>
                <option value="1" <?= ($filter_status === '1') ? 'selected' : '' ?>>Enabled</option>
                <option value="0" <?= ($filter_status === '0') ? 'selected' : '' ?>>Disabled</option>
            </select>
        </div>
        <div class="filter-group" style="display: flex; flex-direction: row; gap: 10px; align-self: flex-end;">
            <button type="submit" class="btn btn-secondary">Search</button>
            <a href="<?= base_url('admin/catalog/products') ?>" class="btn">Clear</a>
        </div>
    </div>
</form>

<!-- Mass Actions & Info bar -->
<form id="massaction-form" action="<?= base_url('admin/catalog/products/massStatus') ?>" method="POST">
    <?= csrf_field() ?>
    <div class="grid-actions-bar">
        <div class="massaction-wrapper">
            <select name="status" class="filter-input" required style="width: 140px;" aria-label="Choose action to run on selected products">
                <option value="">Actions</option>
                <option value="1">Change Status: Enable</option>
                <option value="0">Change Status: Disable</option>
            </select>
            <button type="submit" class="btn" style="padding: 5px 12px;">Submit</button>
        </div>
        <div>
            Total <strong><?= $totalCount ?></strong> records found
        </div>
    </div>

    <!-- Grid Table -->
    <div class="grid-table-container">
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">
                        <input type="checkbox" id="check-all" aria-label="Select all products on page">
                    </th>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 80px;">Image</th>
                    <th style="width: 120px;">SKU</th>
                    <th>Name</th>
                    <th style="width: 120px;">Price</th>
                    <th style="width: 100px;">Qty</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 100px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)) : ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No records found.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="product_ids[]" value="<?= $product->id ?>" class="product-checkbox" aria-label="Select product <?= esc($product->name) ?>">
                            </td>
                            <td><?= $product->id ?></td>
                            <td>
                                <img src="<?= base_url($product->image_url) ?>" alt="<?= esc($product->name) ?>" style="width: 40px; height: 40px; object-fit: contain; border: 1px solid var(--color-border-light); background: #fbfbfb;">
                            </td>
                            <td><strong><?= esc($product->sku) ?></strong></td>
                            <td><?= esc($product->name) ?></td>
                            <td>
                                <?php if ($product->special_price) : ?>
                                    <span style="text-decoration: line-through; color: var(--color-text-muted); font-size: 11px;">$<?= number_format($product->price, 2) ?></span><br>
                                    <span style="color: var(--color-danger); font-weight: 700;">$<?= number_format($product->special_price, 2) ?></span>
                                <?php else : ?>
                                    <strong>$<?= number_format($product->price, 2) ?></strong>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($product->qty) ?></td>
                            <td>
                                <span class="status-badge status-<?= ($product->status) ? 'enabled' : 'disabled' ?>">
                                    <?= ($product->status) ? 'Enabled' : 'Disabled' ?>
                                </span>
                            </td>
                            <td style="text-align: center; display: flex; justify-content: center; gap: 10px;">
                                <a href="<?= base_url('admin/catalog/products/edit/' . $product->id) ?>" title="Edit" style="color: var(--color-text-light);"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button type="button" onclick="confirmDelete(<?= $product->id ?>, '<?= esc($product->name) ?>')" title="Delete" style="border:none; background:none; color: var(--color-danger); cursor:pointer;"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<!-- Pagination -->
<?php if ($totalPages > 1) : ?>
    <div class="pagination-container">
        <div>
            Page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong> pages
        </div>
        <div class="pagination-links">
            <?php if ($page > 1) : ?>
                <a href="?page=1<?= $q ? '&q='.$q : '' ?>" class="pagination-link">&laquo;</a>
                <a href="?page=<?= $page - 1 ?><?= $q ? '&q='.$q : '' ?>" class="pagination-link">&lsaquo;</a>
            <?php endif; ?>

            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) : ?>
                <a href="?page=<?= $i ?><?= $q ? '&q='.$q : '' ?>" class="pagination-link <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages) : ?>
                <a href="?page=<?= $page + 1 ?><?= $q ? '&q='.$q : '' ?>" class="pagination-link">&rsaquo;</a>
                <a href="?page=<?= $totalPages ?><?= $q ? '&q='.$q : '' ?>" class="pagination-link">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Delete Form Submission -->
<form id="delete-form" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
    // Handle checking/unchecking all products
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
                cb.closest('tr').classList.toggle('selected', checkAll.checked);
            });
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            cb.closest('tr').classList.toggle('selected', cb.checked);
        });
    });

    // JavaScript delete confirmation
    function confirmDelete(id, name) {
        if (confirm("Are you sure you want to delete product '" + name + "'?")) {
            const deleteForm = document.getElementById('delete-form');
            deleteForm.action = "<?= base_url('admin/catalog/products/delete/') ?>" + id;
            deleteForm.submit();
        }
    }
</script>
<?= $this->endSection() ?>
