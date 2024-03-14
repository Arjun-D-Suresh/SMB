@component('mail::message')
<center>
    <a class="navbar-brand" href="{{ url('/') }}">
        <img style="height: 60px; " src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
    </a>
</center>

{{$filename}} File uploaded. With the message {{$message}}.

@endcomponent