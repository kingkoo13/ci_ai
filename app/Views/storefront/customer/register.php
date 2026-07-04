<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>Create New Customer Account<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title">Create New Customer Account</h1>
</div>

<div class="checkout-step" style="max-width: 600px; margin: 0 auto;">
    <h2 style="font-size:18px; font-weight:700; margin-bottom:20px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px;">Personal Information</h2>
    
    <form action="<?= base_url('customer/account/register') ?>" method="POST">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
            <div>
                <label for="reg-firstname" style="display:block; font-weight:600; margin-bottom:5px;">First Name *</label>
                <input type="text" id="reg-firstname" name="first_name" value="<?= esc(old('first_name')) ?>" class="search-input" style="width:100%;" required>
            </div>
            <div>
                <label for="reg-lastname" style="display:block; font-weight:600; margin-bottom:5px;">Last Name *</label>
                <input type="text" id="reg-lastname" name="last_name" value="<?= esc(old('last_name')) ?>" class="search-input" style="width:100%;" required>
            </div>
        </div>

        <div style="margin-bottom:25px;">
            <label for="reg-email" style="display:block; font-weight:600; margin-bottom:5px;">Email Address *</label>
            <input type="email" id="reg-email" name="email" value="<?= esc(old('email')) ?>" class="search-input" style="width:100%;" required placeholder="e.g. customer@example.com">
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <a href="<?= base_url('customer/account/login') ?>" style="font-size:13px;"><i class="fa-solid fa-chevron-left" style="font-size:10px;"></i> Back to Login</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 30px;">Create Account</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
