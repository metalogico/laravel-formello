{{-- Formello Scripts --}}
@foreach(\Metalogico\Formello\AssetManager::getScripts() as $script)
    <script src="{{ asset('vendor/formello/js/' . $script) }}"></script>
@endforeach

{{-- Always load formello.js --}}
<script src="{{ asset('vendor/formello/js/formello.js') }}"></script>

{{-- Reactive engine (loaded after formello.js so widgets are initialized first) --}}
<script src="{{ asset('vendor/formello/js/formello-reactive.js') }}"></script>
