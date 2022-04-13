@extends('emails.template')

@section('content')
    {{ trans('email.password_changed', ['at' => $updated_at]) }}
@endsection
