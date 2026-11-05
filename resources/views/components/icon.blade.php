@php($icon = $urls->icon($id))
@if($icon)
    <img class="documentation-icon" src="{{ $icon }}" width="24" height="24" alt="" aria-hidden="true">
@endif
