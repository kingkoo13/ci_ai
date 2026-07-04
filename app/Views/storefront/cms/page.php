<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?><?= esc($pageTitle ?? 'Welcome') ?><?= $this->endSection() ?>

<!-- Left Sidebar Default Category navigation links for CMS pages under 2cols-left -->
<?= $this->section('sidebar_left') ?>
<div class="sidebar-title">Categories</div>
<ul style="list-style:none; padding-left:10px; display:flex; flex-direction:column; gap:10px;">
    <?php 
    $db = \Config\Database::connect();
    $cats = $db->table('categories')->where('parent_id', 1)->where('is_active', 1)->get()->getResult();
    foreach ($cats as $c) :
    ?>
        <li><a href="<?= base_url('category/' . $c->id) ?>" style="font-weight:600;"><i class="fa-solid fa-chevron-right" style="font-size:10px; margin-right:5px;"></i> <?= esc($c->name) ?></a></li>
    <?php endforeach; ?>
</ul>
<?= $this->endSection() ?>

<!-- Right Sidebar Default content -->
<?= $this->section('sidebar_right') ?>
<div class="sidebar-title">Today's Deals</div>
<div style="background-color: var(--color-primary); color:white; padding:15px; border-radius: var(--border-radius); text-align:center; font-weight:700;">
    <p>Get Free Shipping on all orders over $100!</p>
</div>
<?= $this->endSection() ?>

<!-- Main CMS Content -->
<?= $this->section('content') ?>
<div class="cms-page-content" style="line-height:1.8;">
    
    <!-- Render CMS content directly -->
    <?= $content ?>

    <!-- If homepage, output a grid of featured products -->
    <?php if (isset($products) && !empty($products)) : ?>
        <div style="margin-top: 50px;">
            <h2 style="font-weight:300; font-size:24px; margin-bottom:20px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px;">Featured Products</h2>
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
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
