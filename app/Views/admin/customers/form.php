<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Edit Customer: <?= esc($customer->first_name . ' ' . $customer->last_name) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Customers <span>/</span> All Customers <span>/</span> Edit
            </div>
            <h1 class="page-title"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/customers') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Customer</button>
        </div>
    </div>

    <!-- Tabbed container -->
    <div class="tabs-container">
        <!-- Tab navigation sidebar -->
        <aside class="tabs-sidebar" aria-label="Customer form sections">
            <ul class="tab-nav">
                <li class="tab-nav-item active" onclick="switchTab(event, 'account-tab')">Account Information</li>
                <li class="tab-nav-item" onclick="switchTab(event, 'addresses-tab')">Addresses</li>
            </ul>
        </aside>

        <!-- Tab panels -->
        <div class="tabs-content">
            <!-- 1. Account Details Tab -->
            <div id="account-tab" class="tab-panel active">
                <h2 class="form-section-title">Customer Account Information</h2>
                
                <div class="form-group">
                    <label for="is_active">Status</label>
                    <div class="form-control-wrapper">
                        <select id="is_active" name="is_active" class="form-control" style="width: 150px;">
                            <option value="1" <?= ($customer->is_active == 1) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= ($customer->is_active == 0) ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name', $customer->first_name)) ?>" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name', $customer->last_name)) ?>" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="email" id="email" name="email" value="<?= esc(old('email', $customer->email)) ?>" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="group_id">Customer Group</label>
                    <div class="form-control-wrapper">
                        <select id="group_id" name="group_id" class="form-control" style="width: 250px;">
                            <option value="1" <?= ($customer->group_id == 1) ? 'selected' : '' ?>>General</option>
                            <option value="2" <?= ($customer->group_id == 2) ? 'selected' : '' ?>>Wholesale</option>
                            <option value="3" <?= ($customer->group_id == 3) ? 'selected' : '' ?>>VIP</option>
                        </select>
                    </div>
                </div>

                <!-- Custom Customer Attributes -->
                <?php if (!empty($customerAttributes)) : ?>
                    <div style="margin-top:35px; border-top:1px solid var(--color-border-light); padding-top:20px;">
                        <h3 style="font-size:14px; font-weight:700; color:var(--color-text); margin-bottom:15px;">Custom Profile Attributes</h3>
                        
                        <?php foreach ($customerAttributes as $attr) : ?>
                            <?php 
                            $val = isset($customerValues[$attr->id]) ? esc($customerValues[$attr->id]) : '';
                            $required = $attr->is_required ? 'required' : '';
                            $reqStar = $attr->is_required ? ' <span class="required">*</span>' : '';
                            ?>
                            <div class="form-group">
                                <label for="attr-<?= $attr->id ?>"><?= esc($attr->frontend_label) ?><?= $reqStar ?></label>
                                <div class="form-control-wrapper">
                                    <?php if ($attr->input_type === 'text') : ?>
                                        <input type="text" id="attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" value="<?= $val ?>" class="form-control" <?= $required ?>>
                                    <?php elseif ($attr->input_type === 'textarea') : ?>
                                        <textarea id="attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="form-control" <?= $required ?>><?= $val ?></textarea>
                                    <?php elseif ($attr->input_type === 'boolean') : ?>
                                        <select id="attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="form-control" style="width: 150px;" <?= $required ?>>
                                            <option value="0" <?= ($val === '0') ? 'selected' : '' ?>>No</option>
                                            <option value="1" <?= ($val === '1') ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    <?php elseif ($attr->input_type === 'select') : ?>
                                        <select id="attr-<?= $attr->id ?>" name="attributes[<?= $attr->id ?>]" class="form-control" style="width: 250px;" <?= $required ?>>
                                            <option value="">-- Select Option --</option>
                                            <?php foreach ($attr->options as $opt) : ?>
                                                <option value="<?= esc($opt->option_value) ?>" <?= ($val === $opt->option_value) ? 'selected' : '' ?>>
                                                    <?= esc($opt->option_value) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. Addresses Tab -->
            <div id="addresses-tab" class="tab-panel">
                <h2 class="form-section-title">Addresses Book</h2>
                
                <?php if (empty($addresses)) : ?>
                    <p style="color:var(--color-text-muted); font-style:italic;">No addresses saved for this customer.</p>
                <?php else : ?>
                    <?php foreach ($addresses as $index => $addr) : ?>
                        <div class="address-card" style="border:1px solid var(--color-border); border-radius:var(--border-radius); padding:20px; margin-bottom:25px; background-color:#fafafa;">
                            <h3 style="font-size:14px; font-weight:700; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                                <span>Address #<?= $index + 1 ?></span>
                                <span style="font-weight:normal; font-size:11px;">
                                    <?php if ($addr->is_default_billing) : ?>
                                        <span class="status-badge status-complete" style="margin-right:5px;">Default Billing</span>
                                    <?php endif; ?>
                                    <?php if ($addr->is_default_shipping) : ?>
                                        <span class="status-badge status-complete">Default Shipping</span>
                                    <?php endif; ?>
                                </span>
                            </h3>

                            <div class="form-group">
                                <label for="addr-<?= $addr->id ?>-street">Street Address <span class="required">*</span></label>
                                <div class="form-control-wrapper">
                                    <input type="text" id="addr-<?= $addr->id ?>-street" name="address[<?= $addr->id ?>][street]" value="<?= esc($addr->street) ?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="addr-<?= $addr->id ?>-city">City <span class="required">*</span></label>
                                <div class="form-control-wrapper">
                                    <input type="text" id="addr-<?= $addr->id ?>-city" name="address[<?= $addr->id ?>][city]" value="<?= esc($addr->city) ?>" class="form-control" required style="width:250px;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="addr-<?= $addr->id ?>-region">State/Region</label>
                                <div class="form-control-wrapper">
                                    <input type="text" id="addr-<?= $addr->id ?>-region" name="address[<?= $addr->id ?>][region]" value="<?= esc($addr->region) ?>" class="form-control" style="width:250px;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="addr-<?= $addr->id ?>-postcode">Zip/Postcode <span class="required">*</span></label>
                                <div class="form-control-wrapper">
                                    <input type="text" id="addr-<?= $addr->id ?>-postcode" name="address[<?= $addr->id ?>][postcode]" value="<?= esc($addr->postcode) ?>" class="form-control" required style="width:200px;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="addr-<?= $addr->id ?>-country">Country <span class="required">*</span></label>
                                <div class="form-control-wrapper">
                                    <input type="text" id="addr-<?= $addr->id ?>-country" name="address[<?= $addr->id ?>][country]" value="<?= esc($addr->country) ?>" class="form-control" required style="width:250px;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="addr-<?= $addr->id ?>-tel">Telephone <span class="required">*</span></label>
                                <div class="form-control-wrapper">
                                    <input type="text" id="addr-<?= $addr->id ?>-tel" name="address[<?= $addr->id ?>][telephone]" value="<?= esc($addr->telephone) ?>" class="form-control" required style="width:200px;">
                                </div>
                            </div>

                            <!-- Custom Address-specific EAV Attributes -->
                            <?php if (!empty($addressAttributes)) : ?>
                                <div style="margin-top:20px; border-top:1px dashed var(--color-border); padding-top:15px; background-color:#f5f5f5; padding:15px; border-radius:4px;">
                                    <h4 style="font-size:11px; font-weight:700; text-transform:uppercase; color:var(--color-text-light); margin-bottom:12px;"><i class="fa-solid fa-location-dot"></i> Address Custom Attributes</h4>
                                    
                                    <?php foreach ($addressAttributes as $attr) : ?>
                                        <?php 
                                        $val = isset($addressValues[$addr->id][$attr->id]) ? esc($addressValues[$addr->id][$attr->id]) : '';
                                        $required = $attr->is_required ? 'required' : '';
                                        $reqStar = $attr->is_required ? ' <span class="required">*</span>' : '';
                                        ?>
                                        <div class="form-group" style="grid-template-columns: 180px 1fr;">
                                            <label for="addr-<?= $addr->id ?>-attr-<?= $attr->id ?>"><?= esc($attr->frontend_label) ?><?= $reqStar ?></label>
                                            <div class="form-control-wrapper">
                                                <?php if ($attr->input_type === 'text') : ?>
                                                    <input type="text" id="addr-<?= $addr->id ?>-attr-<?= $attr->id ?>" name="address_attributes[<?= $addr->id ?>][<?= $attr->id ?>]" value="<?= $val ?>" class="form-control" <?= $required ?>>
                                                <?php elseif ($attr->input_type === 'textarea') : ?>
                                                    <textarea id="addr-<?= $addr->id ?>-attr-<?= $attr->id ?>" name="address_attributes[<?= $addr->id ?>][<?= $attr->id ?>]" class="form-control" rows="2" <?= $required ?>><?= $val ?></textarea>
                                                <?php elseif ($attr->input_type === 'boolean') : ?>
                                                    <select id="addr-<?= $addr->id ?>-attr-<?= $attr->id ?>" name="address_attributes[<?= $addr->id ?>][<?= $attr->id ?>]" class="form-control" style="width: 150px;" <?= $required ?>>
                                                        <option value="0" <?= ($val === '0') ? 'selected' : '' ?>>No</option>
                                                        <option value="1" <?= ($val === '1') ? 'selected' : '' ?>>Yes</option>
                                                    </select>
                                                <?php elseif ($attr->input_type === 'select') : ?>
                                                    <select id="addr-<?= $addr->id ?>-attr-<?= $attr->id ?>" name="address_attributes[<?= $addr->id ?>][<?= $attr->id ?>]" class="form-control" style="width: 250px;" <?= $required ?>>
                                                        <option value="">-- Select Option --</option>
                                                        <?php foreach ($attr->options as $opt) : ?>
                                                            <option value="<?= esc($opt->option_value) ?>" <?= ($val === $opt->option_value) ? 'selected' : '' ?>>
                                                                <?= esc($opt->option_value) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<script>
    // Handles changing tabs
    function switchTab(event, tabId) {
        // Deactivate all nav items
        const navItems = document.querySelectorAll('.tab-nav-item');
        navItems.forEach(item => item.classList.remove('active'));
        
        // Activate current nav item
        event.currentTarget.classList.add('active');
        
        // Hide all tab panels
        const panels = document.querySelectorAll('.tab-panel');
        panels.forEach(panel => panel.classList.remove('active'));
        
        // Show current panel
        document.getElementById(tabId).classList.add('active');
    }
</script>
<?= $this->endSection() ?>
