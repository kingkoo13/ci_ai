<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?><?= esc($category->name) ?><?= $this->endSection() ?>

<!-- Left Sidebar Layered Navigation Filters -->
<?= $this->section('sidebar_left') ?>
<div class="sidebar-title">Shop By</div>

<!-- Active Filters list -->
<?php if (!empty($activeFilters)) : ?>
    <div style="background:var(--color-bg-light); padding:10px; border-radius:var(--border-radius); margin-bottom:20px; font-size:13px;">
        <p style="font-weight:700; margin-bottom:8px;">Active Filters:</p>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:5px;">
            <?php foreach ($activeFilters as $code => $val) : ?>
                <li style="display:flex; justify-content:space-between; align-items:center;">
                    <span><?= esc(ucfirst($code)) ?>: <strong><?= esc($val) ?></strong></span>
                    <?php 
                    // Generate url without this query param
                    $currentParams = $_GET;
                    unset($currentParams[$code]);
                    $queryString = !empty($currentParams) ? '?' . http_build_query($currentParams) : '';
                    ?>
                    <a href="<?= base_url('category/' . $category->id . $queryString) ?>" style="color:var(--color-danger); font-size:10px;"><i class="fa-solid fa-circle-xmark"></i></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="<?= base_url('category/' . $category->id) ?>" style="display:block; text-align:center; font-size:11px; margin-top:10px; font-weight:700;">Clear All</a>
    </div>
<?php endif; ?>

<!-- Filter Options -->
<div class="filter-options">
    <?php foreach ($sidebarFilters as $filter) : ?>
        <!-- Skip loading this filter if it is already active in search -->
        <?php if (isset($activeFilters[$filter['code']])) continue; ?>
        
        <div>
            <div class="filter-title"><?= esc($filter['label']) ?></div>
            <ul class="filter-links">
                <?php foreach ($filter['options'] as $option) : ?>
                    <?php 
                    $params = $_GET;
                    $params[$filter['code']] = $option['label'];
                    $linkUrl = base_url('category/' . $category->id . '?' . http_build_query($params));
                    ?>
                    <li>
                        <a href="<?= $linkUrl ?>">
                            <?= esc($option['label']) ?>
                            <span class="filter-count">(<?= $option['count'] ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>

<!-- Main Category Page Grid content -->
<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title"><?= esc($category->name) ?></h1>
</div>

<!-- Banner Image -->
<?php if (!empty($bannerImg)) : ?>
    <div style="margin-bottom: 25px; border-radius: var(--border-radius); overflow:hidden; border: 1px solid var(--color-border-light);">
        <img src="<?= base_url($bannerImg) ?>" alt="<?= esc($category->name) ?> Banner" style="width: 100%; height: 200px; object-fit: cover;">
    </div>
<?php endif; ?>

<?php if (!empty($category->description)) : ?>
    <p style="margin-bottom:25px; color:var(--color-text-light); font-style:italic; line-height:1.6;"><?= esc($category->description) ?></p>
<?php endif; ?>

<!-- Products Grid list -->
<?php if (empty($products)) : ?>
    <div style="background-color: var(--color-bg-light); border-radius: var(--border-radius); padding: 40px; text-align:center; color: var(--color-text-muted);">
        <i class="fa-solid fa-box-open" style="font-size: 32px; margin-bottom:10px;"></i>
        <p>No products match your selected filters. Try clearing your options.</p>
    </div>
<?php else : ?>
    <div class="product-grid">
        <?php foreach ($products as $prod) : ?>
            <div class="product-item">
                <a href="<?= base_url('product/' . $prod->id) ?>">
                    <img src="<?= base_url($prod->image_url) ?>" alt="<?= esc($prod->name) ?>" class="product-item-img">
                    <h3 class="product-item-name"><?= esc($prod->name) ?></h3>
                </a>
                
                <div>
                    <div class="product-item-price">
                        <?php if ($prod->special_price) : ?>
                            <span class="special-price">$<?= number_format($prod->special_price, 2) ?></span>
                            <span class="old-price">$<?= number_format($prod->price, 2) ?></span>
                        <?php else : ?>
                            <span>$<?= number_format($prod->price, 2) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <form action="<?= base_url('cart/add') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $prod->id ?>">
                        <input type="hidden" name="qty" value="1">
                        <button type="submit" class="btn-add-to-cart"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
