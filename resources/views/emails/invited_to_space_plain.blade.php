{{ trans('email.invited_to_space', ['name' => $invite->inviter->name, 'space' => $invite->space->name], $lang) }}

{{ trans('email.use_link_below', [], $lang) }}

{{ route('space_invites.show', ['space' => $invite->space->id, 'invite' => $invite->id]) }}
