<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Product Attributes Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Stores <span>/</span> Attributes <span>/</span> Product Attributes
        </div>
        <h1 class="page-title">Product Attributes</h1>
    </div>
    <div class="page-actions">
        <a href="<?= base_url('admin/stores/attributes/new') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Attribute</a>
    </div>
</div>

<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th style="width: 150px;">Entity Type</th>
                <th style="width: 150px;">Attribute Code</th>
                <th>Frontend Label</th>
                <th style="width: 150px;">Input Type</th>
                <th style="width: 150px;">Attribute Set</th>
                <th style="width: 100px; text-align: center;">Required</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($attributes)) : ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No attributes found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($attributes as $attr) : ?>
                    <tr>
                        <td><?= $attr->id ?></td>
                        <td>
                            <span class="status-badge status-processing" style="font-size:10px; font-weight:700;">
                                <?= esc(ucfirst($attr->entity_type)) ?>
                            </span>
                        </td>
                        <td><code><?= esc($attr->attribute_code) ?></code></td>
                        <td><strong><?= esc($attr->frontend_label) ?></strong></td>
                        <td><code><?= esc($attr->input_type) ?></code></td>
                        <td><?= $attr->set_name ? esc($attr->set_name) : '<span style="color:var(--color-text-muted); font-style:italic;">N/A</span>' ?></td>
                        <td style="text-align: center;">
                            <?= ($attr->is_required) ? '<span style="color:var(--color-danger); font-weight:700;">Yes</span>' : 'No' ?>
                        </td>
                        <td style="text-align: center; display: flex; justify-content: center; gap: 10px; align-items: center;">
                            <a href="<?= base_url('admin/stores/attributes/edit/' . $attr->id) ?>" title="Edit" style="color: var(--color-text-light);"><i class="fa-solid fa-pen-to-square"></i></a>
                            
                            <button type="button" onclick="confirmDelete(<?= $attr->id ?>, '<?= esc($attr->attribute_code) ?>')" title="Delete" style="border:none; background:none; color: var(--color-danger); cursor:pointer;"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Attribute Delete Form -->
<form id="delete-attribute-form" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
    function confirmDelete(id, code) {
        if (confirm("Are you sure you want to delete attribute '" + code + "'?\n\nWarning: All custom values stored under this attribute across all entities will be permanently removed!")) {
            const form = document.getElementById('delete-attribute-form');
            form.action = "<?= base_url('admin/stores/attributes/delete/') ?>" + id;
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>
