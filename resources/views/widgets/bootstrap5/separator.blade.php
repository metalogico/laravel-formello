<div class="form-group mb-3 mt-3"
    @foreach (($config['attributes'] ?? []) as $attr => $value)
        {{ $attr }}="{{ $value }}" @endforeach>

    @if (!empty($label))
        <h5 class="mb-2 text-muted">{{ $label }}</h5>
    @endif

    <hr @foreach (($config['hr'] ?? []) as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach>
</div>
