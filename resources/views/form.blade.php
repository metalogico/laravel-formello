<form
    method="POST"
    action="{{ $formConfig['action'] ?? '' }}"
    @foreach ($formConfig['attributes'] ?? [] as $attr => $value)
        {{ $attr }}="{{ $value }}" @endforeach>

    @csrf
    @if (isset($formConfig['method']))
        @method($formConfig['method'])
    @endif

    {{-- Grid container per columnSpan --}}
    <div class="row">
        @foreach ($formello->getFields() as $name => $field)
            <div class="col-md-{{ $field['config']['columns'] ?? 12 }} mb-3">
                {!! $formello->renderField($name) !!}
            </div>
        @endforeach
    </div>

    <div class="form-group mt-5 border-top pt-5">
        <button type="submit" class="btn btn-sm btn-primary">{{ $formConfig['submit_label'] ?? __('Save') }}</button>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary ms-2">{{ $formConfig['cancel_label'] ?? __('Cancel') }}</a>
    </div>

</form>

@stack('formello-scripts')
<script src="{{ asset('vendor/formello/js/formello.js') }}"></script>