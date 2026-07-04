<?php if (empty($cart)) : ?>
    <p style="color:var(--color-text-muted); font-style:italic; text-align:center; padding: 20px 0;">Your cart is empty.</p>
<?php else : ?>
    <div class="minicart-items">
        <?php foreach ($cart as $item) : ?>
            <div class="minicart-item">
                <img src="<?= base_url($item['image_url']) ?>" alt="<?= esc($item['name']) ?>" class="minicart-item-img">
                <div class="minicart-item-details">
                    <div class="minicart-item-name">
                        <a href="<?= base_url('product/' . $item['id']) ?>"><?= esc($item['name']) ?></a>
                    </div>
                    <div class="minicart-item-price">
                        <?= (int)$item['qty'] ?> x <strong>$<?= number_format($item['price'], 2) ?></strong>
                    </div>
                </div>
                <a href="<?= base_url('cart/remove/' . $item['id']) ?>" style="color:var(--color-text-muted);" title="Remove"><i class="fa-solid fa-trash-can"></i></a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="minicart-totals">
        <span>Cart Subtotal:</span>
        <span>$<?= number_format($subtotal, 2) ?></span>
    </div>

    <div class="minicart-actions">
        <a href="<?= base_url('cart') ?>" class="btn btn-secondary" style="flex:1; font-size:11px; padding:6px 0;">View Cart</a>
        <a href="<?= base_url('checkout') ?>" class="btn btn-primary" style="flex:1; font-size:11px; padding:6px 0;">Checkout</a>
    </div>
<?php endif; ?>
