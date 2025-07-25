@props(['url' => config('app.url')])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <link rel="icon" href="/favicon.svg" type="image/svg+xml">
            @else
                {!! $slot !!}
            @endif
        </a>
    </td>
</tr>
