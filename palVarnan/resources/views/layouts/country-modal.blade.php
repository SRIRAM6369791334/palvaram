<!-- ==================== Country Selection Modal ==================== -->
<div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="countryModalLabel" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-16 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 pe-24 pt-20">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-32 pt-0 text-center">
                <img src="/assets/images/logo/logo.png" alt="Pal Varnan" class="mb-16" style="max-height: 50px; width: auto; object-fit: contain;">
                <h4 class="fw-bold mb-8 text-heading">Select Your Country / Region</h4>
                <p class="text-neutral-500 mb-28 text-sm">Choose your delivery country to see local catalog, pricing, and currency.</p>

                <div class="row g-16 justify-content-center">
                    <!-- India -->
                    <div class="col-6">
                        <form method="POST" action="/select-country" class="m-0">
                            @csrf
                            <input type="hidden" name="country" value="india">
                            <button type="submit" class="btn btn-outline-neutral-100 w-100 py-20 px-16 rounded-12 d-flex flex-column align-items-center gap-10 hover-border-main-600 transition-1 {{ session('country_id') == 243 ? 'border-main-600 bg-main-50' : 'bg-white' }}">
                                <img src="/assets/images/flags/india.png" 
                                     alt="India Flag" 
                                     style="width: 52px; height: 35px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='https://flagcdn.com/w80/in.png'">
                                <span class="fw-bold text-heading text-base">India</span>
                                <span class="badge bg-main-100 text-main-600 text-xs px-10 py-4 rounded-pill fw-semibold">₹ INR (Rupee)</span>
                            </button>
                        </form>
                    </div>

                    <!-- Mexico -->
                    <div class="col-6">
                        <form method="POST" action="/select-country" class="m-0">
                            @csrf
                            <input type="hidden" name="country" value="mexico">
                            <button type="submit" class="btn btn-outline-neutral-100 w-100 py-20 px-16 rounded-12 d-flex flex-column align-items-center gap-10 hover-border-main-600 transition-1 {{ session('country_id') == 244 ? 'border-main-600 bg-main-50' : 'bg-white' }}">
                                <img src="/assets/images/flags/mexico.png" 
                                     alt="Mexico Flag" 
                                     style="width: 52px; height: 35px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='https://flagcdn.com/w80/mx.png'">
                                <span class="fw-bold text-heading text-base">Mexico</span>
                                <span class="badge bg-main-100 text-main-600 text-xs px-10 py-4 rounded-pill fw-semibold">$ MXN (Peso)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ==================== Country Selection Modal End ==================== -->