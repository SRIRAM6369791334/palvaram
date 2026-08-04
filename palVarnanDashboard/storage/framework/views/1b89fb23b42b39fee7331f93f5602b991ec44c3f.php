<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8" />
    <title> <?php echo $__env->yieldContent('title'); ?>  </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Yesbe Dashboard" name="description" />
    <meta content="Yesbe" name="author" />
    <link rel="shortcut icon" href="<?php echo e(URL::asset('assets/images/logoooo.png')); ?>">
    <?php echo $__env->make('layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<?php $__env->startSection('body'); ?>
    <?php echo $__env->make('layouts.body', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldSection(); ?>
<?php echo $__env->yieldContent('content'); ?>
<?php echo $__env->make('layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/layouts/master-without_nav.blade.php ENDPATH**/ ?>