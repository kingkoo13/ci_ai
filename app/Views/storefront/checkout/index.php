<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>Checkout<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title">Checkout</h1>
</div>

<form action="<?= base_url('checkout') ?>" method="POST">
    <?= csrf_field() ?>
    <div class="checkout-container">
        
        <!-- Left Side Forms -->
        <div>
            <!-- 1. Shipping Address -->
            <div class="checkout-step">
                <h2 class="checkout-step-title">1. Shipping Address</h2>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div>
                        <label for="first_name" style="display:block; font-weight:600; margin-bottom:5px;">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name') ?: ($customer->first_name ?? '')) ?>" class="search-input" style="width:100%;" required>
                    </div>
                    <div>
                        <label for="last_name" style="display:block; font-weight:600; margin-bottom:5px;">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name') ?: ($customer->last_name ?? '')) ?>" class="search-input" style="width:100%;" required>
                    </div>
                </div>

                <div style="margin-bottom:15px;">
                    <label for="email" style="display:block; font-weight:600; margin-bottom:5px;">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?= esc(old('email') ?: ($customer->email ?? '')) ?>" class="search-input" style="width:100%;" required>
                </div>

                <div style="margin-bottom:15px;">
                    <label for="street" style="display:block; font-weight:600; margin-bottom:5px;">Street Address *</label>
                    <input type="text" id="street" name="street" value="<?= esc(old('street') ?: ($defaultAddress->street ?? '')) ?>" class="search-input" style="width:100%;" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div>
                        <label for="city" style="display:block; font-weight:600; margin-bottom:5px;">City *</label>
                        <input type="text" id="city" name="city" value="<?= esc(old('city') ?: ($defaultAddress->city ?? '')) ?>" class="search-input" style="width:100%;" required>
                    </div>
                    <div>
                        <label for="region" style="display:block; font-weight:600; margin-bottom:5px;">State/Province</label>
                        <input type="text" id="region" name="region" value="<?= esc(old('region') ?: ($defaultAddress->region ?? '')) ?>" class="search-input" style="width:100%;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div>
                        <label for="postcode" style="display:block; font-weight:600; margin-bottom:5px;">Zip/Postal Code *</label>
                        <input type="text" id="postcode" name="postcode" value="<?= esc(old('postcode') ?: ($defaultAddress->postcode ?? '')) ?>" class="search-input" style="width:100%;" required>
                    </div>
                    <div>
                        <label for="country" style="display:block; font-weight:600; margin-bottom:5px;">Country *</label>
                        <input type="text" id="country" name="country" value="<?= esc(old('country') ?: ($defaultAddress->country ?? 'United States')) ?>" class="search-input" style="width:100%;" required>
                    </div>
                </div>

                <div style="margin-bottom:15px;">
                    <label for="telephone" style="display:block; font-weight:600; margin-bottom:5px;">Phone Number *</label>
                    <input type="text" id="telephone" name="telephone" value="<?= esc(old('telephone') ?: ($defaultAddress->telephone ?? '')) ?>" class="search-input" style="width:100%;" required>
                </div>
            </div>

            <!-- 2. Shipping Method -->
            <div class="checkout-step">
                <h2 class="checkout-step-title">2. Shipping Method</h2>
                <div style="display:flex; align-items:center; gap:10px; background:var(--color-bg-light); padding:12px; border-radius:var(--border-radius);">
                    <input type="radio" id="flatrate" name="shipping_method" value="flatrate" checked required>
                    <label for="flatrate" style="font-weight:600;">Flat Rate - Fixed ($10.00)</label>
                </div>
            </div>

            <!-- 3. Payment Method & EAV custom Order details -->
            <div class="checkout-step">
                <h2 class="checkout-step-title">3. Payment & Additional Details</h2>
                
                <div style="margin-bottom:25px;">
                    <p style="font-weight:700; margin-bottom:10px;">Select Payment Method:</p>
                    <div style="display:flex; align-items:center; gap:10px; background:var(--color-bg-light); padding:12px; border-radius:var(--border-radius); border: 1px solid var(--color-border-light);">
                        <input type="radio" id="checkmo" name="payment_method" value="checkmo" checked required>
                        <label for="checkmo" style="font-weight:600;"><i class="fa-solid fa-money-check-dollar"></i> Check / Money Order</label>
                    </div>
                </div>

                <!-- Custom EAV Order attributes form fields -->
                <?php if (!empty($orderAttributes)) : ?>
                    <div style="background-color:#fafafa; border:1px solid var(--color-border-light); padding:15px; border-radius:var(--border-radius);">
                        <h3 style="font-size:14px; font-weight:700; margin-bottom:15px; border-bottom:1px solid var(--color-border-light); padding-bottom:5px; text-transform:uppercase;">Order Options</h3>
                        
                        <?php foreach ($orderAttributes as $attr) : ?>
                            <?php 
                            $required = $attr->is_required ? 'required' : '';
                            $reqStar = $attr->is_required ? ' <span style="color:var(--color-danger)">*</span>' : '';
                            ?>
                            <div style="margin-bottom:15px;">
                                <label for="checkout-attr-<?= $attr->id ?>" style="display:block; font-weight:600; margin-bottom:5px; font-size:13px;"><?= esc($attr->frontend_label) ?><?= $reqStar ?></label>
                                
                                <?php if ($attr->input_type === 'text') : ?>
                                    <input type="text" id="checkout-attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" value="<?= esc(old('attributes.' . $attr->id) ?? '') ?>" class="search-input" style="width:100%;" <?= $required ?>>
                                <?php elseif ($attr->input_type === 'textarea') : ?>
                                    <textarea id="checkout-attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="search-input" style="width:100%; height:60px;" <?= $required ?>><?= esc(old('attributes.' . $attr->id) ?? '') ?></textarea>
                                <?php elseif ($attr->input_type === 'boolean') : ?>
                                    <select id="checkout-attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="search-input" style="width:150px;" <?= $required ?>>
                                        <option value="0" <?= old('attributes.' . $attr->id) === '0' ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= old('attributes.' . $attr->id) === '1' ? 'selected' : '' ?>>Yes</option>
                                    </select>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Side Order Summary box -->
        <div class="checkout-summary-box">
            <div class="checkout-summary-title">Order Summary</div>
            
            <!-- Items scroll list -->
            <div style="max-height: 250px; overflow-y:auto; margin-bottom:20px; display:flex; flex-direction:column; gap:10px; border-bottom:1px solid var(--color-border-light); padding-bottom:15px;">
                <?php foreach ($cart as $item) : ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px;">
                        <div>
                            <span style="font-weight:600; color:var(--color-text);"><?= esc($item['name']) ?></span>
                            <div style="font-size:11px; color:var(--color-text-muted);">Qty: <?= (int)$item['qty'] ?></div>
                        </div>
                        <span style="font-weight:600; color:var(--color-text);">$<?= number_format($item['price'] * $item['qty'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Totals block -->
            <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:13px; color:var(--color-text-light);">
                <span>Cart Subtotal:</span>
                <span>$<?= number_format($subtotal, 2) ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:13px; color:var(--color-text-light);">
                <span>Shipping (Flat Rate):</span>
                <span>$<?= number_format($shipping, 2) ?></span>
            </div>
            <hr style="border:0; border-top:1px solid var(--color-border-light); margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; font-weight:700; font-size:16px; margin-bottom:25px;">
                <span>Order Total:</span>
                <span>$<?= number_format($grandTotal, 2) ?></span>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:12px 0;">Place Order</button>
        </div>

    </div>
</form>
<?= $this->endSection() ?>
