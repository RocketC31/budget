@extends('layout')

@section('tailwind', true)

@section('title', __('auth.login'))

@section('body')
    <div class="max-w-sm mx-auto my-12">
        <img class="h-12 mx-auto mb-8" src="/logo.svg" />
        @if (session('alert_type') && session('alert_message'))
            @include('partials.alerts.' . session('alert_type'), ['payload' => ['classes' => 'mb-4', 'message' => session('alert_message')]])
        @endif
        <div class="p-5 bg-white border rounded-md">
            <form method="POST">
                {{ csrf_field() }}
                <div class="mb-5">
                    <label class="block mb-1 text-sm text-gray-700">{{ __('fields.email') }}</label>
                    <input class="w-full px-3 py-2 text-sm border rounded-md" type="email" name="email" value="{{ old('email') }}" autofocus />
                </div>
                <div class="mb-5">
                    <label class="block mb-1 text-sm text-gray-700">{{ __('fields.password') }}</label>
                    <input class="w-full px-3 py-2 text-sm border rounded-md" type="password" name="password" />
                    <div class="mt-1 text-right">
                        <a href="{{ route('reset_password') }}" class="text-sm transition text-primary-regular hover:text-primary-dark">{{ __('auth.forgot_your_password') }}</a>
                    </div>
                </div>
                <button class="w-full py-2.5 hover:bg-primary-dark transition text-sm bg-primary-regular text-white rounded-md">{{ __('auth.login') }}</button>
            </form>
        </div>
        <div class="mt-4 text-center">
            <a class="text-sm transition text-primary-regular hover:text-primary-dark" href="{{ route('register') }}">{{ __('auth.first_time_here') }} {{ __('auth.register') }}.</a>
        </div>
    </div>
@endsection
