<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>User Roles Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> System <span>/</span> User Roles
        </div>
        <h1 class="page-title">User Roles</h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/system/roles/new') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Role</a>
    </div>
</div>

<!-- Roles Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Role Name</th>
                <th>Permissions Summary</th>
                <th>Created At</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($roles)) : ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No roles found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($roles as $role) : ?>
                    <tr>
                        <td><?= $role->id ?></td>
                        <td><strong><?= esc($role->name) ?></strong></td>
                        <td>
                            <?php 
                            $perms = json_decode($role->permissions, true) ?: [];
                            if (in_array('*', $perms)) {
                                echo '<span class="status-badge status-complete" style="font-size:10px;">All / Administrator Access</span>';
                            } else {
                                echo '<code>' . implode(', ', array_map('esc', $perms)) . '</code>';
                            }
                            ?>
                        </td>
                        <td><?= date('M d, Y', strtotime($role->created_at)) ?></td>
                        <td style="text-align: center; display: flex; justify-content: center; gap: 12px; align-items: center;">
                            <a href="<?= base_url('admin/system/roles/edit/' . $role->id) ?>" title="Edit Permissions" style="color: var(--color-text-light);"><i class="fa-solid fa-user-lock"></i></a>
                            
                            <?php if ($role->id != 1) : ?>
                                <button type="button" onclick="confirmDelete(<?= $role->id ?>, '<?= esc($role->name) ?>')" title="Delete Role" style="border:none; background:none; color: var(--color-danger); cursor:pointer;"><i class="fa-solid fa-trash-can"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Role Delete Submission -->
<form id="delete-role-form" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
    function confirmDelete(id, name) {
        if (confirm("Are you sure you want to delete role '" + name + "'?")) {
            const form = document.getElementById('delete-role-form');
            form.action = "<?= base_url('admin/system/roles/delete/') ?>" + id;
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>
