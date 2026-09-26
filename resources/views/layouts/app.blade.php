<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="{{ asset('sb-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href="{{ asset('sb-admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    </head>

    <body id="page-top">
        <div id="wrapper">
            @include('layouts.sidebar')


            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    @include('layouts.navigation', ['topbarTitle' => $topbarTitle ?? 'Default Dashboard Title'])
                    
                    <!-- Page Content -->
                    {{ $slot }}

                </div>
                
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Your Website 2021</span>
                        </div>
                    </div>
                </footer>

            </div>

        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="btn btn-primary" 
                                        :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                >
                                    Logout
                                </a>
                            </form>
                            
                    </div>
                </div>
            </div>
        </div>
        
        <!-- JS Links before </body> -->
        <script src="{{ asset('sb-admin/vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('sb-admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{ asset('sb-admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{ asset('sb-admin/js/sb-admin-2.min.js') }}"></script>

        <!-- Page level plugins -->
        <script src="{{ asset('sb-admin/vendor/chart.js/Chart.min.js') }}"></script>

        <!-- Page level custom scripts -->
        <script src="{{ asset('sb-admin/js/demo/chart-area-demo.js') }}"></script>
        <script src="{{ asset('sb-admin/js/demo/chart-pie-demo.js') }}"></script>
    </body>
</html>
