@props(['sticky' => true])

<div class="sidebar-modern {{ $sticky ? 'sticky-sidebar' : '' }}">
    {{ $slot }}
</div>
