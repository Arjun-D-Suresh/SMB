<?php $__env->startComponent('mail::message'); ?>
<center>
    <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
        <img style="height: 60px;" src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
    </a>
</center>

File processed for the company <h1><?php echo e($comapnyname); ?></h1> with the devidend amount <p><?php echo e($dividendamount); ?></p>.

<!-- <?php $__env->startComponent('mail::button', ['url' => '']); ?>
Button Text
<?php echo $__env->renderComponent(); ?> -->


Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?><?php /**PATH C:\jiyuuSEVEN\GitHub\IEPF\resources\views\emails\process.blade.php ENDPATH**/ ?>