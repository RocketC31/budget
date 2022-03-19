<?php

namespace App\Http\Controllers;

use App\Actions\CreateStripeCheckoutAction;
use App\Actions\CreateStripeCustomerAction;
use App\Actions\FetchStripeSubscriptionAction;
use App\Exceptions\UserStripelessException;
use Illuminate\Http\Request;
use App\Mail\PasswordChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SettingsController extends Controller
{
    public function getIndex()
    {
        return redirect()->route('settings.profile');
    }

    public function postIndex(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif',
            'password' => 'nullable|confirmed',
            'language' => 'nullable|in:' . implode(',', array_keys(config('app.locales'))),
            'theme' => 'nullable|in:light,dark',
            'weekly_report' => 'nullable|in:true,false',
            'default_transaction_type' => 'nullable|in:earning,spending',
            'first_day_of_week' => 'nullable|in:sunday,monday'
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

        // Account
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        if ($password = $request->input('password')) {
            $user->password = Hash::make($password);
        }

        // Preferences
        if ($request->has('language')) {
            $user->language = $request->input('language');
        }

        if ($request->has('theme')) {
            $user->theme = $request->input('theme');
        }

        if ($request->has('weekly_report')) {
            $user->weekly_report = $request->input('weekly_report') == 'true' ? true : false;
        }

        if ($request->has('default_transaction_type')) {
            $user->default_transaction_type = $request->input('default_transaction_type');
        }

        if ($request->has('first_day_of_week')) {
            $user->first_day_of_week = $request->input('first_day_of_week');
        }

        $user->save();

        // Notify upon changing of password
        if ($request->has('password')) {
            try {
                Mail::to($user->email)->queue(new PasswordChanged($user->updated_at));
            } catch (\Exception $e) {
            }
        }

        return back();
    }

    public function getProfile()
    {
        return view('settings.profile');
    }

    public function getAccount()
    {
        return view('settings.account');
    }

    public function getSpaces()
    {
        return view('settings.spaces.index', [
            'spaces' => Auth::user()->spaces
        ]);
    }

    public function getPreferences()
    {
        $languages = [];

        foreach (config('app.locales') as $key => $value) {
            $flag = $key;

            if ($key == 'en') {
                $flag = 'us';
            }

            $languages[] = ['key' => $key, 'label' => '<i class="twf twf-s twf-' . $flag . '"></i> ' . $value];
        }

        return view('settings.preferences', compact('languages'));
    }

    public function getDashboard()
    {
        return view('settings.dashboard');
    }

    public function getBilling(Request $request)
    {
        $user = $request->user();
        $stripeSubscription = null;

        try {
            $stripeSubscription = (new FetchStripeSubscriptionAction())->execute($user->id);
        } catch (UserStripelessException $e) {
            // Don't worry, $stripeSubscription will simply be `null`
        }

        return view('settings.billing', [
            'user' => $user,
            'stripeSubscription' => $stripeSubscription
        ]);
    }

    public function postUpgrade(Request $request)
    {
        $user = $request->user();

        if (!$user->stripe_customer_id) {
            (new CreateStripeCustomerAction())->execute($user->id);
        }

        $stripeSubscription = (new FetchStripeSubscriptionAction())->execute($user->id);

        if ($stripeSubscription) {
            return redirect()->route('settings.billing');
        }

        $stripeCheckoutId = (new CreateStripeCheckoutAction())->execute($user->id);

        return view('stripe_checkout_redirect', ['stripeCheckoutId' => $stripeCheckoutId]);
    }

    public function postCancel(Request $request)
    {
        $user = $request->user();

        if (!$user->stripe_customer_id) {
            return redirect()->route('settings.billing');
        }

        $stripeSubscription = (new FetchStripeSubscriptionAction())->execute($user->id);

        if (!$stripeSubscription) {
            return redirect()->route('settings.billing');
        }

        $stripeSubscription->cancel();

        return redirect()->route('settings.billing');
    }
}
