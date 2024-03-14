@component('mail::message')
<center>
    <a class="navbar-brand" href="{{ url('/') }}">
        <img style="height: 60px;" src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
    </a>
</center>

File processed for the company <h1>{{ $comapnyname }}</h1> with the devidend amount <p>{{ $dividendamount }}</p>.

<!-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent -->


Thanks,<br>
{{ config('app.name') }}
@endcomponent