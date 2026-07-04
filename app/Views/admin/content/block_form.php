<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Edit CMS Block: <?= esc($block->title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Content <span>/</span> Blocks <span>/</span> Edit
            </div>
            <h1 class="page-title"><?= esc($block->title) ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/content/blocks') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Block</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">CMS Block Contents</div>
        <div class="card-body">
            <div class="form-group">
                <label for="is_active">Status</label>
                <div class="form-control-wrapper">
                    <select id="is_active" name="is_active" class="form-control" style="width: 150px;">
                        <option value="1" <?= ($block->is_active == 1) ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= ($block->is_active == 0) ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Block Title <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="title" name="title" value="<?= esc(old('title', $block->title)) ?>" class="form-control" required placeholder="e.g. Footer Copyright Block">
                </div>
            </div>

            <div class="form-group">
                <label for="identifier">Identifier <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="identifier" name="identifier" value="<?= esc(old('identifier', $block->identifier)) ?>" class="form-control" required placeholder="e.g. footer_copyright">
                    <span class="form-note">Used in templates. e.g. <code>promo_banner</code></span>
                </div>
            </div>

            <div class="form-group">
                <label for="content">Block HTML Content <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <textarea id="content" name="content" rows="10" class="form-control" required placeholder="<div class='promo'>Promo Info</div>"><?= esc(old('content', $block->content)) ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
