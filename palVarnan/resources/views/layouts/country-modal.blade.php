<!-- ==================== Country Selection Modal ==================== -->
<div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="countryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow-lg">
            <div class="modal-body p-5 text-center">

                <img src="assets/images/logo/logo.png" alt="Logo" class="mb-3" style="height: 50px;">
                <h4 class="fw-bold mb-1">Welcome to Pal Varnan</h4>
                <p class="text-muted mb-4">Please select your country to continue</p>

                <div class="row g-3 justify-content-center">

                    <!-- India -->
                    <div class="col-6">
                        <button class="country-select-btn btn btn-outline-secondary w-100 py-3 rounded-3 d-flex flex-column align-items-center gap-2 hover-shadow"
                            data-country="india">
                            <img src="https://flagcdn.com/w80/in.png" 
                                 alt="India Flag" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <span class="fw-semibold fs-6">India</span>
                            <small class="text-muted">₹ INR</small>
                        </button>
                    </div>

                    <!-- Mexico -->
                    <div class="col-6">
                        <button class="country-select-btn btn btn-outline-secondary w-100 py-3 rounded-3 d-flex flex-column align-items-center gap-2"
                            data-country="mexico">
                            <img src="https://flagcdn.com/w80/mx.png" 
                                 alt="Mexico Flag" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <span class="fw-semibold fs-6">Mexico</span>
                            <small class="text-muted">$ MXN</small>
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<!-- ==================== Country Selection Modal End ==================== -->