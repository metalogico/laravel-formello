<div class="my-4"
    @foreach (($config['attributes'] ?? []) as $attr => $value)
        {{ $attr }}="{{ $value }}" @endforeach>

    @if (!empty($label))
        <h5 class="mb-2 text-sm font-medium text-gray-500">{{ $label }}</h5>
    @endif

    <hr class="border-gray-300" @foreach (($config['hr'] ?? []) as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach>
</div>
