@extends('layouts.app')

@section('content')
<div id="start-of-content" class="page-wrapper page-wrapper--full">
  <svg viewBox="0 0 800 217" version="1.1" preserveAspectRatio="xMidYMid meet">
    <rect clip-path="url(#2px9eixpmrz)" x="0" y="0" width="800" height="250" style="fill: url(&quot;#0hia3u67x8c9&quot;);"></rect>
    <defs>
      <clipPath id="2px9eixpmrz">
        <rect x="425" y="3" rx="2" ry="2" width="361" height="26"></rect>
        <rect x="425" y="44" rx="2" ry="2" width="361" height="26"></rect>
        <rect x="6" y="2" rx="2" ry="2" width="400" height="192"></rect>
        <rect x="425" y="83" rx="2" ry="2" width="361" height="26"></rect>
        <rect x="425" y="124" rx="2" ry="2" width="361" height="26"></rect>
      </clipPath>
      <linearGradient id="0hia3u67x8c9">
        <stop offset="0%" stop-color="#c6c6c6" stop-opacity="1"></stop>
        <stop offset="50%" stop-color="#c6c6c6" stop-opacity="1"></stop>
        <stop offset="100%" stop-color="#c6c6c6" stop-opacity="1"></stop>
      </linearGradient>
    </defs>
  </svg>
  @foreach ($state['videos'] as $category)
  <h2 class="carousel__title">
    <a href="/search?topics={{ $category['id'] }}">{{ $category['label'] }}</a>
  </h2>
  <div class="carousel-wrapper carousel-wrapper--static">
    @foreach ($category['hits'] as $video)
    <div class="ui-card">

      @if(isset($video['asset_id']) && isset($video['title_slug']))
      <a href="/video/{{$video['asset_id']}}/{{$video['title_slug']}}">

        <div class="ui-card__thumbnail">

        @isset($video['thumbnailId'])
          <img src="/images/d/medium/{{$video['thumbnailId']}}.jpg" alt="" class="ui-card__thumbnail-image">
        @endisset
        </div>

        <h2 class="ui-card__title">
        @isset($video['title'])
          <span>{{ $video['title'] }}</span>
        @endisset
        </h2>

        @isset($video['description'])
        <p class="ui-card__description">{{ html_entity_decode(strip_tags(Str::of($video['description'])->limit(180))) }}</p>
        @endisset

        @isset($video['duration'])
          <time class="ui-card__duration">{{ $video['duration']}}</time>
        @endisset
      </a>
      @endif
    </div>
    @endforeach
  </div>
  @endforeach
</div>
@endsection
