<nav class="previous-next" aria-label="Artículos anterior y siguiente">
    @if($previous)
        <a href="{{ $urls->article($previous->id) }}"><span class="pager-label"><span aria-hidden="true">←</span> Anterior</span><span>{{ $previous->title }}</span></a>
    @else
        <span class="pager-placeholder"></span>
    @endif
    @if($next)
        <a class="next" href="{{ $urls->article($next->id) }}"><span class="pager-label">Siguiente <span aria-hidden="true">→</span></span><span>{{ $next->title }}</span></a>
    @endif
</nav>
