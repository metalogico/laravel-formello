<form method="POST" action="{{ $formConfig['action'] ?? '' }}"
    @foreach ($formConfig['attributes'] ?? [] as $attr => $value)
        {{ $attr }}="{{ $value }}" @endforeach>

    @csrf
    @if (isset($formConfig['method']))
        @method($formConfig['method'])
    @endif

    @foreach ($formello->getFields() as $name => $field)
        {!! $formello->renderField($name) !!}
    @endforeach

    <div class="form-group mt-2">
        <button type="submit" class="btn btn-primary">{{ $formConfig['submit_label'] ?? 'Save' }}</button>
    </div>

</form>
