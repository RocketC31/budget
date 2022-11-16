<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Mail\PasswordChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Facades\Image;

class SettingsController extends Controller
{
    public function getIndex(): RedirectResponse
    {
        return redirect()->route('settings.profile');
    }

    public function getProfile(): Response
    {
        return Inertia::render('Settings/Profile');
    }

    public function postProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif',
        ]);

        $user = Auth::user();
        // Profile
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $fileName = $file->hashName();

            $image = Image::make($file)
                ->fit(500);

            Storage::put('public/avatars/' . $fileName, (string) $image->encode());

            $user->avatar = $fileName;
        }

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        $user->save();

        return back();
    }

    public function getAccount(): Response
    {
        return Inertia::render('Settings/Account');
    }

    public function postAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'nullable|confirmed',
            'email' => 'required|email|unique:users',
        ]);

        $user = Auth::user();

        if ($password = $request->input('password')) {
            $user->password = Hash::make($password);
        }

        // Notify upon changing of password
        if ($request->has('password')) {
            try {
                Mail::to($user->email)->queue(new PasswordChanged($user->updated_at, $user->language));
            } catch (\Exception $e) {
            }
        }

        $user->save();

        return back();
    }

    public function getPreferences(): Response
    {
        $languages = [];

        foreach (config('app.locales') as $key => $value) {
            $languages[] = ['key' => $key, 'label' => trans('locales.' . $value)];
        }

        return Inertia::render('Settings/Preferences', compact('languages'));
    }

    public function postPreferences(Request $request): \Illuminate\Http\Response|RedirectResponse
    {
        $request->validate([
            'language' => 'nullable|in:' . implode(',', array_keys(config('app.locales'))),
            'theme' => 'nullable|in:light,dark',
            'weekly_report' => 'nullable|in:1,0',
            'default_transaction_type' => 'nullable|in:earning,spending',
            'first_day_of_week' => 'nullable|in:sunday,monday'
        ]);

        $user = Auth::user();

        $reloadForLangue = false;

        // Preferences
        if ($request->has('language')) {
            if ($user->language !== $request->input('language')) {
                $reloadForLangue = true;
            }
            $user->language = $request->input('language');
        }

        if ($request->has('theme')) {
            $user->theme = $request->input('theme');
        }

        if ($request->has('weekly_report')) {
            $user->weekly_report = $request->input('weekly_report') == '1' ? true : false;
        }

        if ($request->has('default_transaction_type')) {
            $user->default_transaction_type = $request->input('default_transaction_type');
        }

        if ($request->has('first_day_of_week')) {
            $user->first_day_of_week = $request->input('first_day_of_week');
        }

        $user->save();
        if ($reloadForLangue) {
            return Inertia::location(route('settings.preferences'));
        }
        return back();
    }

    public function getDashboard(): Response
    {
        //TODO : not make widget so abstract. Go to make dedicated component
        $types = array_keys(config('widgets.types'));
        $expectedProperties = [];
        $widgets = Auth::user()->widgets();
        foreach ($types as $type) {
            $expectedProperties[$type] = config('widgets.types.' . $type . '.properties');
        }
        return Inertia::render('Settings/Dashboard', compact("types", "expectedProperties", "widgets"));
    }

    public function getSpaces(): Response
    {
        return Inertia::render('Settings/Spaces/Index', [
            'spaces' => Auth::user()->spaces
        ]);
    }
}
