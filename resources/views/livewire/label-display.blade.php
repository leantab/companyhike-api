@foreach($chunk as $name => $val)
    <div class="col-{{ $col }}">
        <span><strong>{{ $name }}</strong></span>
        @if(is_array($val))
            <p class="text-muted"><pre>{{ json_encode($val, JSON_PRETTY_PRINT)}}</pre></p>
        @else
            <p class="text-muted">
                @if(isset($variables[$name]))
                    @if($variables[$name] == 'money')
                        $ {{ number_format((float) $val, 0, ',', '.') }}
                    @elseif($variables[$name] == 'percentage')
                        {{ round((float) $val, 2) }} %
                    @elseif($variables[$name] == 'units')
                        {{ round((float) $val, 2) }} u
                    @elseif($variables[$name] == 'number')
                        {{ round((float) $val, 2) }}
                    @elseif($variables[$name] == 'ratio')
                        {{ round((float) $val, 3) }}
                    @endif
                @else
                    {{ $val }}
                @endif
            </p>
        @endif

    </div>
@endforeach
