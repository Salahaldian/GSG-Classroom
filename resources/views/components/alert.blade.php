@props([
    'name'
])
@if (session()->has($name))
    <div {{ $attributes->class(['alert'])
        ->merge([
            'id' => 'alert'
        ])
    }} >
        {{ session($name) }}
    </div>
@endif
