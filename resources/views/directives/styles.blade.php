{{-- Formello Styles --}}
@php
    $styles = \Metalogico\Formello\AssetManager::getStyles();
@endphp

@if(!empty($styles))
    @foreach($styles as $style)
        <link rel="stylesheet" href="{{ asset('vendor/formello/css/' . $style) }}">
    @endforeach
@endif
