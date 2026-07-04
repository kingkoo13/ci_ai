<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>All Admin Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> System <span>/</span> All Users
        </div>
        <h1 class="page-title">Users</h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/system/users/new') ?>" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Add New User</a>
    </div>
</div>

<!-- Users Table Grid -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th style="width: 120px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)) : ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No users found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $user->id ?></td>
                        <td><strong><?= esc($user->username) ?></strong></td>
                        <td><?= esc($user->first_name . ' ' . $user->last_name) ?></td>
                        <td><?= esc($user->email) ?></td>
                        <td><span class="status-badge status-complete" style="font-size:10px;"><?= esc($user->role_name) ?></span></td>
                        <td>
                            <span class="status-badge status-<?= ($user->is_active) ? 'enabled' : 'disabled' ?>">
                                <?= ($user->is_active) ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td style="text-align: center; display: flex; justify-content: center; gap: 12px; align-items: center;">
                            <a href="<?= base_url('admin/system/users/edit/' . $user->id) ?>" title="Edit User" style="color: var(--color-text-light);"><i class="fa-solid fa-user-pen"></i></a>
                            
                            <?php if ($user->id != 1 && $user->id != session()->get('admin_id')) : ?>
                                <button type="button" onclick="confirmDelete(<?= $user->id ?>, '<?= esc($user->username) ?>')" title="Delete User" style="border:none; background:none; color: var(--color-danger); cursor:pointer;"><i class="fa-solid fa-trash-can"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- User Delete Submission Form -->
<form id="delete-user-form" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
    function confirmDelete(id, username) {
        if (confirm("Are you sure you want to delete admin user account '" + username + "'?")) {
            const form = document.getElementById('delete-user-form');
            form.action = "<?= base_url('admin/system/users/delete/') ?>" + id;
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>
