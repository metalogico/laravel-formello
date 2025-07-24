{{-- Formello Scripts --}}
@php
    $scripts = \Metalogico\Formello\AssetManager::getScripts();
@endphp

@if(!empty($scripts))
    @foreach($scripts as $script)
        <script src="{{ asset('vendor/formello/js/' . $script) }}"></script>
    @endforeach
@endif

{{-- Always load formello.js --}}
<script src="{{ asset('vendor/formello/js/formello.js') }}"></script>
