@yield('css')
<link href="{{ URL::asset('assets/css/bootstrap.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/app.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/custom_styles.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 4px 8px;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0c716a;
        border: 1px solid #0c716a;
        color: #ffffff;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 12px;
        margin-top: 2px;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff;
        margin-right: 5px;
        border: none;
        background: transparent;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcccc;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0c716a;
        outline: 0;
        box-shadow: 0 0 0 0.15rem rgba(12, 113, 106, 0.25);
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
    }
</style>

