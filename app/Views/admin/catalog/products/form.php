<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?><?= ($isEdit) ? 'Edit Product: ' . esc($product->name) : 'New Product' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST" id="product-form">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Catalog <span>/</span> Products <span>/</span> <?= ($isEdit) ? 'Edit' : 'New' ?>
            </div>
            <h1 class="page-title"><?= ($isEdit) ? esc($product->name) : 'New Product' ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/catalog/products') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Product</button>
        </div>
    </div>

    <!-- Tabbed container -->
    <div class="tabs-container">
        <!-- Tab navigation sidebar -->
        <aside class="tabs-sidebar" aria-label="Product form sections">
            <ul class="tab-nav">
                <li class="tab-nav-item active" onclick="switchTab(event, 'basic-tab')">Product Details</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'attributes-tab')">Custom Attributes</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'content-tab')">Content</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'categories-tab')">Categories</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'images-tab')">Images</li>
            </ul>
        </aside>

        <!-- Tab panels -->
        <div class="tabs-content">
            <!-- 1. Basic Details Tab -->
            <div id="basic-tab" class="tab-panel active">
                <h2 class="form-section-title">Product Details</h2>
                
                <div class="form-group">
                    <label for="status">Enable Product</label>
                    <div class="form-control-wrapper">
                        <select id="status" name="status" class="form-control" style="width: 150px;">
                            <option value="1" <?= (!$isEdit || $product->status == 1) ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= ($isEdit && $product->status == 0) ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="attribute_set_id">Attribute Set <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <select id="attribute_set_id" name="attribute_set_id" class="form-control" style="width: 250px;" onchange="loadCustomAttributes(this.value)">
                            <?php foreach ($sets as $set) : ?>
                                <option value="<?= $set->id ?>" <?= (($isEdit && $product->attribute_set_id == $set->id) || (!$isEdit && $set->id == 1)) ? 'selected' : '' ?>>
                                    <?= esc($set->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-note">Determines which custom attributes load in the attributes tab.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sku">SKU <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="text" id="sku" name="sku" value="<?= esc(old('sku', $product->sku ?? '')) ?>" class="form-control" required placeholder="e.g. running-shoes-01">
                        <span class="form-note">Must be unique across catalog.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Product Name <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="text" id="name" name="name" value="<?= esc(old('name', $product->name ?? '')) ?>" class="form-control" required placeholder="e.g. Sleek Athletic Running Shoes">
                    </div>
                </div>

                <div class="form-group">
                    <label for="price">Price ($) <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="number" step="0.01" id="price" name="price" value="<?= esc(old('price', $product->price ?? '')) ?>" class="form-control" required placeholder="0.00" style="width: 200px;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="special_price">Special Price ($)</label>
                    <div class="form-control-wrapper">
                        <input type="number" step="0.01" id="special_price" name="special_price" value="<?= esc(old('special_price', $product->special_price ?? '')) ?>" class="form-control" placeholder="0.00" style="width: 200px;">
                        <span class="form-note">Leave empty to use regular price.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="qty">Quantity <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="number" id="qty" name="qty" value="<?= esc(old('qty', $product->qty ?? '0')) ?>" class="form-control" required placeholder="0" style="width: 200px;">
                    </div>
                </div>
            </div>

            <!-- 2. Custom Attributes Tab -->
            <div id="attributes-tab" class="tab-panel">
                <h2 class="form-section-title">Custom Attributes</h2>
                <div id="attributes-loader-container">
                    <p style="color: var(--color-text-muted); font-style: italic;">Loading custom attributes...</p>
                </div>
            </div>

            <!-- 3. Content Tab -->
            <div id="content-tab" class="tab-panel">
                <h2 class="form-section-title">Content</h2>
                
                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <div class="form-control-wrapper">
                        <textarea id="short_description" name="short_description" rows="3" class="form-control" placeholder="Brief visual overview..."><?= esc(old('short_description', $product->short_description ?? '')) ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Full Description</label>
                    <div class="form-control-wrapper">
                        <textarea id="description" name="description" rows="8" class="form-control" placeholder="Detailed product attributes and specifications..."><?= esc(old('description', $product->description ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 4. Categories Tab -->
            <div id="categories-tab" class="tab-panel">
                <h2 class="form-section-title">Categories</h2>
                <p style="margin-bottom: 20px; color: var(--color-text-light);">Select the categories this product belongs to:</p>
                
                <div style="display: flex; flex-direction: column; gap: 10px; max-height: 350px; overflow-y: auto; padding: 10px; border: 1px solid var(--color-border); border-radius: var(--border-radius);">
                    <?php if (empty($categories)) : ?>
                        <p style="color: var(--color-text-muted);">No categories found. Create one in Catalog -> Categories first.</p>
                    <?php else : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="categories[]" value="<?= $cat->id ?>" id="cat-<?= $cat->id ?>" <?= (in_array($cat->id, $mappedCats)) ? 'checked' : '' ?>>
                                <label for="cat-<?= $cat->id ?>" style="font-weight: normal; cursor: pointer; padding: 0;"><?= esc($cat->name) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 5. Images Tab -->
            <div id="images-tab" class="tab-panel">
                <h2 class="form-section-title">Images</h2>
                
                <div class="form-group">
                    <label for="image_url">Image Relative Path</label>
                    <div class="form-control-wrapper">
                        <input type="text" id="image_url" name="image_url" value="<?= esc(old('image_url', $product->image_url ?? 'assets/images/placeholder.jpg')) ?>" class="form-control" onchange="previewImage(this.value)">
                        <span class="form-note">Use paths like <code>assets/images/shoes-01.jpg</code> or external URLs.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Image Preview</label>
                    <div class="form-control-wrapper">
                        <img id="product-img-preview" src="<?= base_url(old('image_url', $product->image_url ?? 'assets/images/placeholder.jpg')) ?>" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: contain; border: 1px solid var(--color-border); background-color: #fafafa; padding: 5px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Handles changing tabs
    function switchTab(event, tabId) {
        // Deactivate all nav items
        const navItems = document.querySelectorAll('.tab-nav-item');
        navItems.forEach(item => item.classList.remove('active'));
        
        // Activate current nav item
        event.currentTarget.classList.add('active');
        
        // Hide all tab panels
        const panels = document.querySelectorAll('.tab-panel');
        panels.forEach(panel => panel.classList.remove('active'));
        
        // Show current panel
        document.getElementById(tabId).classList.add('active');
    }

    // Handles dynamically changing image preview
    function previewImage(val) {
        const preview = document.getElementById('product-img-preview');
        if (val.startsWith('http') || val.startsWith('https')) {
            preview.src = val;
        } else {
            preview.src = "<?= base_url() ?>" + val;
        }
    }

    // Load custom attributes dynamically
    function loadCustomAttributes(setId) {
        const container = document.getElementById('attributes-loader-container');
        container.innerHTML = '<p style="color: var(--color-text-muted); font-style: italic;">Loading custom attributes...</p>';
        
        const productId = <?= $isEdit ? $product->id : 0 ?>;
        
        fetch('<?= base_url('admin/catalog/products/getAttributes') ?>?set_id=' + setId + '&product_id=' + productId)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(err => {
                container.innerHTML = '<p style="color: var(--color-danger);">Failed to load custom attributes.</p>';
            });
    }

    // Trigger initial load on page load
    document.addEventListener("DOMContentLoaded", function() {
        const setId = document.getElementById('attribute_set_id').value;
        loadCustomAttributes(setId);
    });
</script>
<?= $this->endSection() ?>
