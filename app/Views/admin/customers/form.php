<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Edit Customer: <?= esc($customer->first_name . ' ' . $customer->last_name) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Customers <span>/</span> All Customers <span>/</span> Edit
            </div>
            <h1 class="page-title"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/customers') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Customer</button>
        </div>
    </div>

    <!-- Forms Block wrapper -->
    <div class="dashboard-card">
        <div class="card-header">Customer Account Details</div>
        <div class="card-body">
            <div class="form-group">
                <label for="is_active">Status</label>
                <div class="form-control-wrapper">
                    <select id="is_active" name="is_active" class="form-control" style="width: 150px;">
                        <option value="1" <?= ($customer->is_active == 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($customer->is_active == 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="first_name">First Name <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name', $customer->first_name)) ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name', $customer->last_name)) ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="email" id="email" name="email" value="<?= esc(old('email', $customer->email)) ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="group_id">Customer Group</label>
                <div class="form-control-wrapper">
                    <select id="group_id" name="group_id" class="form-control" style="width: 250px;">
                        <option value="1" <?= ($customer->group_id == 1) ? 'selected' : '' ?>>General</option>
                        <option value="2" <?= ($customer->group_id == 2) ? 'selected' : '' ?>>Wholesale</option>
                        <option value="3" <?= ($customer->group_id == 3) ? 'selected' : '' ?>>VIP</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
