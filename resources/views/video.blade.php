@extends('layouts.app')

@section('content')

<div id="start-of-content" class="page-wrapper page--video">

  <header class="video-meta video-meta__header">
    <h1 class="heading heading--primary video-meta__title">
      {{ $title }}
    </h1>
    <div class="video-meta__date">
      {{ $date }}
    </div>
  </header>

  <div class="panels">
    <div class="panel--left">
      <div class="video-player__wrapper">
          <div class="video-player__container">
            <div id="video" class="video-player" label="Video Player" style="gap:24px;max-width:66vw;margin:0 auto;display:flex;flex-direction:column;align-items:flex-start;justify-content:center;">
              <video
                style="width: 100%;height: 100%;height:auto;"
                poster="{{ $thumbnailUrl }}"
                src="{{ $src }}"
                playsinline
                controls>
                <track kind="captions" label="English" srclang="en" src="{{ $trackUrl }}">
              </video>
          </div>
        </div>
      </div>
    </div>
    <div class="panel--right">
      <div class="tabs vp__tabs">
        <ul class="nav nav-tabs">
          <li role="presentation" class="nav-item nav-link active">
            <h3 class="vp__tabs__label">
              Info
            </h3>
          </li>
        </ul>
      </div>
      <div class="tab-content">
        <div class="video-meta">
          <div class="video-meta__inner">
            <div class="video-meta__description">{!! $description !!}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
