@props([
    'value' => '', 'name', 'id' => null
])
<textarea
    {{-- @class(['form-control', 'is-invalid' => $errors->has('name')])  --}}
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    {{ $attributes->merge([])
        ->class(['form-control', 'is-invalid' => $errors->has($name)])
    }}
> {{ old($name, $value) }} </textarea>

