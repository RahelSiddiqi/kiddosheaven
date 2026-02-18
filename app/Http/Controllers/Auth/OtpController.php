<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected SmsService $smsService,
    ) {}

    // ─────────────────────────────────────────────────────────────
    // OTP LOGIN
    // ─────────────────────────────────────────────────────────────

    /**
     * Send OTP to an existing account by phone number.
     * POST /otp/login/send (JSON)
     */
    public function sendLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $user = User::where('phone', $request->phone)
                    ->orWhere('phone', $this->normalise($request->phone))
                    ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this phone number.',
            ], 422);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This account has been deactivated.',
            ], 403);
        }

        $sent = $this->otpService->send($user, $this->smsService);

        if (! $sent) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your phone. Valid for ' . OtpService::TTL . ' minutes.',
        ]);
    }

    /**
     * Verify OTP and log the user in.
     * POST /otp/login/verify (JSON)
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'otp'   => 'required|string|size:6',
        ]);

        $user = User::where('phone', $request->phone)
                    ->orWhere('phone', $this->normalise($request->phone))
                    ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 422);
        }

        if (! $this->otpService->verify($user, $request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please try again.',
            ], 422);
        }

        Auth::login($user, true);

        return response()->json([
            'success'  => true,
            'redirect' => $this->redirectAfterLogin($user),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // OTP REGISTRATION — Phone Verification Step
    // ─────────────────────────────────────────────────────────────

    /**
     * Show the OTP verification page after registration form submission.
     * GET /register/verify
     */
    public function showRegistrationVerify()
    {
        if (! session()->has('pending_registration')) {
            return redirect()->route('register')->withErrors(['phone' => 'Please complete the registration form first.']);
        }

        return view('auth.verify-otp', [
            'mode'  => 'register',
            'phone' => session('pending_registration.phone'),
        ]);
    }

    /**
     * Send OTP to the phone captured during registration.
     * POST /register/otp/send (JSON)
     */
    public function sendRegistrationOtp(Request $request)
    {
        $pending = session('pending_registration');

        if (! $pending) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please register again.'], 422);
        }

        // Upsert a temporary "unverified" user record so we have somewhere to store the OTP
        $user = User::firstOrCreate(
            ['phone' => $pending['phone']],
            [
                'name'     => $pending['name'],
                'email'    => $pending['email'],
                'password' => Hash::make($pending['password']),
                'is_active' => false, // not yet verified
            ]
        );

        // If the email is already taken by another user, abort
        if ($user->email !== $pending['email']) {
            return response()->json(['success' => false, 'message' => 'Email already registered.'], 422);
        }

        $sent = $this->otpService->send($user, $this->smsService);

        if (! $sent) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }

        session(['pending_registration.user_id' => $user->id]);

        return response()->json(['success' => true, 'message' => 'OTP sent to ' . $pending['phone']]);
    }

    /**
     * Verify registration OTP and activate the account.
     * POST /register/otp/verify
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $pending = session('pending_registration');

        if (! $pending || ! isset($pending['user_id'])) {
            return back()->withErrors(['otp' => 'Session expired. Please register again.']);
        }

        $user = User::find($pending['user_id']);

        if (! $user) {
            return back()->withErrors(['otp' => 'Account not found. Please register again.']);
        }

        if (! $this->otpService->verify($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Activate account and assign customer role
        $customerRole = Role::where('slug', 'customer')->first();

        $user->update([
            'is_active' => true,
            'role_id'   => $customerRole?->id,
        ]);

        session()->forget('pending_registration');

        Auth::login($user, true);

        return redirect()->route('home')->with('success', 'Welcome to Kiddo\'s Heaven! Your account has been created.');
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    protected function redirectAfterLogin(User $user): string
    {
        if ($user->is_admin || $user->role_id) {
            // If they have admin privileges, send to admin panel
            if ($user->is_admin) {
                return route('admin.dashboard');
            }
        }

        return route('home');
    }

    protected function normalise(string $phone): string
    {
        $phone = preg_replace('/[\s\-().]/', '', $phone);
        if (str_starts_with($phone, '01') && strlen($phone) === 11) {
            return '+880' . $phone;
        }
        return $phone;
    }
}
