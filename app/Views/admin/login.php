<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Magento 2 CI Admin</title>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main Style Sheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="login-body">
    <main class="login-card">
        <div class="login-logo">
            <i class="fa-solid fa-cart-shopping"></i> MAGNETO <span>CI</span>
        </div>
        
        <!-- Flash messages -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <i class="fa-solid fa-circle-xmark"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success" style="margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <div class="form-control-wrapper">
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required autocomplete="username">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="form-control-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
    </main>
</body>
</html>
