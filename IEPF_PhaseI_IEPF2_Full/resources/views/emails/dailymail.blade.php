@component('mail::message')
<center>
    <a class="navbar-brand center" href="{{ url('/') }}">
        <img style="height:60px" src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
    </a>
</center>




@component('mail::table')

<?php
echo ($message['message']);
?>
@endcomponent

@endcomponent