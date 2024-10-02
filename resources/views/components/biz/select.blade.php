@props(['options' => [], 'selected' => ''])

<select {!! $attributes->merge([
    'class' =>
        'text-gray-900 py-1.5  border-gray-300 bg-gray-50 focus:border-blue-500 focus:ring-blue-500 rounded-md placeholder:text-gray-400 sm:text-sm sm:leading-6',
]) !!}>
    {{ $slot }}
    @foreach ($options as $option)
        <option value="{{ $option['value'] }}" {{ $option['value'] == $selected ? 'selected' : '' }}>
            {{ $option['label'] }}</option>
    @endforeach
</select>
