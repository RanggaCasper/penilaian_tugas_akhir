@props(['active' => false, 'collapsible' => false, 'icon' => null, 'title', 'href' => '#', 'id' => null])

@php
    $classes = $active ? 'nav-link menu-link active' : 'nav-link menu-link';
@endphp

<li class="nav-item">
    <a class="{{ $classes }}" href="{{ $href }}" aria-expanded="false">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $title }}</span>
    </a>
</li>
