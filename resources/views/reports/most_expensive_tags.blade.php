@extends('layout')

@section('body')
    <div class="wrapper my-3">
        <h2>{{ __('activities.tag.most_expensive') }}</h2>
        @include('partials.tags_price', ['tagsPrice' => $mostExpensiveTags])
    </div>
@endsection
