<header id="page-topbar" class="ishorizontal-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?php echo e(url('/')); ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?php echo e(URL::asset('assets/images/logo-sm.svg')); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo e(URL::asset('assets/images/logo-sm.svg')); ?>" alt="" height="22"> <span
                            class="logo-txt"><?php echo app('translator')->get('translation.Yesbe'); ?></span>
                    </span>
                </a>

                <a href="<?php echo e(url('/')); ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?php echo e(URL::asset('assets/images/logo-sm.svg')); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo e(URL::asset('assets/images/logo-sm.svg')); ?>" alt="" height="22"> <span
                            class="logo-txt"><?php echo app('translator')->get('translation.Yesbe'); ?></span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="topnav">
                <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                    <div class="collapse navbar-collapse" id="topnav-menu-content">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle arrow-none" href="index" id="topnav-dashboard"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class='bx bx-tachometer'></i>
                                    <span data-key="t-dashboards"><?php echo app('translator')->get('translation.Dashboard'); ?></span>
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages"
                                    role="button">
                                    <i class='bx bx-grid-alt'></i>
                                    <span data-key="t-apps"><?php echo app('translator')->get('translation.Apps'); ?></span>
                                    <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-pages">

                                    <a href="apps-calendar" class="dropdown-item"
                                        data-key="t-calendar"><?php echo app('translator')->get('translation.Calendar'); ?></a>
                                    <a href="apps-chat" class="dropdown-item" data-key="t-chat"><?php echo app('translator')->get('translation.Chat'); ?></a>
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-email" role="button">
                                            <span data-key="t-email"><?php echo app('translator')->get('translation.Email'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-email">
                                            <a href="email-inbox" class="dropdown-item"
                                                data-key="t-inbox"><?php echo app('translator')->get('translation.Inbox'); ?></a>
                                            <a href="email-read" class="dropdown-item"
                                                data-key="t-read-email"><?php echo app('translator')->get('translation.Read_Email'); ?></a>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-ecommerce" role="button"><?php echo app('translator')->get('translation.Ecommerce'); ?> <div
                                                class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-ecommerce">
                                            <a href="ecommerce-products" class="dropdown-item"
                                                data-key="t-products"><?php echo app('translator')->get('translation.Products'); ?></a>
                                            <a href="ecommerce-product-detail" class="dropdown-item"
                                                data-key="t-product-detail"><?php echo app('translator')->get('translation.Product_Detail'); ?></a>
                                            <a href="ecommerce-orders" class="dropdown-item"
                                                data-key="t-orders"><?php echo app('translator')->get('translation.Orders'); ?></a>
                                            <a href="ecommerce-customers" class="dropdown-item"
                                                data-key="t-customers"><?php echo app('translator')->get('translation.Customers'); ?></a>
                                            <a href="ecommerce-cart" class="dropdown-item"
                                                data-key="t-cart"><?php echo app('translator')->get('translation.Cart'); ?></a>
                                            <a href="ecommerce-checkout" class="dropdown-item"
                                                data-key="t-checkout"><?php echo app('translator')->get('translation.Checkout'); ?></a>
                                            <a href="ecommerce-shops" class="dropdown-item"
                                                data-key="t-shops"><?php echo app('translator')->get('translation.Shops'); ?></a>
                                            <a href="ecommerce-add-product" class="dropdown-item"
                                                data-key="t-add-product"><?php echo app('translator')->get('translation.Add_Product'); ?></a>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-invoice" role="button"><span
                                                data-key="t-invoices"><?php echo app('translator')->get('translation.Invoices'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-invoice">
                                            <a href="invoices-list" class="dropdown-item"
                                                data-key="t-invoice-list"><?php echo app('translator')->get('translation.Invoice_List'); ?></a>
                                            <a href="invoices-detail" class="dropdown-item"
                                                data-key="t-invoice-detail"><?php echo app('translator')->get('translation.Invoice_Detail'); ?></a>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-contact" role="button">
                                            <span data-key="t-contacts"><?php echo app('translator')->get('translation.Contacts'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-contact">
                                            <a href="contacts-grid" class="dropdown-item"
                                                data-key="t-user-grid"><?php echo app('translator')->get('translation.User_Grid'); ?></a>
                                            <a href="contacts-list" class="dropdown-item"
                                                data-key="t-user-list"><?php echo app('translator')->get('translation.User_List'); ?></a>
                                            <a href="contacts-profile" class="dropdown-item"
                                                data-key="t-user-settings"><?php echo app('translator')->get('translation.User_Profile'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-uielement"
                                    role="button" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class='bx bxl-bootstrap'></i>
                                    <span data-key="t-bootstrap"><?php echo app('translator')->get('translation.Bootstrap'); ?></span>
                                    <div class="arrow-down"></div>
                                </a>

                                <div class="dropdown-menu mega-dropdown-menu px-2 dropdown-mega-menu-xl"
                                    aria-labelledby="topnav-uielement">
                                    <div class="ps-2 p-lg-0">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div>
                                                    <div class="menu-title">Elements</div>
                                                    <div class="row g-0">
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <a href="ui-alerts" class="dropdown-item"
                                                                    data-key="t-alerts"><?php echo app('translator')->get('translation.Alerts'); ?></a>
                                                                <a href="ui-buttons" class="dropdown-item"
                                                                    data-key="t-buttons"><?php echo app('translator')->get('translation.Buttons'); ?></a>
                                                                <a href="ui-cards" class="dropdown-item"
                                                                    data-key="t-cards"><?php echo app('translator')->get('translation.Cards'); ?></a>
                                                                <a href="ui-carousel" class="dropdown-item"
                                                                    data-key="t-carousel"><?php echo app('translator')->get('translation.Carousel'); ?></a>
                                                                <a href="ui-dropdowns" class="dropdown-item"
                                                                    data-key="t-dropdowns"><?php echo app('translator')->get('translation.Dropdowns'); ?></a>
                                                                <a href="ui-grid" class="dropdown-item"
                                                                    data-key="t-grid"><?php echo app('translator')->get('translation.Grid'); ?></a>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <a href="ui-images" class="dropdown-item"
                                                                    data-key="t-images"><?php echo app('translator')->get('translation.Images'); ?></a>
                                                                <a href="ui-modals" class="dropdown-item"
                                                                    data-key="t-modals"><?php echo app('translator')->get('translation.Modals'); ?></a>
                                                                <a href="ui-offcanvas" class="dropdown-item"
                                                                    data-key="t-offcanvas"><?php echo app('translator')->get('translation.Offcanvas'); ?></a>
                                                                <a href="ui-placeholders" class="dropdown-item"
                                                                    data-key="t-placeholders"><?php echo app('translator')->get('translation.Placeholders'); ?></a>
                                                                <a href="ui-progressbars" class="dropdown-item"
                                                                    data-key="t-progress-bars"><?php echo app('translator')->get('translation.Progress_Bars'); ?> </a>
                                                                <a href="ui-tabs-accordions" class="dropdown-item"
                                                                    data-key="t-tabs-accordions"><?php echo app('translator')->get('translation.Tabs_&_Accordions'); ?></a>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <a href="ui-typography" class="dropdown-item"
                                                                data-key="t-typography"><?php echo app('translator')->get('translation.Typography'); ?></a>
                                                            <a href="ui-video" class="dropdown-item"
                                                                data-key="t-video"><?php echo app('translator')->get('translation.Video'); ?></a>
                                                            <a href="ui-general" class="dropdown-item"
                                                                data-key="t-general"><?php echo app('translator')->get('translation.General'); ?></a>
                                                            <a href="ui-colors" class="dropdown-item"
                                                                data-key="t-colors"><?php echo app('translator')->get('translation.Colors'); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components"
                                    role="button">
                                    <i class='bx bx-layer'></i>
                                    <span data-key="t-components"><?php echo app('translator')->get('translation.Components'); ?></span>
                                    <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-components">
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-extended" role="button">
                                            <span data-key="t-extendeds"><?php echo app('translator')->get('translation.Extended'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-form">
                                            <a href="extended-lightbox" class="dropdown-item"
                                                data-key="t-lightbox"><?php echo app('translator')->get('translation.Lightbox'); ?></a>
                                            <a href="extended-rangeslider" class="dropdown-item"
                                                data-key="t-range-slider"><?php echo app('translator')->get('translation.Range_Slider'); ?> </a>
                                            <a href="extended-sweet-alert" class="dropdown-item"
                                                data-key="t-sweet-alert"><?php echo app('translator')->get('translation.SweetAlert_2'); ?> </a>
                                            <a href="extended-rating" class="dropdown-item"
                                                data-key="t-rating"><?php echo app('translator')->get('translation.Rating'); ?></a>
                                            <a href="extended-notifications" class="dropdown-item"
                                                data-key="t-notifications"><?php echo app('translator')->get('translation.Notifications'); ?></a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-form" role="button">
                                            <span data-key="t-forms"><?php echo app('translator')->get('translation.Forms'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-form">
                                            <a href="form-elements" class="dropdown-item"
                                                data-key="t-basic-elements"><?php echo app('translator')->get('translation.Basic_Elements'); ?></a>
                                            <a href="form-validation" class="dropdown-item"
                                                data-key="t-validation"><?php echo app('translator')->get('translation.Validation'); ?></a>
                                            <a href="form-advanced" class="dropdown-item"
                                                data-key="t-advanced-plugins"><?php echo app('translator')->get('translation.Advanced_Plugins'); ?></a>
                                            <a href="form-editors" class="dropdown-item"
                                                data-key="t-editors"><?php echo app('translator')->get('translation.Editors'); ?></a>
                                            <a href="form-uploads" class="dropdown-item"
                                                data-key="t-file-upload"><?php echo app('translator')->get('translation.File_Upload'); ?></a>
                                            <a href="form-wizard" class="dropdown-item"
                                                data-key="t-wizard"><?php echo app('translator')->get('translation.Wizard'); ?></a>
                                            <a href="form-mask" class="dropdown-item"
                                                data-key="t-mask"><?php echo app('translator')->get('translation.Mask'); ?></a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-table" role="button">
                                            <span data-key="t-tables"><?php echo app('translator')->get('translation.Tables'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-table">
                                            <a href="tables-basic" class="dropdown-item"
                                                data-key="t-bootstrap-basic"><?php echo app('translator')->get('translation.Bootstrap_Basic'); ?></a>
                                            <a href="tables-advanced" class="dropdown-item"
                                                data-key="t-advanced-tables"><?php echo app('translator')->get('translation.Advance_Tables'); ?></a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-charts" role="button">
                                            <span data-key="t-charts"><?php echo app('translator')->get('translation.Charts'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-charts">
                                            <a href="charts-apex" class="dropdown-item"
                                                data-key="t-apex-charts"><?php echo app('translator')->get('translation.Apex_Charts'); ?> </a>
                                            <a href="charts-chartjs" class="dropdown-item"
                                                data-key="t-chartjs-charts"><?php echo app('translator')->get('translation.Chartjs'); ?></a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-icons" role="button">
                                            <span data-key="t-icons"><?php echo app('translator')->get('translation.Icons'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-icons">
                                            <a href="icons-feather" class="dropdown-item"
                                                data-key="t-feather"><?php echo app('translator')->get('translation.Feather'); ?></a>
                                            <a href="icons-boxicons" class="dropdown-item"
                                                data-key="t-boxicons"><?php echo app('translator')->get('translation.Boxicons'); ?></a>
                                            <a href="icons-materialdesign" class="dropdown-item"
                                                data-key="t-material-design"><?php echo app('translator')->get('translation.Material_Design'); ?> </a>
                                            <a href="icons-dripicons" class="dropdown-item"
                                                data-key="t-dripicons"><?php echo app('translator')->get('translation.Dripicons'); ?></a>
                                            <a href="icons-fontawesome" class="dropdown-item"
                                                data-key="t-font-awesome"><?php echo app('translator')->get('translation.Font_Awesome_5'); ?> </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-map" role="button">
                                            <span data-key="t-maps"><?php echo app('translator')->get('translation.Maps'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-map">
                                            <a href="maps-google" class="dropdown-item"
                                                data-key="t-google"><?php echo app('translator')->get('translation.Google'); ?></a>
                                            <a href="maps-vector" class="dropdown-item"
                                                data-key="t-vector"><?php echo app('translator')->get('translation.Vector'); ?></a>
                                            <a href="maps-leaflet" class="dropdown-item"
                                                data-key="t-leaflet"><?php echo app('translator')->get('translation.Leaflet'); ?></a>
                                        </div>
                                    </div>
                                </div>

                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                    role="button">
                                    <i class='bx bx-file'></i>
                                    <span data-key="t-pages"><?php echo app('translator')->get('translation.Pages'); ?></span>
                                    <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-more">
                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-auth" role="button">
                                            <span data-key="t-authentication"><?php echo app('translator')->get('translation.Authentication'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-auth">
                                            <a href="auth-login" class="dropdown-item"
                                                data-key="t-login"><?php echo app('translator')->get('translation.Login'); ?></a>
                                            <a href="auth-register" class="dropdown-item"
                                                data-key="t-register"><?php echo app('translator')->get('translation.Register'); ?></a>
                                            <a href="auth-recoverpw" class="dropdown-item"
                                                data-key="t-recover-password"><?php echo app('translator')->get('translation.Recover_Password'); ?></a>
                                            <a href="auth-lock-screen" class="dropdown-item"
                                                data-key="t-lock-screen"><?php echo app('translator')->get('translation.Lock_Screen'); ?> </a>
                                            <a href="auth-logout" class="dropdown-item"
                                                data-key="t-logout"><?php echo app('translator')->get('translation.Log_Out'); ?> </a>
                                            <a href="auth-confirm-mail" class="dropdown-item"
                                                data-key="t-confirm-mail"><?php echo app('translator')->get('translation.Confirm_Mail'); ?> </a>
                                            <a href="auth-email-verification" class="dropdown-item"
                                                data-key="t-email-verification"><?php echo app('translator')->get('translation.Email_Verification'); ?> </a>
                                            <a href="auth-two-step-verification" class="dropdown-item"
                                                data-key="t-two-step-verification"><?php echo app('translator')->get('translation.Two_Step_Verification'); ?> </a>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                            id="topnav-utility" role="button">
                                            <span data-key="t-utility"><?php echo app('translator')->get('translation.Utility'); ?></span>
                                            <div class="arrow-down"></div>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="topnav-utility">
                                            <a href="pages-starter" class="dropdown-item"
                                                data-key="t-starter-page"><?php echo app('translator')->get('translation.Starter_Page'); ?></a>
                                            <a href="pages-maintenance" class="dropdown-item"
                                                data-key="t-maintenance"><?php echo app('translator')->get('translation.Maintenance'); ?></a>
                                            <a href="pages-comingsoon" class="dropdown-item"
                                                data-key="t-coming-soon"><?php echo app('translator')->get('translation.Coming_Soon'); ?></a>
                                            <a href="pages-timeline" class="dropdown-item"
                                                data-key="t-timeline"><?php echo app('translator')->get('translation.Timeline'); ?></a>
                                            <a href="pages-faqs" class="dropdown-item"
                                                data-key="t-faqs"><?php echo app('translator')->get('translation.FAQs'); ?></a>
                                            <a href="pages-pricing" class="dropdown-item"
                                                data-key="t-pricing"><?php echo app('translator')->get('translation.Pricing'); ?></a>
                                            <a href="pages-404" class="dropdown-item"
                                                data-key="t-error-404"><?php echo app('translator')->get('translation.Error_404'); ?></a>
                                            <a href="pages-500" class="dropdown-item"
                                                data-key="t-error-500"><?php echo app('translator')->get('translation.Error_500'); ?></a>
                                        </div>
                                    </div>

                                    <a href="layouts-vertical" class="dropdown-item"
                                        data-key="t-vertical"><?php echo app('translator')->get('translation.Vertical'); ?></a>
                                </div>
                            </li>

                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="icon-sm" data-feather="search"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                    <form class="p-3">
                        <div class="search-box">
                            <div class="position-relative">
                                <input type="text" class="form-control rounded" placeholder="Search here...">
                                <i class="mdi mdi-magnify search-icon"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dropdown d-inline-block language-switch">
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <img class="header-lang-img" src="<?php echo e(URL::asset('assets/images/flags/us.jpg')); ?>"
                        alt="Header Language" height="16">
                </button>
                <div class="dropdown-menu dropdown-menu-end">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="eng">
                        <img src="<?php echo e(URL::asset('assets/images/flags/us.jpg')); ?>" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">English</span>
                    </a>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="sp">
                        <img src="<?php echo e(URL::asset('assets/images/flags/spain.jpg')); ?>" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Spanish</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="gr">
                        <img src="<?php echo e(URL::asset('assets/images/flags/germany.jpg')); ?>" alt="user-image"
                            class="me-1" height="12"> <span class="align-middle">German</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="it">
                        <img src="<?php echo e(URL::asset('assets/images/flags/italy.jpg')); ?>" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">Italian</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item language" data-lang="ru">
                        <img src="<?php echo e(URL::asset('assets/images/flags/russia.jpg')); ?>" alt="user-image"
                            class="me-1" height="12"> <span class="align-middle">Russian</span>
                    </a>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <?php switch(Session::get('lang')):
                        case ('ru'): ?>
                            <img src="<?php echo e(URL::asset('/assets/images/flags/russia.jpg')); ?>" alt="Header Language"
                                height="16"> <span class="align-middle"></span>
                        <?php break; ?>

                        <?php case ('it'): ?>
                            <img src="<?php echo e(URL::asset('/assets/images/flags/italy.jpg')); ?>" alt="Header Language"
                                height="16"> <span class="align-middle"></span>
                        <?php break; ?>

                        <?php case ('de'): ?>
                            <img src="<?php echo e(URL::asset('/assets/images/flags/germany.jpg')); ?>" alt="Header Language"
                                height="16"> <span class="align-middle"></span>
                        <?php break; ?>

                        <?php case ('es'): ?>
                            <img src="<?php echo e(URL::asset('/assets/images/flags/spain.jpg')); ?>" alt="Header Language"
                                height="16"> <span class="align-middle"></span>
                        <?php break; ?>

                        <?php default: ?>
                            <img src="<?php echo e(URL::asset('/assets/images/flags/us.jpg')); ?>" alt="Header Language" height="16">
                            <span class="align-middle"></span>
                    <?php endswitch; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end">

                    <!-- item-->
                    <a href="<?php echo e(url('index/en')); ?>" class="dropdown-item notify-item language" data-lang="eng">
                        <img src="<?php echo e(URL::asset('/assets/images/flags/us.jpg')); ?>" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">English</span>
                    </a>
                    <!-- item-->
                    <a href="<?php echo e(url('index/es')); ?>" class="dropdown-item notify-item language" data-lang="sp">
                        <img src="<?php echo e(URL::asset('/assets/images/flags/spain.jpg')); ?>" alt="user-image"
                            class="me-1" height="12"> <span class="align-middle">Spanish</span>
                    </a>

                    <!-- item-->
                    <a href="<?php echo e(url('index/de')); ?>" class="dropdown-item notify-item language" data-lang="gr">
                        <img src="<?php echo e(URL::asset('/assets/images/flags/germany.jpg')); ?>" alt="user-image"
                            class="me-1" height="12"> <span class="align-middle">German</span>
                    </a>

                    <!-- item-->
                    <a href="<?php echo e(url('index/it')); ?>" class="dropdown-item notify-item language" data-lang="it">
                        <img src="<?php echo e(URL::asset('/assets/images/flags/italy.jpg')); ?>" alt="user-image"
                            class="me-1" height="12"> <span class="align-middle">Italian</span>
                    </a>

                    <!-- item-->
                    <a href="<?php echo e(url('index/ru')); ?>" class="dropdown-item notify-item language" data-lang="ru">
                        <img src="<?php echo e(URL::asset('/assets/images/flags/russia.jpg')); ?>" alt="user-image"
                            class="me-1" height="12"> <span class="align-middle">Russian</span>
                    </a>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon" id="page-header-notifications-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="icon-sm" data-feather="bell"></i>
                    <span class="noti-dot bg-danger rounded-pill">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="m-0 font-size-15"> Notifications </h5>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0);" class="small"> Mark all as read</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 250px;">
                        <a href="" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start bg-light">
                                <div class="flex-shrink-0">
                                    <img src="<?php echo e(URL::asset('assets/images/users/avatar-3.jpg')); ?>"
                                        class="me-3 rounded-circle avatar-sm" alt="user-pic">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Justin Verduzco</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Your task changed an issue from "In Progress" to
                                            <span class="badge badge-soft-success">Review</span>
                                        </p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="bx bx-shopping-bag"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">New order has been placed</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Open the order confirmation or shipment
                                            confirmation.</p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 5 hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm me-3">
                                        <span
                                            class="avatar-title bg-soft-success text-success rounded-circle font-size-16">
                                            <i class="bx bx-cart"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Your item is shipped</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Here is somthing that you might light like to
                                            know.</p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 1 day ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start">
                                <div class="flex-shrink-0">
                                    <img src="<?php echo e(URL::asset('assets/images/users/avatar-4.jpg')); ?>"
                                        class="me-3 rounded-circle avatar-sm" alt="user-pic">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Salena Layfield</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Yay ! Everything worked!</p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 3 days ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 btn-block text-decoration-underline fw-bold text-center"
                            href="javascript:void(0)">
                            <span>View All <i class='bx bx-right-arrow-alt'></i></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item light-dark" id="mode-setting-btn">
                    <i data-feather="moon" class="icon-sm layout-mode-dark "></i>
                    <i data-feather="sun" class="icon-sm layout-mode-light"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="<?php echo e(asset('/assets/images/users/avatar-1.jpg')); ?>" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">Admin</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <a class="dropdown-item" href="contacts-profile"><i
                            class='bx bx-user-circle text-muted font-size-18 align-middle me-1'></i> <span
                            class="align-middle">My Account</span></a>
                    <a class="dropdown-item" href="apps-chat"><i
                            class='bx bx-chat text-muted font-size-18 align-middle me-1'></i> <span
                            class="align-middle">Chat</span></a>
                    <a class="dropdown-item" href="pages-faqs"><i
                            class='bx bx-buoy text-muted font-size-18 align-middle me-1'></i> <span
                            class="align-middle">Support</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item d-flex align-items-center" href="#"><i
                            class='bx bx-cog text-muted font-size-18 align-middle me-1'></i> <span
                            class="align-middle me-3">Settings</span><span
                            class="badge badge-soft-success ms-auto">New</span></a>
                    <a class="dropdown-item" href="auth-lock-screen"><i
                            class='bx bx-lock text-muted font-size-18 align-middle me-1'></i> <span
                            class="align-middle">Lock screen</span></a>
                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout"><?php echo app('translator')->get('translation.Logout'); ?></span></a>
                    <form id="logout_formasd" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/layouts/horizontal.blade.php ENDPATH**/ ?>