{!! nl2br(trans('email.welcome', ['name' => $name ], $lang)) !!}

{{ trans('email.use_link_below', [], $lang) }}

{{ config('app.url') . '/verify/' . $verification_token }}
