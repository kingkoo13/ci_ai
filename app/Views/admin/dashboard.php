<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Dashboard <span>/</span> Overview
        </div>
        <h1 class="page-title">Dashboard</h1>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="kpi-grid">
    <div class="kpi-card">
        <span class="kpi-title">Lifetime Sales</span>
        <span class="kpi-value">$<?= number_format($lifetimeSales, 2) ?></span>
        <span class="kpi-trend trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +12% vs last month</span>
    </div>
    
    <div class="kpi-card">
        <span class="kpi-title">Average Order Value</span>
        <span class="kpi-value">$<?= number_format($averageOrder, 2) ?></span>
        <span class="kpi-trend trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +4.5% vs last month</span>
    </div>
    
    <div class="kpi-card">
        <span class="kpi-title">Total Orders</span>
        <span class="kpi-value"><?= number_format($totalOrders) ?></span>
        <span class="kpi-trend trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +18.2% vs last month</span>
    </div>
    
    <div class="kpi-card">
        <span class="kpi-title">Total Customers</span>
        <span class="kpi-value"><?= number_format($totalCustomers) ?></span>
        <span class="kpi-trend trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +8.1% vs last month</span>
    </div>
</div>

<!-- Chart Area -->
<div class="dashboard-card" style="margin-bottom: 25px;">
    <div class="card-header">
        <span>Sales Analytics (Past 7 Days)</span>
        <div style="font-size: 12px; color: var(--color-text-light);">
            <i class="fa-solid fa-circle" style="color: var(--color-primary); font-size: 10px; margin-right: 5px;"></i> Revenue ($)
        </div>
    </div>
    <div class="card-body">
        <canvas id="salesChart" style="max-height: 300px; width: 100%;"></canvas>
    </div>
</div>

<!-- Dashboard Bottom Grid -->
<div class="dashboard-sections">
    <!-- Left Section: Recent Orders -->
    <div class="dashboard-card">
        <div class="card-header">
            <span>Recent Orders</span>
            <a href="<?= base_url('admin/sales/orders') ?>" class="btn btn-link" style="font-size: 11px;">View All Orders</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="grid-table-container" style="border: none; box-shadow: none;">
                <table class="grid-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                            <th>Purchased On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)) : ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--color-text-muted);">No orders found.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($recentOrders as $order) : ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('admin/sales/orders/view/' . $order->id) ?>" style="font-weight: 600;">
                                            #<?= esc($order->increment_id) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($order->customer_firstname . ' ' . $order->customer_lastname) ?></td>
                                    <td><strong>$<?= number_format($order->grand_total, 2) ?></strong></td>
                                    <td>
                                        <span class="status-badge status-<?= esc(strtolower($order->status)) ?>">
                                            <?= esc($order->status) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y h:i A', strtotime($order->created_at)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Section: Bestsellers & Popular Searches -->
    <div style="display: flex; flex-direction: column; gap: 25px;">
        <!-- Bestsellers Card -->
        <div class="dashboard-card">
            <div class="card-header">Bestsellers</div>
            <div class="card-body" style="padding: 10px 20px;">
                <ul style="display: flex; flex-direction: column; gap: 12px;">
                    <?php if (empty($bestsellers)) : ?>
                        <li style="text-align: center; color: var(--color-text-muted); padding: 15px 0;">No bestsellers yet.</li>
                    <?php else : ?>
                        <?php foreach ($bestsellers as $product) : ?>
                            <li style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                <div style="max-width: 70%;">
                                    <div style="font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?= esc($product->name) ?></div>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">SKU: <?= esc($product->sku) ?></div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 700; color: var(--color-primary);"><?= (int)$product->total_qty ?> sold</div>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">$<?= number_format($product->price, 2) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <!-- Search Terms Card -->
        <div class="dashboard-card">
            <div class="card-header">Popular Search Terms</div>
            <div class="card-body" style="padding: 10px 20px;">
                <ul style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($searchTerms as $search) : ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                            <span><i class="fa-solid fa-magnifying-glass" style="font-size: 10px; color: var(--color-text-muted); margin-right: 8px;"></i> <?= esc($search['term']) ?></span>
                            <span style="font-weight: 600; background: var(--color-bg-light); padding: 2px 8px; border-radius: 10px; font-size: 11px;"><?= $search['uses'] ?> searches</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = <?= json_encode($chartLabels) ?>;
        const dataValues = <?= json_encode($chartValues) ?>;
        
        // Format dates into readable charts label
        const formattedLabels = labels.map(dateStr => {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: [{
                    label: 'Sales Revenue ($)',
                    data: dataValues,
                    borderColor: '#eb5202', // Magento Orange
                    backgroundColor: 'rgba(235, 82, 2, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#eb5202',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return ' $' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e3e3e3'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            },
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
