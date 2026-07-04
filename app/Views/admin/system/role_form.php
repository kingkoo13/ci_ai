<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?><?= ($isEdit) ? 'Edit Role: ' . esc($role->name) : 'New User Role' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> System <span>/</span> User Roles <span>/</span> <?= ($isEdit) ? 'Edit' : 'New' ?>
            </div>
            <h1 class="page-title"><?= ($isEdit) ? esc($role->name) : 'New User Role' ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/system/roles') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Role</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">Role Properties & ACL Permissions</div>
        <div class="card-body">
            <div class="form-group">
                <label for="name">Role Name <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="name" name="name" value="<?= esc(old('name', $role->name ?? '')) ?>" class="form-control" required placeholder="e.g. Catalog Editors">
                </div>
            </div>

            <div class="form-group">
                <label>Resource Access</label>
                <div class="form-control-wrapper">
                    <!-- Wildcard master checkbox -->
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <input type="checkbox" name="full_access" id="full-access" value="1" <?= (in_array('*', $permissions)) ? 'checked' : '' ?> onchange="togglePermissions(this.checked)">
                        <label for="full-access" style="font-weight: 700; cursor: pointer; padding: 0;">Grant Full Wildcard Access (All Resources)</label>
                    </div>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin-bottom: 15px;">

                    <!-- Specific modules checklist -->
                    <div id="module-permissions" style="display: flex; flex-direction: column; gap: 10px; padding-left: 20px;">
                        <p style="font-weight: 600; font-size: 12px; color: var(--color-text-light); text-transform: uppercase;">Select Modules:</p>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="permissions[]" value="sales" id="perm-sales" class="perm-checkbox" <?= (in_array('sales', $permissions) || in_array('*', $permissions)) ? 'checked' : '' ?>>
                            <label for="perm-sales" style="font-weight: normal; cursor: pointer; padding:0;">Sales (Orders, Invoices, Shipments)</label>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="permissions[]" value="catalog" id="perm-catalog" class="perm-checkbox" <?= (in_array('catalog', $permissions) || in_array('*', $permissions)) ? 'checked' : '' ?>>
                            <label for="perm-catalog" style="font-weight: normal; cursor: pointer; padding:0;">Catalog (Products, Categories)</label>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="permissions[]" value="customers" id="perm-customers" class="perm-checkbox" <?= (in_array('customers', $permissions) || in_array('*', $permissions)) ? 'checked' : '' ?>>
                            <label for="perm-customers" style="font-weight: normal; cursor: pointer; padding:0;">Customers (All Customers)</label>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="permissions[]" value="content" id="perm-content" class="perm-checkbox" <?= (in_array('content', $permissions) || in_array('*', $permissions)) ? 'checked' : '' ?>>
                            <label for="perm-content" style="font-weight: normal; cursor: pointer; padding:0;">Content (CMS Pages, CMS Blocks)</label>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="permissions[]" value="stores" id="perm-stores" class="perm-checkbox" <?= (in_array('stores', $permissions) || in_array('*', $permissions)) ? 'checked' : '' ?>>
                            <label for="perm-stores" style="font-weight: normal; cursor: pointer; padding:0;">Stores (System Configurations)</label>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="permissions[]" value="system" id="perm-system" class="perm-checkbox" <?= (in_array('system', $permissions) || in_array('*', $permissions)) ? 'checked' : '' ?>>
                            <label for="perm-system" style="font-weight: normal; cursor: pointer; padding:0;">System (Cache Management, User Roles, All Users)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function togglePermissions(isChecked) {
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(cb => {
            cb.disabled = isChecked;
            if (isChecked) cb.checked = true;
        });
    }

    // Run on page load to set initial state of checkboxes based on wildcard selection
    document.addEventListener("DOMContentLoaded", function() {
        const fullAccessChecked = document.getElementById('full-access').checked;
        togglePermissions(fullAccessChecked);
    });
</script>
<?= $this->endSection() ?>
