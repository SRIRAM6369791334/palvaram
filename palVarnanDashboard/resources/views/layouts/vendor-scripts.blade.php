    <script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/metismenujs/metismenujs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js') }}"></script>

    <script src="{{ URL::asset('assets/libs/feather-icons/feather-icons.min.js') }}"></script>
    <script src="{{ asset('assets/js/invoice/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"
        integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/justValidate/justValidate.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            function initSearchableSelects(context) {
                if (typeof $.fn.select2 !== 'function') return;
                $(context || document).find('select.select2, select.searchable-select').each(function() {
                    var $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }
                    var $modal = $select.closest('.modal');
                    var options = {
                        width: '100%',
                        placeholder: $select.find('option:first-child').text() || 'Select an option',
                        allowClear: !$select.prop('required')
                    };
                    if ($modal.length) {
                        options.dropdownParent = $modal;
                    }
                    $select.select2(options);
                });
            }

            initSearchableSelects(document);

            $(document).on('shown.bs.modal', '.modal', function() {
                initSearchableSelects(this);
            });
        });
    </script>
    @yield('script')

