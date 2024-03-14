<?php $__env->startComponent('mail::message'); ?>
<center>
    <a class="navbar-brand center" href="<?php echo e(url('/')); ?>">
        <img style="height:60px" src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
    </a>
</center>




<?php $__env->startComponent('mail::table'); ?>

<?php
echo ($message['message']);
?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->renderComponent(); ?><?php /**PATH C:\jiyuuSEVEN\GitHub\IEPF\resources\views\emails\dailymail.blade.php ENDPATH**/ ?>