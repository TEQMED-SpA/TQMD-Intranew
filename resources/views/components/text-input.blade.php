@props([
    'disabled' => false,
    'type' => 'text',
])

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 ' .
        'focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm',
]) !!}>
