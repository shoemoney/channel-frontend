@extends('layouts.app')

@section('content')
  <video-embed :video="{{ json_encode($video) }}"></video-embed>
@endsection
