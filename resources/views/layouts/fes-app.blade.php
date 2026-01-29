<!doctype html>
<html lang="it" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>FES Dashboard</title>
 
    <meta name="description" content="FES Dashboard">
    <meta name="robots" content="noindex, nofollow">

    <link rel="shortcut icon" href="{{ asset('backend/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('backend/favicons/favicon-192x192fesy.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/favicons/apple-touch-icon-180x180fesy.png') }}">


    <link rel="stylesheet" href="{{ asset('backend/js/plugins/sweetalert2/sweetalert2.min.css') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,400i,600,700">
    <link rel="stylesheet" id="css-main" href="{{ asset('backend/css/codebase.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/a-table.css') }}?v3">
    <link rel="stylesheet" href="{{ asset('backend/css/main.css') }}?v3">
    <link rel="stylesheet" href="{{ asset('backend/css/jquery-ui.css') }}">

    <link rel="stylesheet" href="{{ asset('backend/js/plugins/magnific-popup/magnific-popup.css') }}"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('header')

</head>
<body>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">

    <nav id="sidebar">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
            <div class="content-header content-header-fullrow px-15 fes-colr">
                <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                    <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                        <i class="fa fa-times text-danger"></i>
                    </button>

                    <div class="content-header-item">
                        <img id="fes-logo" src="{{ asset('backend/images/fes-logo.svg') }}" alt="fes sabbiature">
                    </div>
                </div>
            </div>


            <!-- Side Navigation -->
            <div class="content-side content-side-full">
                <ul class="nav-main">
                    @if(auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="si si-cup"></i><span class="sidebar-mini-hide">Dashboard</span></a>
                    </li>
                    @endif

                    <li @if(false !== strpos(url()->current(), 'building-sites')){!! 'class="open"' !!}@endif>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="fa fa-pencil"></i><span class="sidebar-mini-hide">Cantieri</span></a>
                        <ul>
                            @can('create', \App\BuildingSite::class)
                            <li>
                                <a class="active" href="{{ route('building-sites.create') }}">Aggiungi Cantiere</a>
                            </li>
                            @endcan
                            <li>
                                <a class="active" href="{{ route('building-sites.index') }}">Lista cantieri</a>
                            </li>
                        </ul>
                    </li>

                    @can('create', \App\User::class)
                    <li @if(false !== strpos(url()->current(), 'employees')){!! 'class="open"' !!}@endif>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="fa fa-users"></i><span class="sidebar-mini-hide">Dipendenti</span></a>
                        <ul>
                            @can('create', \App\User::class)
                            <li>
                                <a class="active" href="{{ route('employees.create') }}">Aggiungi dipendente</a>
                            </li>
                            @endcan
                            <li>
                                <a class="active" href="{{ route('employees.index') }}">Lista dipendenti</a>
                            </li>
                            <li>
                                <a class="active" href="{{ route('employees.sites-user') }}">Lista cantieri per dipendente</a>
                            </li>
                            <li>
                                <a class="active" href="{{ route('work_list') }}">Programma lavori dipendenti</a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can('create', \App\Machinery::class)
                    <li @if(false !== strpos(url()->current(), 'machinery')){!! 'class="open"' !!}@endif>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="fa fa-car"></i><span class="sidebar-mini-hide">Macchinari</span></a>
                        <ul>
                            <li>
                                <a class="active" href="{{ route('machinery.create') }}">Aggiungi macchinario</a>
                            </li>
                            <li>
                                <a class="active" href="{{ route('machinery.index') }}">Lista macchinari</a>
                            </li>
                        </ul>
                    </li>

                    <li @if(false !== strpos(url()->current(), 'tools')){!! 'class="open"' !!}@endif>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="fa fa-wrench"></i><span class="sidebar-mini-hide">Attrezzatura</span></a>
                        <ul>
                            <li>
                                <a class="active" href="{{ route('create_tool') }}">Aggiungi attrezzatura</a>
                            </li>
                            <li>
                                <a class="active" href="{{ route('tools_list') }}?type=tool">Lista attrezzature</a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can('create', \App\Customer::class)
                    <li @if(false !== strpos(url()->current(), 'customers')){!! 'class="open"' !!}@endif>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="fa fa-user-circle"></i><span class="sidebar-mini-hide">Clienti</span></a>
                        <ul>
                            <li>
                                <a class="active" href="{{ route('customers.create') }}">Aggiungi cliente</a>
                            </li>
                            <li>
                                <a class="active" href="{{ route('customers.index') }}">Lista clienti</a>
                            </li>
                        </ul>
                    </li>
                    @endcan
                    
                    @if(!auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('work_list_user') }}"><i class="si si-briefcase"></i><span class="sidebar-mini-hide">Lavori Programmati</span></a>
                    </li>
                    <li>
                        <a href="{{ route('report_list_user') }}"><i class="si si-envelope-open"></i><span class="sidebar-mini-hide">Elenco Rapportini</span></a>
                    </li>
                    @endif

                    <li>
                        <a href="" data-target="#search-modal" data-toggle="modal"><i class="si si-magnifier"></i><span class="sidebar-mini-hide">Ricerca </span></a>
                    </li>

                    <li>
                        <a href="{{ route('create_internal_report') }}"><i class="si si-home"></i><span class="sidebar-mini-hide">Capannone </span></a>
                    </li>

                    <li>
                        <a href="{{ route('user_logout') }}"><i class="si si-lock"></i><span class="sidebar-mini-hide">Disconnetti </span></a>
                    </li>
                </ul>
            </div>
            <!-- END Side Navigation -->
        </div>
        <!-- Sidebar Content -->
    </nav>
    <!-- END Sidebar -->

