<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>Shopping Cart<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title">Shopping Cart</h1>
</div>

<?php if (empty($cart)) : ?>
    <div style="background-color: var(--color-bg-light); border-radius: var(--border-radius); padding: 50px; text-align:center; color: var(--color-text-muted);">
        <i class="fa-solid fa-cart-shopping" style="font-size: 48px; margin-bottom:15px; color:var(--color-border);"></i>
        <h2>Your Shopping Cart is Empty</h2>
        <p style="margin-top:10px;">You have no items in your shopping cart.</p>
        <a href="<?= base_url('/') ?>" class="btn btn-primary" style="margin-top:20px;">Continue Shopping</a>
    </div>
<?php else : ?>
    <form action="<?= base_url('cart/update') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="checkout-container" style="grid-template-columns: 1fr 320px;">
            
            <!-- Items Table list -->
            <div class="column">
                <table class="standard-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th colspan="2">Item</th>
                            <th style="text-align:right;">Price</th>
                            <th style="text-align:center; width:100px;">Qty</th>
                            <th style="text-align:right; width:120px;">Subtotal</th>
                            <th style="text-align:center; width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item) : ?>
                            <tr>
                                <td style="width: 80px; text-align:center; padding: 10px;">
                                    <img src="<?= base_url($item['image_url']) ?>" alt="<?= esc($item['name']) ?>" style="width:60px; height:60px; object-fit:contain; background-color:#fcfcfd;">
                                </td>
                                <td>
                                    <a href="<?= base_url('product/' . $item['id']) ?>" style="font-weight:600; color:var(--color-text);"><?= esc($item['name']) ?></a>
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:3px;">SKU: <?= esc($item['sku']) ?></div>
                                </td>
                                <td style="text-align:right; font-weight:600;">$<?= number_format($item['price'], 2) ?></td>
                                <td style="text-align:center;">
                                    <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= (int)$item['qty'] ?>" min="1" class="qty-input" style="width: 60px; padding: 5px;">
                                </td>
                                <td style="text-align:right; font-weight:700;">$<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                <td style="text-align:center;">
                                    <a href="<?= base_url('cart/remove/' . $item['id']) ?>" style="color:var(--color-danger);" title="Remove"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display:flex; justify-content:space-between; margin-top:20px;">
                    <a href="<?= base_url('/') ?>" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Update Shopping Cart</button>
                </div>
            </div>

            <!-- Cart totals summary card -->
            <div class="checkout-summary-box">
                <div class="checkout-summary-title">Summary</div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--color-text-light);">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--color-text-light);">
                    <span>Shipping (Flat Rate):</span>
                    <span>$10.00</span>
                </div>
                <hr style="border:0; border-top:1px solid var(--color-border-light); margin-bottom:15px;">
                <div style="display:flex; justify-content:space-between; font-weight:700; font-size:16px; margin-bottom:20px;">
                    <span>Order Total:</span>
                    <span>$<?= number_format($subtotal + 10.00, 2) ?></span>
                </div>
                
                <a href="<?= base_url('checkout') ?>" class="btn btn-primary" style="display:block; width:100%; text-align:center; padding:12px 0;">Proceed to Checkout</a>
            </div>

        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>
