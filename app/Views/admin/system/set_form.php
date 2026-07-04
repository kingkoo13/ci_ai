<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?><?= ($isEdit) ? 'Edit Attribute Set: ' . esc($set->name) : 'New Attribute Set' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Stores <span>/</span> Attributes <span>/</span> Attribute Sets <span>/</span> <?= ($isEdit) ? 'Edit' : 'New' ?>
            </div>
            <h1 class="page-title"><?= ($isEdit) ? esc($set->name) : 'New Attribute Set' ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/stores/attributes/sets') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Attribute Set</button>
        </div>
    </div>

    <!-- Forms Panel -->
    <div class="dashboard-card">
        <div class="card-header">Attribute Set Properties</div>
        <div class="card-body">
            <div class="form-group">
                <label for="name">Attribute Set Name <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="name" name="name" value="<?= esc(old('name', $set->name ?? '')) ?>" class="form-control" required placeholder="e.g. Shoes" <?= ($isEdit && $set->id == 1) ? 'readonly style="background:#f0f0f0;"' : '' ?>>
                    <?php if ($isEdit && $set->id == 1) : ?>
                        <span class="form-note">The primary 'Default' set cannot be renamed.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
