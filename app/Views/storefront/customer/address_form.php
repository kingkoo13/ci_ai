<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>Edit Address Book Details<?= $this->endSection() ?>

<!-- Left Sidebar Navigation menu -->
<?= $this->section('sidebar_left') ?>
<div class="sidebar-title">My Account</div>
<ul style="list-style:none; padding-left:10px; display:flex; flex-direction:column; gap:10px; font-size:13px;">
    <li><a href="<?= base_url('customer/account') ?>" style="color:var(--color-text-light);"><i class="fa-regular fa-circle-dot"></i> Account Dashboard</a></li>
    <li><a href="<?= base_url('customer/account') ?>" style="font-weight:700; color:var(--color-primary);"><i class="fa-solid fa-address-book"></i> Address Book</a></li>
    <li><a href="<?= base_url('customer/account') ?>" style="color:var(--color-text-light);"><i class="fa-solid fa-basket-shopping"></i> My Orders</a></li>
</ul>
<?= $this->endSection() ?>

<!-- Main Edit Address Form Content -->
<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title">Edit Address Book Details</h1>
</div>

<div class="checkout-step" style="max-width: 700px; margin: 0;">
    <form action="" method="POST">
        <?= csrf_field() ?>

        <h3 style="font-size:16px; font-weight:700; margin-bottom:15px; border-bottom:1px solid var(--color-border-light); padding-bottom:5px;">Contact & Street Details</h3>
        
        <div style="margin-bottom:15px;">
            <label for="street" style="display:block; font-weight:600; margin-bottom:5px;">Street Address *</label>
            <input type="text" id="street" name="street" value="<?= esc(old('street') ?: $address->street) ?>" class="search-input" style="width:100%;" required>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
            <div>
                <label for="city" style="display:block; font-weight:600; margin-bottom:5px;">City *</label>
                <input type="text" id="city" name="city" value="<?= esc(old('city') ?: $address->city) ?>" class="search-input" style="width:100%;" required>
            </div>
            <div>
                <label for="region" style="display:block; font-weight:600; margin-bottom:5px;">State/Province</label>
                <input type="text" id="region" name="region" value="<?= esc(old('region') ?: $address->region) ?>" class="search-input" style="width:100%;">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
            <div>
                <label for="postcode" style="display:block; font-weight:600; margin-bottom:5px;">Zip/Postal Code *</label>
                <input type="text" id="postcode" name="postcode" value="<?= esc(old('postcode') ?: $address->postcode) ?>" class="search-input" style="width:100%;" required>
            </div>
            <div>
                <label for="country" style="display:block; font-weight:600; margin-bottom:5px;">Country *</label>
                <input type="text" id="country" name="country" value="<?= esc(old('country') ?: $address->country) ?>" class="search-input" style="width:100%;" required>
            </div>
        </div>

        <div style="margin-bottom:25px;">
            <label for="telephone" style="display:block; font-weight:600; margin-bottom:5px;">Phone Number *</label>
            <input type="text" id="telephone" name="telephone" value="<?= esc(old('telephone') ?: $address->telephone) ?>" class="search-input" style="width:100%;" required>
        </div>

        <!-- Custom Address EAV fields -->
        <?php if (!empty($addressAttributes)) : ?>
            <h3 style="font-size:16px; font-weight:700; margin-bottom:15px; border-bottom:1px solid var(--color-border-light); padding-bottom:5px; margin-top:25px;">Custom Address Options</h3>
            
            <?php foreach ($addressAttributes as $attr) : ?>
                <?php 
                $val = isset($savedValuesMap[$attr->id]) ? esc($savedValuesMap[$attr->id]) : '';
                $required = $attr->is_required ? 'required' : '';
                $reqStar = $attr->is_required ? ' <span style="color:var(--color-danger)">*</span>' : '';
                ?>
                <div style="margin-bottom:15px;">
                    <label for="addr-attr-<?= $attr->id ?>" style="display:block; font-weight:600; margin-bottom:5px; font-size:13px;"><?= esc($attr->frontend_label) ?><?= $reqStar ?></label>
                    
                    <?php if ($attr->input_type === 'text') : ?>
                        <input type="text" id="addr-attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" value="<?= $val ?>" class="search-input" style="width:100%;" <?= $required ?>>
                    <?php elseif ($attr->input_type === 'textarea') : ?>
                        <textarea id="addr-attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="search-input" style="width:100%; height:60px;" <?= $required ?>><?= $val ?></textarea>
                    <?php elseif ($attr->input_type === 'boolean') : ?>
                        <select id="addr-attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="search-input" style="width:150px;" <?= $required ?>>
                            <option value="0" <?= ($val === '0') ? 'selected' : '' ?>>No</option>
                            <option value="1" <?= ($val === '1') ? 'selected' : '' ?>>Yes</option>
                        </select>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:30px;">
            <a href="<?= base_url('customer/account') ?>" style="font-size:13px;"><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i> Back to Dashboard</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 30px;">Save Address</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
