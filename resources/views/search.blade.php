@extends('layouts.app')

@section('content')
<div id="start-of-content" class="page-wrapper page-wrapper--search">

  <header class="search-header">
    <div class="search-header__inner search-header__inner-layout">
      <h1 class="search-header__heading heading heading--primary heading--search">
        Search results
      </h1>
      <div class="search-header__summary"><span>{{ $state['totals']['total'] . ' results' ?? '' }}</span></div>
    </div>
  </header>

  <div class="search-page__content">
    <nav class="search-page__sidebar">
      <div class="search__filters-overlay search__filters-overlay--active">
        <div class="search__filters">
          <div class="search-page__form">
            <form class="form__input-wrapper" action="/search" method="get">
              <input name="term" id="term" type="text" placeholder="Search" class="form__input form__input--light">
              <div class="form__submit-wrapper">
                <button class="form__submit button button--icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-labelledby="search-form" role="presentation" class="icon">
                    <title id="search-form" lang="en">Submit search</title>
                    <g fill="currentColor">
                      <path d="M15.89,14.77A8.9,8.9,0,0,0,18,9a9,9,0,1,0-9,9,8.89,8.89,0,0,0,4.6-1.28l6.73,6.73,2.12-2.12ZM2,9a7,7,0,1,1,7,7A7,7,0,0,1,2,9Z"></path>
                    </g>
                  </svg>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <div class="search-page__main">
      <div class="search__results">
        <div class="ui-grid">

          @foreach ($state['videos'] as $video)
          <div class="ui-card">

            @if(isset($video['asset_id']) && isset($video['title_slug']))
            <a href="/video/{{$video['asset_id']}}/{{$video['title_slug']}}">
              <div class="ui-card__thumbnail">
                <img src="/images/d/medium/{{$video['thumbnailId']}}.jpg" alt="" class="ui-card__thumbnail-image">
              </div>
              <h2 class="ui-card__title">
                <span>{{ $video['title'] }}</span>
              </h2>

              @isset($video['date_recorded'])
              <div class="ui-card__date">{{ date('M d, Y', strtotime($video['date_recorded'])) }}</div>
              @endisset

              @isset($video['duration'])
              <time class="ui-card__duration">{{ $video['duration']}}</time>
              @endisset
            </a>
            @endif
          </div>
          @endforeach

        </div>

        @isset($state['pagerLinks'])
        <div class="pagination">
          <ul class="pagination__list">
            @isset($state['pagerLinks']['previous'])
            <li><a href="{{$state['pagerLinks']['previous']}}">Prev</a></li>
            @endisset

            @isset($state['pagerLinks']['previous'])
            <li><a href="{{ Request::path() }}?{{$state['pagerLinks']['next']}}">Next</a></li>

            @endisset
          </ul>
        </div>
        @endisset
      </div>
    </div>
  </div>
</div>
@endsection
