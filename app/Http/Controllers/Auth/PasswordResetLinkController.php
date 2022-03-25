<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PasswordResetLinkController extends Controller
{
    private PasswordResetRepository $passwordResetRepository;

    public function __construct(PasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Display the password reset link request view.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'token' => $request->get('token'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate(User::getValidationRulesForPasswordReset());

        if ($request->input('email') && !$request->has('token')) {
            $email = $request->input('email');

            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                $existingRecord = $this->passwordResetRepository->getByEmail($email);

                if (!$existingRecord) {
                    $shippingToken = Str::random(100);

                    $this->passwordResetRepository->create($email, $shippingToken);
                } else {
                    $shippingToken = $existingRecord->token;
                }

                try {
                    Mail::to($email)->queue(new ResetPassword($shippingToken));
                } catch (\Exception $e) {
                }
            }

            $request->session()->flash('message', 'success');
            return back();
        } elseif ($request->has('token') && $request->has('password') && !$request->has('email')) {
            $token = $request->input('token');
            $password = $request->input('password');

            $record = $this->passwordResetRepository->getByToken($token);

            if ($record) {
                $user = User::where('email', $record->email)->first();

                $user->update(['password' => Hash::make($password)]);
            }

            $this->passwordResetRepository->delete($token);
            $request->session()->flash('message', 'success');
            return redirect()
                ->route('login');
        }

        return back();
    }
}
