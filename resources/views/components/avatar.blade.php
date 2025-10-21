@props(['src', 'name', 'size' => 'md'])

@php
$sizes = [
    'xs' => 'w-6 h-6 text-xs',
    'sm' => 'w-8 h-8 text-sm',
    'md' => 'w-10 h-10 text-base',
    'lg' => 'w-12 h-12 text-lg',
    'xl' => 'w-16 h-16 text-xl',
    '2xl' => 'w-20 h-20 text-2xl',
    '3xl' => 'w-24 h-24 text-3xl',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => "relative inline-block $sizeClass"]) }}>
    <img src="{{ $src }}" 
         alt="{{ $name }}" 
         class="rounded-full object-cover w-full h-full ring-2 ring-vm-gold shadow-lg"
         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&color=0A1647&background=D4AF37&bold=true&size=200'">
    
    @if(isset($online) && $online)
        <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
    @endif
</div>

