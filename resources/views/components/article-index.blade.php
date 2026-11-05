<nav class="article-index" aria-label="Índice de documentación">
    <ul class="index-list">
    @foreach($navigation as $node)
        <li @class(['index-group' => $node['children'] !== []])>
            <a class="index-item" href="{{ $urls->article($node['id']) }}">
                @include('components.icon', ['id' => $node['id']])<span>{{ $node['title'] }}</span>
            </a>
            @if($node['children'] !== [])
                <ul class="index-children">
                @foreach($node['children'] as $child)
                    <li><a href="{{ $urls->article($child['id']) }}">@include('components.icon', ['id' => $child['id']])<span>{{ $child['title'] }}</span></a></li>
                @endforeach
                </ul>
            @endif
        </li>
    @endforeach
    </ul>
</nav>
