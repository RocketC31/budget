@extends('emails.template')

@section('content')
    <a href="{{ config('app.url') }}/reset_password?token={{ $token }}">{{ trans('email.btn_change_password') }}</a>
@endsection
