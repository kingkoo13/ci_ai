<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?><?= esc($product->name) ?><?= $this->endSection() ?>

<!-- Right Sidebar: Related Products (if 2columns-right or 3columns) -->
<?= $this->section('sidebar_right') ?>
<div class="sidebar-title">Related Products</div>
<div style="display:flex; flex-direction:column; gap:20px;">
    <?php if (empty($relatedProducts)) : ?>
        <p style="color:var(--color-text-muted); font-style:italic;">No related products found.</p>
    <?php else : ?>
        <?php foreach ($relatedProducts as $rel) : ?>
            <div style="border: 1px solid var(--color-border-light); padding:10px; border-radius:var(--border-radius); background:white;">
                <a href="<?= base_url('product/' . $rel->id) ?>">
                    <img src="<?= base_url($rel->image_url) ?>" alt="<?= esc($rel->name) ?>" style="width:100%; height:100px; object-fit:contain; margin-bottom:5px;">
                    <h4 style="font-size:12px; font-weight:600; color:var(--color-text); height:32px; overflow:hidden;"><?= esc($rel->name) ?></h4>
                </a>
                <div style="font-weight:700; margin-top:5px;">
                    $<?= number_format($rel->special_price ?: $rel->price, 2) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<!-- Main PDP Content -->
<?= $this->section('content') ?>
<div class="product-detail-container">
    <!-- Media gallery -->
    <div class="product-media-gallery">
        <img src="<?= base_url($product->image_url) ?>" alt="<?= esc($product->name) ?>">
    </div>

    <!-- Info Buy Block -->
    <div class="product-info-block">
        <h1 class="page-title" style="font-size: 28px; line-height: 1.2; font-weight:300;"><?= esc($product->name) ?></h1>
        <div class="pdp-sku">SKU: <code><?= esc($product->sku) ?></code></div>
        
        <hr style="border:0; border-top:1px solid var(--color-border-light);">

        <!-- Stock & Status info -->
        <div>
            Status: 
            <?php if ($product->is_in_stock && $product->qty > 0) : ?>
                <span class="status-badge status-complete" style="font-size:11px;">In Stock</span>
            <?php else : ?>
                <span class="status-badge status-disabled" style="font-size:11px;">Out of Stock</span>
            <?php endif; ?>
        </div>

        <!-- Prices -->
        <div class="pdp-price">
            <?php if ($product->special_price) : ?>
                <span class="special-price">$<?= number_format($product->special_price, 2) ?></span>
                <span class="old-price">$<?= number_format($product->price, 2) ?></span>
            <?php else : ?>
                <span>$<?= number_format($product->price, 2) ?></span>
            <?php endif; ?>
        </div>

        <!-- Add to cart form -->
        <?php if ($product->is_in_stock && $product->qty > 0) : ?>
            <form action="<?= base_url('cart/add') ?>" method="POST" style="margin-top: 15px;">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                
                <div class="pdp-qty-wrapper">
                    <label for="qty">Qty:</label>
                    <input type="number" id="qty" name="qty" value="1" min="1" max="<?= (int)$product->qty ?>" class="qty-input" required>
                </div>

                <button type="submit" class="btn btn-primary" style="padding:12px 30px; font-size:14px;"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
            </form>
        <?php else : ?>
            <p style="color:var(--color-danger); font-weight:700; margin-top:15px;">This item is currently unavailable for purchase.</p>
        <?php endif; ?>
    </div>
</div>

<!-- PDP Specs and Description Tabs sheet -->
<div class="pdp-tabs">
    <ul class="tab-list">
        <li class="tab-item active" onclick="switchPdpTab(event, 'details-tab')">Details</li>
        <?php if (!empty($specs)) : ?>
            <li class="tab-item" onclick="switchPdpTab(event, 'specs-tab')">More Information</li>
        <?php endif; ?>
    </ul>

    <div class="tab-content-container">
        <!-- 1. Description Tab -->
        <div id="details-tab" class="tab-content-panel active" style="line-height:1.7; color:var(--color-text-light);">
            <?= $product->description ? $product->description : '<p>No description provided.</p>' ?>
        </div>

        <!-- 2. Custom EAV Specifications Tab -->
        <?php if (!empty($specs)) : ?>
            <div id="specs-tab" class="tab-content-panel">
                <table class="standard-table" style="max-width: 600px; border:none;">
                    <tbody>
                        <?php foreach ($specs as $spec) : ?>
                            <tr>
                                <th style="width: 200px; font-weight:700; text-align:left; border:none; border-bottom:1px solid var(--color-border-light); background:none; padding:10px 0;"><?= esc($spec->frontend_label) ?></th>
                                <td style="border:none; border-bottom:1px solid var(--color-border-light); padding:10px 0; color:var(--color-text-light);">
                                    <?php if ($spec->input_type === 'boolean') : ?>
                                        <?= ($spec->value === '1') ? 'Yes' : 'No' ?>
                                    <?php else : ?>
                                        <?= esc($spec->value) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Local PDP tab switcher script
    function switchPdpTab(event, tabId) {
        // Toggle tab highlights
        const items = document.querySelectorAll('.tab-item');
        items.forEach(i => i.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Toggle content sections
        const panels = document.querySelectorAll('.tab-content-panel');
        panels.forEach(p => p.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
    }
</script>
<?= $this->endSection() ?>
