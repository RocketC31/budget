{{ trans('email.btn_change_password') }}

{{ config('app.url') }}/reset_password?token={{ $token }}
