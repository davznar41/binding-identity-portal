@extends('layouts.documentation')
@section('content')
    @include('components.breadcrumbs')
    <article class="documentation-article">
        <h1>{{ $article->title }}</h1>
        <div class="article-rule" aria-hidden="true"></div>
        <div class="prose">{!! $article->html !!}</div>
        @if($article->example !== null)
            <aside class="example"><h2>Ejemplo</h2><p>{{ $article->example }}</p></aside>
        @endif
        @if($children !== [])
            <section class="related"><h2>En este apartado</h2><ul>
            @foreach($children as $child)
                <li><a href="{{ $urls->article($child['id']) }}"><span>{{ $child['title'] }}</span><span aria-hidden="true">→</span></a></li>
            @endforeach
            </ul></section>
        @endif
    </article>
    @include('components.previous-next')
@endsection
