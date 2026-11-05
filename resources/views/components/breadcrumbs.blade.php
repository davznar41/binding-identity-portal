<nav class="breadcrumbs" aria-label="Migas de pan">
    <ol>
        <li><a href="{{ $urls->index() }}">Documentación</a></li>
        @if($parent)<li><a href="{{ $urls->article($parent->id) }}">{{ $parent->title }}</a></li>@endif
        <li><span>{{ $title }}</span></li>
    </ol>
</nav>
