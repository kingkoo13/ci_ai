<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>CMS Blocks Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Content <span>/</span> Blocks
        </div>
        <h1 class="page-title">CMS Blocks</h1>
    </div>
</div>

<!-- Blocks Grid Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Title</th>
                <th>Block Identifier</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Modified At</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($blocks)) : ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No blocks found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($blocks as $block) : ?>
                    <tr>
                        <td><?= $block->id ?></td>
                        <td><strong><?= esc($block->title) ?></strong></td>
                        <td><code><?= esc($block->identifier) ?></code></td>
                        <td>
                            <span class="status-badge status-<?= ($block->is_active) ? 'enabled' : 'disabled' ?>">
                                <?= ($block->is_active) ? 'Enabled' : 'Disabled' ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($block->created_at)) ?></td>
                        <td><?= $block->updated_at ? date('M d, Y', strtotime($block->updated_at)) : '-' ?></td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/content/blocks/edit/' . $block->id) ?>" title="Edit" style="color: var(--color-text-light);"><i class="fa-solid fa-pen-to-square"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
