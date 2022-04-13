{{ trans('email.invited_to_space', ['name' => $invite->inviter->name, 'space' => $invite->space->name]) }}

{{ trans('email.use_link_below') }}

{{ route('space_invites.show', ['space' => $invite->space->id, 'invite' => $invite->id]) }}
