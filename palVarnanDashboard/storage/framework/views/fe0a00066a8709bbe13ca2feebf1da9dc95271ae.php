

<?php $__env->startSection('title'); ?>
    Gifts
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

    /* Pink – Gift Category */
    .theme-pink .card-icon  { background: #FBEAF0; }
    .theme-pink .card-label { color: #993556; }
    .theme-pink .card-pill  { background: #FBEAF0; color: #72243E; border-color: #F4C0D1; }
    .theme-pink::before     { background: #D4537E; }

    /* Purple – Gift Subcategory */
    .theme-purple .card-icon  { background: #EEEDFE; }
    .theme-purple .card-label { color: #534AB7; }
    .theme-purple .card-pill  { background: #EEEDFE; color: #3C3489; border-color: #CECBF6; }
    .theme-purple::before     { background: #7F77DD; }

    /* Coral – Gift Products */
    .theme-coral .card-icon  { background: #FAECE7; }
    .theme-coral .card-label { color: #993C1D; }
    .theme-coral .card-pill  { background: #FAECE7; color: #712B13; border-color: #F5C4B3; }
    .theme-coral::before     { background: #D85A30; }

    .theme-teal .card-icon  { background: #E6F4F1; }
    .theme-teal .card-label { color: #1A6B5A; }
    .theme-teal .card-pill  { background: #E6F4F1; color: #12503F; border-color: #A8D8CF; }
    .theme-teal::before     { background: #2A9D8F; }

    svg.card-ico { width: 18px; height: 18px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Home <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Gifts <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Gift Management</p>
                            <p class="orders-subtitle">Manage gift categories, subcategories and products</p>
                        </div>
                        <span class="orders-date"><?php echo e(now()->format('D, d M Y')); ?></span>
                    </div>

                    <div class="orders-grid">

                       
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Gift Categories.index')): ?>
                        <a href="<?php echo e(route('gift_categories.index')); ?>" class="order-card theme-pink">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="9" width="14" height="8" rx="1.5" stroke="#993556" stroke-width="1.5"/>
                                        <path d="M3 11h14" stroke="#993556" stroke-width="1.2"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 7.5 4S5 6 7.5 7H10z" stroke="#993556" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 12.5 4S15 6 12.5 7H10z" stroke="#993556" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Gifts</p>
                            <p class="card-name">Gift Category</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Categories</span>
                            </div>
                        </a>
                        <?php endif; ?>

                     
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Gift Subcategories.index')): ?>
                        <a href="<?php echo e(route('gift_subcategories.index')); ?>" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="9" width="14" height="8" rx="1.5" stroke="#534AB7" stroke-width="1.5"/>
                                        <path d="M3 11h14" stroke="#534AB7" stroke-width="1.2"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 7.5 4S5 6 7.5 7H10z" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 12.5 4S15 6 12.5 7H10z" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 14h2M9 14h2" stroke="#534AB7" stroke-width="1" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Gifts</p>
                            <p class="card-name">Gift Sub Category</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Subcategories</span>
                            </div>
                        </a>
                        <?php endif; ?>

                       
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Gift Products.index')): ?>
                        <a href="<?php echo e(route('gift_products.index')); ?>" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="9" width="14" height="8" rx="1.5" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M3 11h14" stroke="#993C1D" stroke-width="1.2"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 7.5 4S5 6 7.5 7H10z" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 12.5 4S15 6 12.5 7H10z" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="10" cy="14.5" r="1.5" stroke="#993C1D" stroke-width="1"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Gifts</p>
                            <p class="card-name">Gift Products</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Products</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <a href="/gift-variants" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="9" width="14" height="8" rx="1.5" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M3 11h14" stroke="#993C1D" stroke-width="1.2"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 7.5 4S5 6 7.5 7H10z" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 12.5 4S15 6 12.5 7H10z" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="10" cy="14.5" r="1.5" stroke="#993C1D" stroke-width="1"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Gifts</p>
                            <p class="card-name">Gift Variants</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Variants</span>
                            </div>
                        </a>

                        <a href="<?php echo e(url('/gift-size')); ?>" class="order-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="5" width="5" height="5" rx="1" stroke="#1A6B5A" stroke-width="1.5"/>
                                        <rect x="3" y="12" width="7" height="5" rx="1" stroke="#1A6B5A" stroke-width="1.5"/>
                                        <rect x="12" y="5" width="5" height="7" rx="1" stroke="#1A6B5A" stroke-width="1.5"/>
                                        <rect x="12" y="14" width="5" height="3" rx="1" stroke="#1A6B5A" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Gifts</p>
                            <p class="card-name">Gift Size </p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Attributes</span>
                            </div>
                        </a>


                        <a href="<?php echo e(url('/gift-color')); ?>" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="9" width="14" height="8" rx="1.5" stroke="#534AB7" stroke-width="1.5"/>
                                        <path d="M3 11h14" stroke="#534AB7" stroke-width="1.2"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 7.5 4S5 6 7.5 7H10z" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 9V7M10 7C10 7 10 4 12.5 4S15 6 12.5 7H10z" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 14h2M9 14h2" stroke="#534AB7" stroke-width="1" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Gifts</p>
                            <p class="card-name">Gift Color</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Subcategories</span>
                            </div>
                        </a>

                       
                        <a href="/gift-suppliers" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="4" y="3" width="12" height="14" rx="1.5" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M7 7h6M7 10h6M7 13h4" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round"/>
                                        <circle cx="15" cy="15" r="3.5" fill="#FAECE7" stroke="#993C1D" stroke-width="1"/>
                                        <path d="M14 15l.8.8 1.3-1.3" stroke="#993C1D" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Procurement</p>
                            <p class="card-name">Gift Supplier Details</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Supplier Details</span>
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
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/giftproducts_single.blade.php ENDPATH**/ ?>