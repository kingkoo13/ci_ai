<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>CMS Pages Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Content <span>/</span> Pages
        </div>
        <h1 class="page-title">CMS Pages</h1>
    </div>
</div>

<!-- Pages Grid Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Title</th>
                <th>URL Identifier</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Modified At</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pages)) : ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No pages found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($pages as $page) : ?>
                    <tr>
                        <td><?= $page->id ?></td>
                        <td><strong><?= esc($page->title) ?></strong></td>
                        <td><code>/<?= esc($page->identifier) ?></code></td>
                        <td>
                            <span class="status-badge status-<?= ($page->is_active) ? 'enabled' : 'disabled' ?>">
                                <?= ($page->is_active) ? 'Enabled' : 'Disabled' ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($page->created_at)) ?></td>
                        <td><?= $page->updated_at ? date('M d, Y', strtotime($page->updated_at)) : '-' ?></td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/content/pages/edit/' . $page->id) ?>" title="Edit" style="color: var(--color-text-light);"><i class="fa-solid fa-pen-to-square"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
