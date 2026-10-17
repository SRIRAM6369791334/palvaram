<?php $__env->startSection('title'); ?>
    Contact Inquiries - Pal Varnan Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Home
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Customer Contact Inquiries
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card card-h-100 border shadow-none" style="border-left: 4px solid #0c716a !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate fw-semibold">Total Inquiries</span>
                            <h4 class="mb-0 text-heading fw-bold"><?php echo e($totalCount); ?></h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-email-multiple text-primary font-size-24" style="color: #0c716a !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card card-h-100 border shadow-none" style="border-left: 4px solid #2ab57d !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate fw-semibold">Today's Inquiries</span>
                            <h4 class="mb-0 text-success fw-bold"><?php echo e($todayCount); ?></h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-calendar-today text-success font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card card-h-100 border shadow-none" style="border-left: 4px solid #5156be !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate fw-semibold">Export & Bulk Queries</span>
                            <h4 class="mb-0 text-primary fw-bold"><?php echo e($exportCount); ?></h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-earth text-primary font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-lg-12">
            <div class="card border">
                <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">Customer Inquiries List</h5>
                    
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bx bx-search"></i></span>
                            <input type="text" id="inquirySearch" class="form-control border-start-0" placeholder="Search inquiries...">
                        </div>
                        <select id="subjectFilter" class="form-select form-select-sm" style="width: 180px;">
                            <option value="">All Topics</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Order Tracking">Order Tracking</option>
                            <option value="Global Export">Global Export</option>
                            <option value="Feedback">Feedback</option>
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-nowrap mb-0" id="inquiriesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Customer Details</th>
                                    <th>Contact Info</th>
                                    <th>Subject Topic</th>
                                    <th>Message Snippet</th>
                                    <th>Received Date</th>
                                    <th class="text-end" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-subject="<?php echo e($contact->subject); ?>">
                                        <td><?php echo e($index + 1); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold me-2" style="background: rgba(12, 113, 106, 0.1); color: #0c716a; width: 36px; height: 36px;">
                                                    <?php echo e(strtoupper(substr($contact->name, 0, 1))); ?>

                                                </div>
                                                <div>
                                                    <h6 class="font-size-14 mb-0 text-dark fw-bold"><?php echo e($contact->name); ?></h6>
                                                    <span class="text-muted font-size-12">ID #<?php echo e($contact->id); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a href="mailto:<?php echo e($contact->email); ?>" class="text-dark d-block font-size-13 text-decoration-none">
                                                    <i class="mdi mdi-email-outline text-muted me-1"></i> <?php echo e($contact->email); ?>

                                                </a>
                                                <a href="tel:<?php echo e($contact->phone); ?>" class="text-muted d-block font-size-12 text-decoration-none">
                                                    <i class="mdi mdi-phone-outline text-muted me-1"></i> <?php echo e($contact->phone ?? 'Not Provided'); ?>

                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $badgeClass = 'bg-secondary';
                                                if (str_contains(strtolower($contact->subject), 'export')) {
                                                    $badgeClass = 'bg-primary';
                                                } elseif (str_contains(strtolower($contact->subject), 'order')) {
                                                    $badgeClass = 'bg-info';
                                                } elseif (str_contains(strtolower($contact->subject), 'general')) {
                                                    $badgeClass = 'bg-success';
                                                } elseif (str_contains(strtolower($contact->subject), 'feedback')) {
                                                    $badgeClass = 'bg-warning text-dark';
                                                }
                                            ?>
                                            <span class="badge <?php echo e($badgeClass); ?> font-size-12 py-1 px-2 rounded-pill">
                                                <?php echo e($contact->subject); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted font-size-13 d-inline-block text-truncate" style="max-width: 280px;" title="<?php echo e($contact->message); ?>">
                                                <?php echo e($contact->message); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-size-13 text-dark fw-semibold">
                                                <?php echo e($contact->created_at ? $contact->created_at->format('d M Y, h:i A') : 'N/A'); ?>

                                            </div>
                                            <small class="text-muted font-size-11">
                                                <?php echo e($contact->created_at ? $contact->created_at->diffForHumans() : ''); ?>

                                            </small>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary view-inquiry-btn" 
                                                        style="border-color: #0c716a; color: #0c716a;"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewInquiryModal"
                                                        data-id="<?php echo e($contact->id); ?>"
                                                        data-name="<?php echo e($contact->name); ?>"
                                                        data-email="<?php echo e($contact->email); ?>"
                                                        data-phone="<?php echo e($contact->phone ?? 'N/A'); ?>"
                                                        data-subject="<?php echo e($contact->subject); ?>"
                                                        data-message="<?php echo e($contact->message); ?>"
                                                        data-date="<?php echo e($contact->created_at ? $contact->created_at->format('d M Y, h:i A') : 'N/A'); ?>">
                                                    <i class="mdi mdi-eye-outline me-1"></i> View
                                                </button>
                                                
                                                <form action="<?php echo e(route('contacts.destroy', $contact->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this inquiry?');" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Inquiry">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyRow">
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-3">
                                                <i class="mdi mdi-email-open-outline text-muted font-size-48 d-block mb-3"></i>
                                                <h6 class="text-muted">No Customer Inquiries Found</h6>
                                                <p class="text-muted font-size-13 mb-0">Messages submitted from the storefront contact form will appear here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="viewInquiryModal" tabindex="-1" aria-labelledby="viewInquiryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background: #0c716a;">
                    <h5 class="modal-title text-white" id="viewInquiryModalLabel">
                        <i class="mdi mdi-email-open-outline me-2"></i> Inquiry Details #<span id="modalInquiryId"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="text-muted font-size-12 text-uppercase fw-semibold mb-1 d-block">Customer Name</label>
                            <h6 class="font-size-15 fw-bold mb-0 text-dark" id="modalCustomerName"></h6>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted font-size-12 text-uppercase fw-semibold mb-1 d-block">Submission Date</label>
                            <span class="font-size-14 text-dark" id="modalDate"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted font-size-12 text-uppercase fw-semibold mb-1 d-block">Email Address</label>
                            <a href="#" id="modalEmailLink" class="font-size-14 text-primary text-decoration-underline">
                                <span id="modalEmail"></span>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted font-size-12 text-uppercase fw-semibold mb-1 d-block">Phone Number</label>
                            <a href="#" id="modalPhoneLink" class="font-size-14 text-dark text-decoration-none">
                                <span id="modalPhone"></span>
                            </a>
                        </div>
                        <div class="col-12">
                            <label class="text-muted font-size-12 text-uppercase fw-semibold mb-1 d-block">Inquiry Topic / Subject</label>
                            <span class="badge bg-soft-primary text-primary font-size-13 py-1 px-3 rounded-pill" id="modalSubject" style="background: rgba(12, 113, 106, 0.1); color: #0c716a;"></span>
                        </div>
                    </div>

                    <div class="p-3 rounded bg-light border">
                        <label class="text-muted font-size-12 text-uppercase fw-bold mb-2 d-block">Customer Message</label>
                        <p class="font-size-14 text-dark mb-0 lh-base" id="modalMessage" style="white-space: pre-wrap;"></p>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div>
                        <a href="#" id="modalWhatsAppBtn" target="_blank" class="btn btn-sm btn-success me-2">
                            <i class="mdi mdi-whatsapp me-1"></i> WhatsApp
                        </a>
                        <a href="#" id="modalReplyBtn" class="btn btn-sm btn-primary" style="background: #0c716a; border-color: #0c716a;">
                            <i class="mdi mdi-reply me-1"></i> Reply Email
                        </a>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/js/app.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal population
            const modal = document.getElementById('viewInquiryModal');
            if (modal) {
                modal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const email = button.getAttribute('data-email');
                    const phone = button.getAttribute('data-phone');
                    const subject = button.getAttribute('data-subject');
                    const message = button.getAttribute('data-message');
                    const date = button.getAttribute('data-date');

                    document.getElementById('modalInquiryId').textContent = id;
                    document.getElementById('modalCustomerName').textContent = name;
                    document.getElementById('modalEmail').textContent = email;
                    document.getElementById('modalEmailLink').href = 'mailto:' + email;
                    document.getElementById('modalPhone').textContent = phone;
                    document.getElementById('modalPhoneLink').href = 'tel:' + phone;
                    document.getElementById('modalSubject').textContent = subject;
                    document.getElementById('modalMessage').textContent = message;
                    document.getElementById('modalDate').textContent = date;

                    document.getElementById('modalReplyBtn').href = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject) + ' - Pal Varnan';
                    
                    const cleanPhone = phone.replace(/[^0-9]/g, '');
                    document.getElementById('modalWhatsAppBtn').href = 'https://wa.me/' + cleanPhone + '?text=Hello ' + encodeURIComponent(name) + ', regarding your inquiry on Pal Varnan...';
                });
            }

            // Realtime Search & Subject Filter
            const searchInput = document.getElementById('inquirySearch');
            const subjectFilter = document.getElementById('subjectFilter');
            const tableRows = document.querySelectorAll('#inquiriesTable tbody tr:not(#emptyRow)');

            function filterRows() {
                const query = (searchInput.value || '').toLowerCase().trim();
                const selectedTopic = (subjectFilter.value || '').toLowerCase().trim();

                tableRows.forEach(row => {
                    const rowText = row.innerText.toLowerCase();
                    const rowSubject = (row.getAttribute('data-subject') || '').toLowerCase();

                    const matchesQuery = !query || rowText.includes(query);
                    const matchesTopic = !selectedTopic || rowSubject.includes(selectedTopic);

                    if (matchesQuery && matchesTopic) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterRows);
            if (subjectFilter) subjectFilter.addEventListener('change', filterRows);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/pages/contacts.blade.php ENDPATH**/ ?>