<?= $this->extend('storefront/layout') ?>

<?= $this->section('title') ?>My Account Dashboard<?= $this->endSection() ?>

<!-- Left Sidebar Navigation menu -->
<?= $this->section('sidebar_left') ?>
<div class="sidebar-title">My Account</div>
<ul style="list-style:none; padding-left:10px; display:flex; flex-direction:column; gap:10px; font-size:13px;">
    <li><a href="<?= base_url('customer/account') ?>" style="font-weight:700; color:var(--color-primary);"><i class="fa-regular fa-circle-dot"></i> Account Dashboard</a></li>
    <li><a href="<?= base_url('customer/account') ?>" style="color:var(--color-text-light);"><i class="fa-solid fa-address-book"></i> Address Book</a></li>
    <li><a href="<?= base_url('customer/account') ?>" style="color:var(--color-text-light);"><i class="fa-solid fa-basket-shopping"></i> My Orders</a></li>
</ul>
<?= $this->endSection() ?>

<!-- Main Dashboard Content -->
<?= $this->section('content') ?>
<div class="page-title-wrapper">
    <h1 class="page-title">My Account Dashboard</h1>
</div>

<!-- 1. Account details box -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:25px; margin-bottom:40px;">
    
    <!-- Profile Card -->
    <div style="border: 1px solid var(--color-border-light); border-radius: var(--border-radius); padding:20px; background:#fafafa;">
        <h3 style="font-size:14px; font-weight:700; margin-bottom:15px; border-bottom:1px solid var(--color-border-light); padding-bottom:5px; text-transform:uppercase;">Contact Information</h3>
        <p style="font-size:16px; font-weight:600; color:var(--color-text);"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></p>
        <p style="color:var(--color-text-light); margin-top:5px; font-size:13px;"><?= esc($customer->email) ?></p>
        <p style="font-size:11px; color:var(--color-text-muted); margin-top:8px;">Customer Group: General</p>
    </div>

    <!-- Info message block -->
    <div style="border: 1px solid var(--color-border-light); border-radius: var(--border-radius); padding:20px; background:#fafafa; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
        <i class="fa-solid fa-gift" style="font-size:32px; color:var(--color-primary); margin-bottom:10px;"></i>
        <p style="font-weight:700; font-size:13px;">Manage Your Profile Metadata</p>
        <p style="font-size:11px; color:var(--color-text-light); margin-top:5px;">Edit details inline and see your address attributes update instantly.</p>
    </div>

</div>

<!-- 2. Recent Orders listing -->
<div style="margin-bottom:40px;">
    <h2 style="font-weight:300; font-size:22px; margin-bottom:15px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px;">Recent Orders</h2>
    
    <?php if (empty($orders)) : ?>
        <p style="color:var(--color-text-muted); font-style:italic; padding:15px; background:var(--color-bg-light); border-radius:var(--border-radius);">You have placed no orders.</p>
    <?php else : ?>
        <table class="standard-table" style="font-size:13px;">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Ship To</th>
                    <th style="text-align:right;">Order Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $ord) : ?>
                    <tr>
                        <td style="font-weight:700;"><?= esc($ord->increment_id) ?></td>
                        <td><?= date('M d, Y', strtotime($ord->created_at)) ?></td>
                        <td><?= esc($ord->customer_firstname . ' ' . $ord->customer_lastname) ?></td>
                        <td style="text-align:right; font-weight:700;">$<?= number_format($ord->grand_total, 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= $ord->status ?>" style="font-size:10px; padding:3px 8px;">
                                <?= esc(ucfirst($ord->status)) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- 3. Address Book listing with custom Address EAV values -->
<div>
    <h2 style="font-weight:300; font-size:22px; margin-bottom:15px; border-bottom:1px solid var(--color-border-light); padding-bottom:8px;">Address Book</h2>
    
    <?php if (empty($addresses)) : ?>
        <p style="color:var(--color-text-muted); font-style:italic;">No addresses configured in your book.</p>
    <?php else : ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
            <?php foreach ($addresses as $addr) : ?>
                <div style="border: 1px solid var(--color-border-light); border-radius: var(--border-radius); padding:20px; background:#ffffff; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; justify-content:space-between; min-height:180px;">
                    <div>
                        <p style="font-weight:700; color:var(--color-text); margin-bottom:10px;">
                            <?= esc($customer->first_name . ' ' . $customer->last_name) ?>
                        </p>
                        <p style="font-size:13px; color:var(--color-text-light); line-height:1.6;">
                            <?= esc($addr->street) ?><br>
                            <?= esc($addr->city) ?>, <?= esc($addr->region) ?>, <?= esc($addr->postcode) ?><br>
                            <?= esc($addr->country) ?><br>
                            T: <?= esc($addr->telephone) ?>
                        </p>

                        <!-- Address EAV custom attributes outputs -->
                        <?php if (!empty($addressValues[$addr->id])) : ?>
                            <div style="margin-top:12px; border-top:1px dashed var(--color-border-light); padding-top:8px; font-size:12px;">
                                <?php foreach ($addressValues[$addr->id] as $val) : ?>
                                    <?php if ($val->value !== '') : ?>
                                        <p style="color:var(--color-text-light); margin-top:3px;">
                                            <strong><?= esc($val->frontend_label) ?>:</strong> <?= esc($val->value) ?>
                                        </p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top:15px; display:flex; justify-content:flex-end;">
                        <a href="<?= base_url('customer/address/edit/' . $addr->id) ?>" class="btn btn-secondary" style="font-size:11px; padding:4px 10px;"><i class="fa-solid fa-pen"></i> Edit Address</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
