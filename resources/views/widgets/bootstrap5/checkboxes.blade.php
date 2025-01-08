<div class="form-group">
    <!-- Display the main label for the checkbox list -->
    @if(!empty($attributes['label']))
        <label class="form-label">{{ $attributes['label'] }}</label>
    @endif

    @foreach($checkboxes as $checkbox)
        <div class="form-check">
            <!-- Render the checkbox input -->
            <input
                type="checkbox"
                name="{{ $checkbox['name'] }}"
                value="{{ $checkbox['value'] }}"
                class="form-check-input {{ $attributes['class'] ?? '' }}"
                id="{{ $checkbox['value'] }}"
                {{ $checkbox['checked'] ? 'checked' : '' }}
            >
            <!-- Render the label for the individual checkbox -->
            <label class="form-check-label" for="{{ $checkbox['value'] }}">
                {{ $checkbox['label'] }}
            </label>
        </div>
    @endforeach

    <!-- Display error messages, if any -->
    @if($errors)
        <div class="text-danger">
            @foreach($errors as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
</div>
