@extends('emails.template')

@section('content')
    {{ trans('email.invited_to_space', ['name' => $invite->inviter->name, 'space' => $invite->space->name]) }}

    <a class="button" href="{{ route('space_invites.show', ['space' => $invite->space->id, 'invite' => $invite->id]) }}">{{ trans('email.check_invite') }}</a>
@endsection
