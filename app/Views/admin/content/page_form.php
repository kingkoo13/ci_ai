<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Edit CMS Page: <?= esc($page->title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Content <span>/</span> Pages <span>/</span> Edit
            </div>
            <h1 class="page-title"><?= esc($page->title) ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/content/pages') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Page</button>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">CMS Page Contents</div>
        <div class="card-body">
            <div class="form-group">
                <label for="is_active">Status</label>
                <div class="form-control-wrapper">
                    <select id="is_active" name="is_active" class="form-control" style="width: 150px;">
                        <option value="1" <?= ($page->is_active == 1) ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= ($page->is_active == 0) ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Page Title <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="title" name="title" value="<?= esc(old('title', $page->title)) ?>" class="form-control" required placeholder="e.g. Terms and Conditions">
                </div>
            </div>

            <div class="form-group">
                <label for="identifier">URL Key <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="identifier" name="identifier" value="<?= esc(old('identifier', $page->identifier)) ?>" class="form-control" required placeholder="e.g. terms-conditions">
                    <span class="form-note">Slug path. e.g. <code>about-us</code></span>
                </div>
            </div>

            <div class="form-group">
                <label for="page_layout">Page Layout</label>
                <div class="form-control-wrapper">
                    <select id="page_layout" name="page_layout" class="form-control" style="width: 250px;">
                        <option value="1column" <?= ($page->page_layout === '1column') ? 'selected' : '' ?>>1 column</option>
                        <option value="2columns-left" <?= ($page->page_layout === '2columns-left') ? 'selected' : '' ?>>2 columns with left bar</option>
                        <option value="2columns-right" <?= ($page->page_layout === '2columns-right') ? 'selected' : '' ?>>2 columns with right bar</option>
                        <option value="3columns" <?= ($page->page_layout === '3columns') ? 'selected' : '' ?>>3 columns</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="content">HTML Content <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <textarea id="content" name="content" rows="12" class="form-control" required placeholder="<h1>Page Header</h1><p>Body...</p>"><?= esc(old('content', $page->content)) ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
