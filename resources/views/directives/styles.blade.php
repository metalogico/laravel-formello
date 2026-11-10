{{-- Formello Styles --}}
@foreach(\Metalogico\Formello\AssetManager::getStyles() as $style)
    <link rel="stylesheet" href="{{ asset('vendor/formello/css/' . $style) }}">
@endforeach
