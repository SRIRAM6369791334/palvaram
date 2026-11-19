

<?php $__env->startSection('title'); ?>
    Stock
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    @import  url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600&family=DM+Sans:wght@400;500&display=swap');

    .orders-root {
        font-family: 'DM Sans', sans-serif;
        padding: 2rem 0;
    }

    .orders-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .orders-title {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 2px;
    }

    .orders-subtitle {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }

    .orders-date {
        font-size: 12px;
        color: #adb5bd;
        background: #f8f9fa;
        border: 0.5px solid #e9ecef;
        padding: 6px 14px;
        border-radius: 999px;
    }

    .orders-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .order-card {
        background: #ffffff;
        border: 0.5px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 0;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        position: relative;
        overflow: hidden;
    }

    .order-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 12px 12px 0 0;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .order-card:hover {
        border-color: #ced4da;
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.07);
        text-decoration: none;
    }

    .order-card:hover::before { opacity: 1; }

    .card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-arrow {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .order-card:hover .card-arrow { opacity: 1; }

    .card-label {
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .card-name {
        font-family: 'Sora', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 1rem;
    }

    .card-divider {
        height: 0.5px;
        background: #e9ecef;
        margin-bottom: 0.85rem;
    }

    .card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-pill {
        font-size: 11px;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 999px;
        border: 0.5px solid;
    }

    .card-count-badge {
        font-size: 11px;
        font-weight: 500;
        color: #6c757d;
    }

    /* Blue – Product Stocks */
    .theme-blue .card-icon  { background: #E6F1FB; }
    .theme-blue .card-label { color: #185FA5; }
    .theme-blue .card-pill  { background: #E6F1FB; color: #0C447C; border-color: #B5D4F4; }
    .theme-blue::before     { background: #378ADD; }

    /* Teal – Stock Transfer */
    .theme-teal .card-icon  { background: #E1F5EE; }
    .theme-teal .card-label { color: #0F6E56; }
    .theme-teal .card-pill  { background: #E1F5EE; color: #085041; border-color: #9FE1CB; }
    .theme-teal::before     { background: #1D9E75; }

    svg.card-ico { width: 18px; height: 18px; }

    .theme-purple .card-icon  { background: #EEEDFE; }
    .theme-purple .card-label { color: #534AB7; }
    .theme-purple .card-pill  { background: #EEEDFE; color: #3C3489; border-color: #AFA9EC; }
    .theme-purple::before     { background: #7F77DD; }

    /* Orange – Stock Tracking */
    .theme-orange .card-icon  { background: #FEF3E6; }
    .theme-orange .card-label { color: #B35A00; }
    .theme-orange .card-pill  { background: #FEF3E6; color: #7A3D00; border-color: #F9C88A; }
    .theme-orange::before     { background: #F08030; }

    /* Rose – Stock Management */
    .theme-rose .card-icon  { background: #FDEDF0; }
    .theme-rose .card-label { color: #A3264A; }
    .theme-rose .card-pill  { background: #FDEDF0; color: #7B1535; border-color: #F2A4B5; }
    .theme-rose::before     { background: #D94F72; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Home <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Stock <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Stock Management</p>
                            <p class="orders-subtitle">Monitor inventory levels and manage stock transfers</p>
                        </div>
                        <span class="orders-date"><?php echo e(now()->format('D, d M Y')); ?></span>
                    </div>

                    <div class="orders-grid">

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Stocks.index')): ?>
                        <a href="<?php echo e(route('stocks.index')); ?>" class="order-card theme-blue">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="13" width="3" height="4" rx="0.75" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="8.5" y="9" width="3" height="8" rx="0.75" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="14" y="5" width="3" height="12" rx="0.75" stroke="#185FA5" stroke-width="1.5"/>
                                        <path d="M3 6l4-3 4 4 5-4" stroke="#185FA5" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Inventory</p>
                            <p class="card-name">Product Stocks</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Stocks</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Stock Distribution.index')): ?>
                        <a href="/stock-distribution" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 3v14M10 3L6 7M10 3l4 4" stroke="#534AB7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M3 10h14" stroke="#534AB7" stroke-width="1.5" stroke-linecap="round"/>
                                        <circle cx="3" cy="10" r="1.5" fill="#534AB7"/>
                                        <circle cx="17" cy="10" r="1.5" fill="#534AB7"/>
                                        <circle cx="10" cy="17" r="1.5" fill="#534AB7"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Distribution</p>
                            <p class="card-name">Stock Distribution</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Distribute</span>
                                <span class="card-count-badge">Warehouse → Store</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Stock Transfer.index')): ?>
                        <a href="/transfer-stock" class="order-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M3 7h14M13 4l4 3-4 3" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17 13H3M7 10l-4 3 4 3" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Transfer</p>
                            <p class="card-name">Stock Transfer</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Initiate</span>
                                <span class="card-count-badge">Transfers</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Stock Tracking.index')): ?>
                        <a href="/stock-tracking" class="order-card theme-orange">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="3" stroke="#B35A00" stroke-width="1.5"/>
                                        <path d="M10 2v2M10 16v2M2 10h2M16 10h2" stroke="#B35A00" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M4.93 4.93l1.41 1.41M13.66 13.66l1.41 1.41M4.93 15.07l1.41-1.41M13.66 6.34l1.41-1.41" stroke="#B35A00" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Tracking</p>
                            <p class="card-name">Stock Tracking</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Track</span>
                                <span class="card-count-badge">Live Status</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Stock Management.index')): ?>
                        <a href="/product-management" class="order-card theme-rose">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="3" width="6" height="6" rx="1" stroke="#A3264A" stroke-width="1.5"/>
                                        <rect x="11" y="3" width="6" height="6" rx="1" stroke="#A3264A" stroke-width="1.5"/>
                                        <rect x="3" y="11" width="6" height="6" rx="1" stroke="#A3264A" stroke-width="1.5"/>
                                        <path d="M11 14h6M14 11v6" stroke="#A3264A" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Management</p>
                            <p class="card-name">Stock Management</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Manage</span>
                                <span class="card-count-badge">Configure</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <a href="/gift-stock" class="order-card theme-blue">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="13" width="3" height="4" rx="0.75" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="8.5" y="9" width="3" height="8" rx="0.75" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="14" y="5" width="3" height="12" rx="0.75" stroke="#185FA5" stroke-width="1.5"/>
                                        <path d="M3 6l4-3 4 4 5-4" stroke="#185FA5" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Inventory</p>
                            <p class="card-name">Gift Product Stocks</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Stocks</span>
                            </div>
                        </a>


                        <a href="/gift-stock-distribution" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 3v14M10 3L6 7M10 3l4 4" stroke="#534AB7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M3 10h14" stroke="#534AB7" stroke-width="1.5" stroke-linecap="round"/>
                                        <circle cx="3" cy="10" r="1.5" fill="#534AB7"/>
                                        <circle cx="17" cy="10" r="1.5" fill="#534AB7"/>
                                        <circle cx="10" cy="17" r="1.5" fill="#534AB7"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Distribution</p>
                            <p class="card-name">Gift Stock Distribution</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Distribute</span>
                                <span class="card-count-badge">Warehouse → Store</span>
                            </div>
                        </a>

                        <a href="/gift-transfer-stock" class="order-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M3 7h14M13 4l4 3-4 3" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17 13H3M7 10l-4 3 4 3" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Transfer</p>
                            <p class="card-name">Gift Stock Transfer</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Initiate</span>
                                <span class="card-count-badge">Transfers</span>
                            </div>
                        </a>


                        <a href="/gift-stock-tracking" class="order-card theme-orange">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="3" stroke="#B35A00" stroke-width="1.5"/>
                                        <path d="M10 2v2M10 16v2M2 10h2M16 10h2" stroke="#B35A00" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M4.93 4.93l1.41 1.41M13.66 13.66l1.41 1.41M4.93 15.07l1.41-1.41M13.66 6.34l1.41-1.41" stroke="#B35A00" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Tracking</p>
                            <p class="card-name">Gift Stock Tracking</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Track</span>
                                <span class="card-count-badge">Live Status</span>
                            </div>
                        </a>


                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/stock_single.blade.php ENDPATH**/ ?>