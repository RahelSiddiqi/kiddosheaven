<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SmsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected $redirectTo = '/account';

    public function __construct(
        protected OtpService $otpService,
        protected SmsService $smsService,
    ) {}

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration form submission.
     * Stores pending data in session → sends OTP → redirects to verification page.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // Store pending registration in session (account not yet created)
        session([
            'pending_registration' => [
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => $request->password, // plain text — hashed on account creation
            ],
        ]);

        // Create a temporary unverified user so we can store the OTP against an ID
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'is_active' => false,
            ]
        );

        // Update user data in case they're re-registering with same phone
        if ($user->wasRecentlyCreated === false) {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'is_active' => false,
            ]);
        }

        session(['pending_registration.user_id' => $user->id]);

        $this->otpService->send($user, $this->smsService);

        return redirect()->route('register.verify');
    }
}
