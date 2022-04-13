{!! nl2br(trans('email.welcome', ['name' => $name ])) !!}

{{ trans('email.use_link_below') }}

{{ config('app.url') . '/verify/' . $verification_token }}
