<div class="form-group mb-3">
    @if (isset($config['label']))
        <label class="form-label" for="{{ $config['attributes']['id'] }}">{{ $config['label'] }}</label>
    @endif
    <div class="form-check form-switch">
        <input type="hidden" name="{{ $name }}" value="0"> <!-- for the unchecked value -->
        <input name="{{ $name }}" value="1"
            @foreach ($config['attributes'] as $attr => $attrValue)
                {{ $attr }}="{{ $attrValue }}" @endforeach>
    </div>
    @if (isset($config['help']))
        <div class="form-text">{!! $config['help'] !!}</div>
    @endif

    @if ($errors)
        <div class="invalid-feedback">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
</div>
