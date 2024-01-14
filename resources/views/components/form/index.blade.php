<form {{ $attributes->merge([
    'class' => 'flex flex-col gap-4',
]) }}>
    {{ $slot }}
</form>
