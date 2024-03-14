<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <!-- <script type="text/javascript" src="date.js"></script> -->
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">

    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js" defer></script>

    <!-- Styles -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <style>
        .navi-font {
            /* font-size: 12px; */
            color: black;
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                    <img style="height: 100%;" src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
                    <!-- <?php echo e(config('app.name', 'Laravel')); ?> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo e(__('Toggle navigation')); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>







                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        <?php if(auth()->guard()->guest()): ?>
                        <?php if(Route::has('login')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
                        </li>
                        <?php endif; ?>

                        <?php if(Route::has('register')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('register')); ?>"><?php echo e(__('Register')); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link navi-font" href="/search">Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navi-font" href="/home">File Upload</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link navi-font" href="/multidiv">Mutiple Dividend</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/allfiles">Bulk Upload</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="/uploaded-details">Verification Excel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/folio-header-deatails">Folio Details</a>
                        </li>
                        <li class="nav-item navi-font dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <?php echo e(Auth::user()->name); ?>

                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    <?php echo e(__('Logout')); ?>

                                </a>

                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                </form>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Scripts -->
    <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>
    <script src="<?php echo e(asset('js/custom.js')); ?>" defer></script>

    <script>
        jQuery.ajax({
            url: "<?php echo e(url('/getcompany')); ?>",
            method: 'get',
            data: {
                c_fullname: jQuery('#c_fullname').val(),
                cin: jQuery('#cin').val(),
                dividend_amount: jQuery('#dividend_amount').val()
            },
            success: function(result) {
                jQuery('.alert').show();
                jQuery('.alert').html(result.success);
            }
        });
    </script>
</body>

</html>
<?php /**PATH C:\jiyuuSEVEN\GitHub\IEPF\resources\views\layouts\app.blade.php ENDPATH**/ ?>