<!-- Header -->
    <header id="page-header">
        <div class="content-header">
            <div class="content-header-section">
                <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="sidebar_toggle">
                    <i class="fa fa-navicon"></i>
                </button>

                @if(!session()->has('dashboard_year'))
                    @if(auth()->user()->isAdmin())

                    <button onclick="toggleDropdown()" class="btn btn-dual-secondary"> <i class="fa fa-clock-o"></i> Apri/Chiudi Menù Anni Precedenti</button>

                    <div id="dropdownContent" style="display: none; position: absolute;">

                    <a href="{{ route('set_dashboard_year') }}" class="btn btn-dual-secondary"  style="display: block; padding: 8px 16px;">
                        <i class="fa fa-clock-o"></i> vai al {{ date('Y')-1 }}
                    </a>
                    <a href="{{ route('set_dashboard_year2') }}" class="btn btn-dual-secondary" style="display: block; padding: 8px 16px;">
                        <i class="fa fa-clock-o"></i> vai al {{ date('Y')-2 }}
                    </a>
                    <a href="{{ route('set_dashboard_year3') }}" class="btn btn-dual-secondary" style="display: block; padding: 8px 16px;">
                        <i class="fa fa-clock-o"></i> vai al {{ date('Y')-3 }}
                    </a>

                    </div>

                    <br><br>


                    @endif
                @else
                    <a href="{{ route('set_dashboard_year') }}" class="btn btn-primary">
                        <i class="fa fa-clock-o"></i> torna all'anno corrente
                    </a>
                @endif
            </div>

            <!-- Right Section -->
            <div class="content-header-section">

                <!-- Notifications -->
                {{--<div class="btn-group" role="group">--}}
                    {{--<button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                        {{--<i class="fa fa-bell"></i>--}}
                        {{--<span class="badge badge-primary badge-pill">5</span>--}}
                    {{--</button>--}}
                    {{--<div class="dropdown-menu dropdown-menu-right min-width-300" aria-labelledby="page-header-notifications">--}}
                        {{--<h5 class="h6 text-center py-10 mb-0 border-b text-uppercase">Notifications</h5>--}}
                        {{--<ul class="list-unstyled my-20">--}}
                            {{--<li>--}}
                                {{--<a class="text-body-color-dark media mb-15" href="javascript:void(0)">--}}
                                    {{--<div class="ml-5 mr-15">--}}
                                        {{--<i class="fa fa-fw fa-check text-success"></i>--}}
                                    {{--</div>--}}
                                    {{--<div class="media-body pr-10">--}}
                                        {{--<p class="mb-0">You’ve upgraded to a VIP account successfully!</p>--}}
                                        {{--<div class="text-muted font-size-sm font-italic">15 min ago</div>--}}
                                    {{--</div>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a class="text-body-color-dark media mb-15" href="javascript:void(0)">--}}
                                    {{--<div class="ml-5 mr-15">--}}
                                        {{--<i class="fa fa-fw fa-exclamation-triangle text-warning"></i>--}}
                                    {{--</div>--}}
                                    {{--<div class="media-body pr-10">--}}
                                        {{--<p class="mb-0">Please check your payment info since we can’t validate them!</p>--}}
                                        {{--<div class="text-muted font-size-sm font-italic">50 min ago</div>--}}
                                    {{--</div>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a class="text-body-color-dark media mb-15" href="javascript:void(0)">--}}
                                    {{--<div class="ml-5 mr-15">--}}
                                        {{--<i class="fa fa-fw fa-times text-danger"></i>--}}
                                    {{--</div>--}}
                                    {{--<div class="media-body pr-10">--}}
                                        {{--<p class="mb-0">Web server stopped responding and it was automatically restarted!</p>--}}
                                        {{--<div class="text-muted font-size-sm font-italic">4 hours ago</div>--}}
                                    {{--</div>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a class="text-body-color-dark media mb-15" href="javascript:void(0)">--}}
                                    {{--<div class="ml-5 mr-15">--}}
                                        {{--<i class="fa fa-fw fa-exclamation-triangle text-warning"></i>--}}
                                    {{--</div>--}}
                                    {{--<div class="media-body pr-10">--}}
                                        {{--<p class="mb-0">Please consider upgrading your plan. You are running out of space.</p>--}}
                                        {{--<div class="text-muted font-size-sm font-italic">16 hours ago</div>--}}
                                    {{--</div>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a class="text-body-color-dark media mb-15" href="javascript:void(0)">--}}
                                    {{--<div class="ml-5 mr-15">--}}
                                        {{--<i class="fa fa-fw fa-plus text-primary"></i>--}}
                                    {{--</div>--}}
                                    {{--<div class="media-body pr-10">--}}
                                        {{--<p class="mb-0">New purchases! +$250</p>--}}
                                        {{--<div class="text-muted font-size-sm font-italic">1 day ago</div>--}}
                                    {{--</div>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                        {{--<div class="dropdown-divider"></div>--}}
                        {{--<a class="dropdown-item text-center mb-0" href="javascript:void(0)">--}}
                            {{--<i class="fa fa-bell mr-5"></i> View All--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- END Notifications -->--}}

            </div>
            <!-- END Right Section -->
        </div>
        <!-- END Header Content -->

        <!-- Header Search -->
        <div id="page-header-search" class="overlay-header">
            <div class="content-header content-header-fullrow">
                <form action="be_pages_generic_search.html" method="post">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <!-- Close Search Section -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-secondary" data-toggle="layout" data-action="header_search_off">
                                <i class="fa fa-times"></i>
                            </button>
                            <!-- END Close Search Section -->
                        </div>
                        <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Header Search -->

        <!-- Header Loader -->
        <!-- Please check out the Activity page under Elements category to see examples of showing/hiding it -->
        <div id="page-header-loader" class="overlay-header bg-primary">
            <div class="content-header content-header-fullrow text-center">
                <div class="content-header-item">
                    <i class="fa fa-sun-o fa-spin text-white"></i>
                </div>
            </div>
        </div>
        <!-- END Header Loader -->
    </header>
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container">

        <!-- Page Content -->
        <div class="content">

            @yield('content')

        </div>
        <!-- END Page Content -->

    </main>
    <!-- END Main Container --> 

    <div class="modal fade" id="search-modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="GET" action="{{ route('search_results') }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Effettua una ricerca</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Inserisci una parola chiave da ricercare all'interno dell'area amministrativa</p>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="search-modal-text">Parola chiave</label>
                                <input type="text" id="search-modal-text" name="s"
                                       value="{{ $searchWord ?? '' }}"
                                       class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Avvia Ricerca</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="page-footer" class="opacity-0">
        <div class="content py-20 font-size-xs clearfix">
            <div class="float-left">
                <a class="font-w600" href="https://www.fes-sabbiature.it/" target="_blank">FES Servizi</a> &copy; <span class="js-year-copy">2021</span>
            </div>
        </div>
    </footer>
    <!-- END Footer -->
</div>
<!-- END Page Container -->

<!--
    Codebase JS Core

    Vital libraries and plugins used in all pages. You can choose to not include this file if you would like
    to handle those dependencies through webpack. Please check out assets/_es6/main/bootstrap.js for more info.

    If you like, you could also include them separately directly from the assets/js/core folder in the following
    order. That can come in handy if you would like to include a few of them (eg jQuery) from a CDN.

    assets/js/core/jquery.min.js
    assets/js/core/bootstrap.bundle.min.js
    assets/js/core/simplebar.min.js
    assets/js/core/jquery-scrollLock.min.js
    assets/js/core/jquery.appear.min.js
    assets/js/core/jquery.countTo.min.js
    assets/js/core/js.cookie.min.js
-->
<script src="{{ asset('backend/js/codebase.core.min.js') }}"></script>

<!--
    Codebase JS

    Custom functionality including Blocks/Layout API as well as other vital and optional helpers
    webpack is putting everything together at assets/_es6/main/app.js
-->
<script src="{{ asset('backend/js/codebase.app.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script src="{{ asset('backend/js/datepicker-it.js') }}"></script>
<script src="{{ asset('backend/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('backend/js/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script>jQuery(function(){Codebase.helpers('magnific-popup'); });</script>

<script>
    const uploadMediaApi = '{{ route('storemedia') }}';
    const uploadFilesApi = '{{ route('storefile') }}';
    const mediaOrderingUrl = '{{ route('order_media') }}';

    // launch the toast notification if any
    $(document).ready(function(){
        @if(session()->has('toast'))
        Swal.fire({
            text: "{!! session()->get('toast') !!}",
            type: '@if(session()->has('toast-class')){{ session()->get('toast-class') }}@else{{ 'success' }}@endif',
            confirmButtonText: 'Chiudi',
        });
        @endif
    });

    // Implement image sorting
    if (undefined !== typeof mediaOrderingUrl && undefined !== typeof sortable) {
        $('.sortable').sortable({
            update: function (event, ui) {
                var productImages = $(".media-image").map(function () {
                    return $(this).data("media-id");
                }).toArray();

                $.ajaxSetup({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                });

                $.ajax({
                    url: mediaOrderingUrl,
                    type: 'POST',
                    data: {
                        'media_ids': productImages
                    }
                });
            }
        });
    }


    // Trigger delete confirm function and delete process
    $(".delete-media").click(function(e){
        e.preventDefault();
        var deleteBtn = $(this);
        var elementText = ($(this).data('element-name') ? $(this).data('element-name') : 'immagine');
        var elementType = ($(this).data('element-type') ? $(this).data('element-type') : 'questa');

        if (confirm("Sei sicuro di voler eliminare " + elementType + " " + elementText + "? l'azione non può essere annullata") && $(this).data('url')) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'DELETE',
                url: $(this).data('url'),
                success: function () {
                    deleteBtn.closest('.media-item').hide('slow');
                },
            });
        }
    });

    function getLocation() {
        if (navigator.geolocation) {
            console.log('requesting position');
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            console.log("Geolocation is not supported by this browser.");

            if ($('#geo-location-error')) {
                $('#geo-location-error').show();
            }
        }
    }

    function showPosition(position) {
        $('#location_lat').val(position.coords.latitude);
        $('#location_lng').val(position.coords.longitude);
    }

    //funzione per bottone anni precedenti
    function toggleDropdown() {
        var dropdownContent = document.getElementById('dropdownContent');
        dropdownContent.style.display = (dropdownContent.style.display === 'block') ? 'none' : 'block';

        var monthsId = document.getElementById('mesi');
        monthsId.style.display = (monthsId.style.display === 'none') ? 'flex' : 'none';

        var progresso = document.getElementById('progresso');
        progresso.style.display = (progresso.style.display === 'none') ? 'flex' : 'none';

        var span = document.getElementById('span');
        span.style.display = (span.style.display === 'none') ? 'flex' : 'none';

        var meter = document.getElementById('meter');
        meter.style.display = (meter.style.display === 'none') ? 'flex' : 'none';
    }


</script>

@yield('footer')

</body>
</html>