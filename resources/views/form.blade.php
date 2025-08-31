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
    @if ($formello->getCssFramework() === 'bootstrap5')
        <div class="row">
            @foreach ($formello->getFields() as $name => $field)
                <div class="col-md-{{ $field['config']['columns'] ?? 12 }} mb-3">
                    {!! $formello->renderField($name) !!}
                </div>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-12 gap-3">
            @foreach ($formello->getFields() as $name => $field)
                <div class="col-span-{{ $field['config']['columns'] ?? 12 }} mb-3">
                    {!! $formello->renderField($name) !!}
                </div>
            @endforeach
        </div>
    @endif

    @if ($formello->getCssFramework() === 'bootstrap5')
        <div class="form-group mt-5 border-top pt-5">
            <button type="submit" class="btn btn-sm btn-primary">{{ $formConfig['submit_label'] ?? __('Save') }}</button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary ms-2">{{ $formConfig['cancel_label'] ?? __('Cancel') }}</a>
        </div>
    @else
        <div class="mt-5 border-t pt-5">
            <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded bg-blue-600 text-white hover:bg-blue-700">
                {{ $formConfig['submit_label'] ?? __('Save') }}
            </button>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200 ml-2">
                {{ $formConfig['cancel_label'] ?? __('Cancel') }}
            </a>
        </div>
    @endif

</form>
