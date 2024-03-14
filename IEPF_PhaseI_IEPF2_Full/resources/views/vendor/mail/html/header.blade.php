<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <!-- <img src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg" class="logo" alt="Laravel Logo"> -->
            <a class="logo" href="{{ url('/') }}">
                <img style="height:50px;" src="https://www.thegoldenegg.in/wp-content/uploads/2018/04/gegg-logo.jpg"><img>
            </a>
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>