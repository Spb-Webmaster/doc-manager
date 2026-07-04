@props([
    'options'     => [],   // ['value' => 'label', ...]
    'selected'    => null,
    'placeholder' => null,
    'compact'     => false,
])

<div data-mz-select class="mz-select{{ $compact ? ' mz-select--compact' : '' }}">
    <select {{ $attributes }}>
        @if($placeholder !== null)
        <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $label)
        <option value="{{ $value }}" @selected($selected !== null && $selected == $value)>{{ $label }}</option>
        @endforeach
        {{ $slot }}
    </select>
</div>
