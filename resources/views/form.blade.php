<form
    method="POST"
    action="{{ $formConfig['action'] ?? '' }}"
    @foreach ($formConfig['attributes'] ?? [] as $attr => $value)
        {{ $attr }}="{{ $value }}" @endforeach>

    @csrf
    @if (isset($formConfig['method']))
        @method($formConfig['method'])
    @endif

    @foreach ($formello->getFields() as $name => $field)
        {!! $formello->renderField($name) !!}
    @endforeach

    <div class="form-group mt-5 border-top pt-5">
        <button type="submit" class="btn btn-sm btn-primary">{{ $formConfig['submit_label'] ?? __('Save') }}</button>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary ms-2">{{ $formConfig['cancel_label'] ?? __('Cancel') }}</a>
    </div>

</form>
