@extends('emails.template')

@section('content')
    {!! nl2br(trans('email.welcome', ['name' => $name ], $lang))  !!}

    <a class="button" href="{{ config('app.url') . '/verify/' . $verification_token }}">{{ trans('email.verify', [], $lang) }}</a>
@endsection
