<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>Customer Login<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title">Customer Login</h1>
</div>

<div class="checkout-container" style="grid-template-columns: 1fr 1fr; gap:60px; max-width: 900px; margin: 0 auto;">
    
    <!-- Left: Login block -->
    <div class="checkout-step">
        <h2 style="font-size:18px; font-weight:700; margin-bottom:20px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px;">Registered Customers</h2>
        <p style="color:var(--color-text-light); margin-bottom:20px; font-size:13px;">If you have an account, sign in with your email address.</p>
        
        <form action="<?= base_url('customer/account/login') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div style="margin-bottom:15px;">
                <label for="login-email" style="display:block; font-weight:600; margin-bottom:5px;">Email *</label>
                <input type="email" id="login-email" name="email" class="search-input" style="width:100%;" required placeholder="e.g. jane.doe@example.com">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Password</label>
                <input type="password" disabled class="search-input" style="width:100%; background-color:#e9e9e9;" placeholder="Mock Login: Password Not Required">
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 10px 25px;">Sign In</button>
        </form>
    </div>

    <!-- Right: Register call -->
    <div class="checkout-step" style="background-color:#fafafa;">
        <h2 style="font-size:18px; font-weight:700; margin-bottom:20px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px;">New Customers</h2>
        <p style="color:var(--color-text-light); line-height:1.6; font-size:13px; margin-bottom:25px;">Creating an account has many benefits: check out faster, keep more than one address, track orders and more.</p>
        
        <a href="<?= base_url('customer/account/register') ?>" class="btn btn-primary" style="padding: 10px 25px;">Create an Account</a>
    </div>

</div>
<?= $this->endSection() ?>
