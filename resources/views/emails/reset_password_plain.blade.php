{{ trans('email.btn_change_password', [], $lang) }}

{{ config('app.url') }}/reset_password?token={{ $token }}
