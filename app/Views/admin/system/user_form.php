<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?><?= ($isEdit) ? 'Edit Admin User: ' . esc($user->username) : 'New Admin User' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> System <span>/</span> All Users <span>/</span> <?= ($isEdit) ? 'Edit' : 'New' ?>
            </div>
            <h1 class="page-title"><?= ($isEdit) ? esc($user->username) : 'New Admin User' ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/system/users') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save User</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">Account Information Details</div>
        <div class="card-body">
            <div class="form-group">
                <label for="is_active">Status</label>
                <div class="form-control-wrapper">
                    <select id="is_active" name="is_active" class="form-control" style="width: 150px;">
                        <option value="1" <?= (!$isEdit || $user->is_active == 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($isEdit && $user->is_active == 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="username" name="username" value="<?= esc(old('username', $user->username ?? '')) ?>" class="form-control" required placeholder="e.g. backend_editor" <?= ($isEdit && $user->id == 1) ? 'readonly' : '' ?>>
                    <?php if ($isEdit && $user->id == 1) : ?>
                        <span class="form-note">Primary admin username cannot be changed.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="first_name">First Name <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name', $user->first_name ?? '')) ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name', $user->last_name ?? '')) ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="email" id="email" name="email" value="<?= esc(old('email', $user->email ?? '')) ?>" class="form-control" required placeholder="name@example.com">
                </div>
            </div>

            <div class="form-group">
                <label for="role_id">User Role <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <select id="role_id" name="role_id" class="form-control" required style="width: 250px;" <?= ($isEdit && $user->id == 1) ? 'disabled' : '' ?>>
                        <?php foreach ($roles as $role) : ?>
                            <option value="<?= $role->id ?>" <?= (($isEdit && $user->role_id == $role->id) || (!$isEdit && $role->id == 1)) ? 'selected' : '' ?>>
                                <?= esc($role->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isEdit && $user->id == 1) : ?>
                        <input type="hidden" name="role_id" value="1">
                        <span class="form-note">Primary administrator role cannot be altered.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password <?= ($isEdit) ? '' : '<span class="required">*</span>' ?></label>
                <div class="form-control-wrapper">
                    <input type="password" id="password" name="password" class="form-control" <?= ($isEdit) ? '' : 'required' ?> minlength="6" placeholder="<?= ($isEdit) ? 'Leave empty to keep existing password' : 'Enter account password' ?>">
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
