<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Attribute Sets Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Stores <span>/</span> Attributes <span>/</span> Attribute Sets
        </div>
        <h1 class="page-title">Attribute Sets</h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/stores/attributes/sets/new') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Set</a>
    </div>
</div>

<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Set Name</th>
                <th>Created At</th>
                <th style="width: 120px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sets)) : ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No sets found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($sets as $set) : ?>
                    <tr>
                        <td><?= $set->id ?></td>
                        <td><strong><?= esc($set->name) ?></strong></td>
                        <td><?= date('M d, Y', strtotime($set->created_at)) ?></td>
                        <td style="text-align: center; display: flex; justify-content: center; gap: 12px; align-items: center;">
                            <a href="<?= base_url('admin/stores/attributes/sets/edit/' . $set->id) ?>" title="Edit" style="color: var(--color-text-light);"><i class="fa-solid fa-pen-to-square"></i></a>
                            
                            <?php if ($set->id != 1) : ?>
                                <button type="button" onclick="confirmDelete(<?= $set->id ?>, '<?= esc($set->name) ?>')" title="Delete" style="border:none; background:none; color: var(--color-danger); cursor:pointer;"><i class="fa-solid fa-trash-can"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Set Delete Form -->
<form id="delete-set-form" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
    function confirmDelete(id, name) {
        if (confirm("Are you sure you want to delete attribute set '" + name + "'?\n\nWarning: All products mapped under this set will be reverted to 'Default' set!")) {
            const form = document.getElementById('delete-set-form');
            form.action = "<?= base_url('admin/stores/attributes/sets/delete/') ?>" + id;
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>
