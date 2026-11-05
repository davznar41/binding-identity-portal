@php($childActive = str_starts_with($currentId ?? '', 'authority-reporting/'))
<aside class="sidebar">
    <button class="mobile-toggle" type="button" aria-expanded="true" aria-controls="documentation-navigation" hidden>
        <span><span class="menu-symbol" aria-hidden="true">☰</span> Explorar documentación</span>
        <span class="chevron" aria-hidden="true">⌄</span>
    </button>
    <nav id="documentation-navigation" aria-label="Documentación de funcionalidades">
        <ul class="nav-list">
        @foreach($navigation as $node)
            <li @class(['has-children' => $node['children'] !== [], 'section-active' => $node['children'] !== [] && $childActive])>
                <div class="nav-row">
                    <a href="{{ $urls->article($node['id']) }}" @if($currentId === $node['id']) aria-current="page" @endif>
                        @include('components.icon', ['id' => $node['id']])
                        <span>{{ $node['title'] }}</span>
                    </a>
                    @if($node['children'] !== [])
                        <button class="group-toggle" type="button" aria-expanded="true" aria-controls="authority-children" data-default-expanded="{{ $childActive ? 'true' : 'false' }}" aria-label="Subapartados de Bi Authority Reporting" hidden>
                            <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8"></path></svg>
                        </button>
                    @endif
                </div>
                @if($node['children'] !== [])
                    <ul id="authority-children" class="child-list">
                    @foreach($node['children'] as $child)
                        <li><a href="{{ $urls->article($child['id']) }}" @if($currentId === $child['id']) aria-current="page" @endif>@include('components.icon', ['id' => $child['id']])<span>{{ $child['title'] }}</span></a></li>
                    @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
        </ul>
    </nav>
</aside>
