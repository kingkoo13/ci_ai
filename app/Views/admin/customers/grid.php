<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Customers Grid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Customers <span>/</span> All Customers
        </div>
        <h1 class="page-title">Customers</h1>
    </div>
</div>

<!-- Customers Table -->
<div class="grid-table-container" style="margin-top: 15px;">
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Group</th>
                <th>Status</th>
                <th>Created At</th>
                <th style="width: 100px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)) : ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--color-text-muted); padding: 20px;">No customers found.</td>
                </tr>
            <?php else : ?>
                <?php foreach ($customers as $customer) : ?>
                    <tr>
                        <td><?= $customer->id ?></td>
                        <td><strong><?= esc($customer->first_name . ' ' . $customer->last_name) ?></strong></td>
                        <td><?= esc($customer->email) ?></td>
                        <td>
                            <?php 
                            if ($customer->group_id == 2) echo 'Wholesale';
                            else if ($customer->group_id == 3) echo 'VIP';
                            else echo 'General';
                            ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= ($customer->is_active) ? 'enabled' : 'disabled' ?>">
                                <?= ($customer->is_active) ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($customer->created_at)) ?></td>
                        <td style="text-align: center;">
                            <a href="<?= base_url('admin/customers/edit/' . $customer->id) ?>" title="Edit" style="color: var(--color-text-light);"><i class="fa-solid fa-user-pen"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
