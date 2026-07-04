<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Cache Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> System <span>/</span> Cache Management
        </div>
        <h1 class="page-title">Cache Management</h1>
    </div>
    
    <div class="page-actions">
        <form action="<?= base_url('admin/system/cache/flush') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-trash-can"></i> Flush Cache Storage</button>
        </form>
    </div>
</div>

<p style="margin-bottom: 20px; color: var(--color-text-light);">Flush CodeIgniter framework templates, configuration indices, and database query cache files:</p>

<div class="grid-table-container">
    <table class="grid-table">
        <thead>
            <tr>
                <th>Cache Type</th>
                <th>Description</th>
                <th style="width: 150px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Configuration</strong></td>
                <td>System configuration file options, parameters, and environmental overrides.</td>
                <td style="text-align: center;"><span class="status-badge status-enabled">Enabled</span></td>
            </tr>
            <tr>
                <td><strong>Layouts & Views</strong></td>
                <td>Precompiled layout blocks, views structures, and static script configurations.</td>
                <td style="text-align: center;"><span class="status-badge status-enabled">Enabled</span></td>
            </tr>
            <tr>
                <td><strong>Collections Data</strong></td>
                <td>Database queries result sets and configurations parameters.</td>
                <td style="text-align: center;"><span class="status-badge status-enabled">Enabled</span></td>
            </tr>
            <tr>
                <td><strong>Translations</strong></td>
                <td>Multi-language lookup keys and dictionary arrays.</td>
                <td style="text-align: center;"><span class="status-badge status-enabled">Enabled</span></td>
            </tr>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
