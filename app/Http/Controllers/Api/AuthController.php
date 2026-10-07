<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Rules\AlphaSpace;
use App\Jobs\SendOtpSmsJob;
use App\Jobs\SendRegistrationCompletionSmsJob;
use App\Jobs\SendEmailNotificationJob;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // If email is not provided or empty, generate a placeholder email using the mobile
        if (!$request->filled('email') || trim($request->input('email')) === '') {
            $phone = $request->input('phone');
            if ($phone && preg_match('/^[0-9]{10}$/', $phone)) {
                $generatedEmail = $phone . '@entekeralam.kerala.gov.in';
                $request->merge(['email' => $generatedEmail]);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255', new AlphaSpace],
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:64|confirmed',
            'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone',
            'dob' => 'required|date',
            'gender' => 'nullable|in:male,female,transgender',
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'referral_code' => 'nullable|string|regex:/^[A-Z0-9]{8}$/|size:8',
        ], [
            'phone.regex' => 'Please enter a valid phone number',
            'referral_code.regex' => 'The referral code must contain only uppercase letters and numbers',
            'referral_code.size' => 'The referral code must be exactly 8 characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate previously generated registration OTP for this mobile
        $expectedOtp = Cache::get("otp:registration:mobile:{$request->phone}");
        if (!$expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not requested. Please request a new OTP.'
            ], 400);
        }

        if ($request->otp !== $expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // Clear used OTP
        Cache::forget("otp:registration:mobile:{$request->phone}");

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        // Process referral code if provided
        if ($request->filled('referral_code')) {
            $referralController = new \App\Http\Controllers\ReferralController();
            $referralController->processReferral($user->id, strtoupper($request->referral_code));
        }

        // Registration completion notifications
        if (!empty($request->phone)) {
            SendRegistrationCompletionSmsJob::dispatch($request->phone, $request->name);
        }
        // Only send email if it's not a placeholder email
        if (!empty($request->email) && !str_ends_with($request->email, '@entekeralam.kerala.gov.in')) {
            SendEmailNotificationJob::dispatch(
                $request->email,
                'Welcome to Ente Keralam',
                '',
                'registration_complete_email',
                'mail.auth.registration-complete',
                [
                    'name' => $request->name,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_login' => ['required','string','max:255','regex:/(^[0-9]{10}$)|(^[^@\\s]+@[^@\\s]+\\.[^@\\s]+$)/'], // 10-digit phone or email
            'password' => 'required|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userLogin = $request->user_login;
        $credentials = ['password' => $request->password];

        // Check if user_login is email or phone
        if (filter_var($userLogin, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $userLogin;
        } else {
            $credentials['phone'] = $userLogin;
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        // Check if account is deactivated
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support to reactivate.'
            ], 403);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists with this mobile number
        $user = User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this mobile number'
            ], 404);
        }

        // Check if account is deactivated
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support to reactivate.'
            ], 403);
        }

        // For testing, return fixed OTP
        // $otp = '300000';
        // Generate a random 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("otp:login:{$request->mobile}", $otp, now()->addMinutes(10));

        Log::info("OTP sent for login", ['mobile' => $request->mobile, 'otp' => $otp]);

        SendOtpSmsJob::dispatch($request->mobile, $otp, 'login_otp');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => app()->environment('local') ? $otp : null,
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists with this mobile number
        $user = User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this mobile number'
            ], 404);
        }

        // Check if account is deactivated
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support to reactivate.'
            ], 403);
        }

        $cachedOtp = Cache::get("otp:login:{$request->mobile}");
        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if ($request->otp !== $cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // Generate token for the user
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function forgotPasswordSendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:email,mobile',
            'email' => 'required_if:method,email|string|email|max:255',
            'mobile' => 'required_if:method,mobile|string|regex:/^[0-9]{10}$/',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $method = $request->method;
        $user = $method === 'email'
            ? User::where('email', $request->email)->first()
            : User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this ' . $method
            ], 404);
        }

        // $otp = $method === 'email' ? '200000' : '300000';
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        if ($method === 'email') {
            Cache::put("otp:forgot:email:{$request->email}", $otp, now()->addMinutes(10));
        } else {
            Cache::put("otp:forgot:mobile:{$request->mobile}", $otp, now()->addMinutes(10));
        }

        if ($method === 'mobile') {
            SendOtpSmsJob::dispatch($request->mobile, $otp, 'forgot_password_otp');
        } else {
            SendEmailNotificationJob::dispatch(
                $request->email,
                'Reset your Ente Keralam password',
                "Your OTP is {$otp}",
                'forgot_password_email_otp',
                'mail.auth.otp',
                [
                    'name' => $user->name ?? 'User',
                    'otp' => $otp,
                    'purpose' => 'Reset your password',
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'method' => $method
        ]);
    }

    public function resetPasswordWithOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:email,mobile',
            'email' => 'required_if:method,email|string|email|max:255',
            'mobile' => 'required_if:method,mobile|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'password' => 'required|string|min:6|max:64|confirmed',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $method = $request->method;
        $user = $method === 'email'
            ? User::where('email', $request->email)->first()
            : User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this ' . $method
            ], 404);
        }

        $cacheKey = $method === 'email'
            ? "otp:forgot:email:{$request->email}"
            : "otp:forgot:mobile:{$request->mobile}";
        $expectedOtp = Cache::get($cacheKey);

        if (!$expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if ($request->otp !== $expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // Replace any old tokens to ensure fresh session after reset
        $user->tokens()->delete();
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function sendUpdateMobileOTP(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone,' . $user->id,
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // $otp = '300000';
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("otp:update_mobile:{$request->mobile}", $otp, now()->addMinutes(10));

        SendOtpSmsJob::dispatch($request->mobile, $otp, 'update_mobile_otp');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
        ]);
    }

    public function updateMobileWithOTP(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone,' . $user->id,
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $expectedOtp = Cache::get("otp:update_mobile:{$request->mobile}");
        if (!$expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if ($request->otp !== $expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        $user->phone = $request->mobile;
        $user->save();

        // Recalculate profile completion (this will award points if profile is now 100% complete)
        $user->calculateProfileCompletion();

        return response()->json([
            'success' => true,
            'message' => 'Mobile number updated successfully',
            'user' => $user,
        ]);
    }

    public function sendUpdateEmailOTP(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ], [
            'email.unique' => 'This email is already in use',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // $otp = '400000';
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("otp:update_email:{$request->email}", $otp, now()->addMinutes(10));

        SendEmailNotificationJob::dispatch(
            $request->email,
            'Update Email - Verify Your Email Address',
            '',
            'update_email_otp',
            'mail.auth.otp',
            [
                'name' => $user->name ?? 'User',
                'otp' => $otp,
                'purpose' => 'Update your email address',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your email',
        ]);
    }

    public function updateEmailWithOTP(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ], [
            'email.unique' => 'This email is already in use',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $expectedOtp = Cache::get("otp:update_email:{$request->email}");
        if (!$expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if ($request->otp !== $expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        $user->email = $request->email;
        $user->save();

        // Recalculate profile completion (this will award points if profile is now 100% complete)
        $user->calculateProfileCompletion();

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully',
            'user' => $user,
        ]);
    }

    public function forgotPasswordVerifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:email,mobile',
            'email' => 'required_if:method,email|string|email|max:255',
            'mobile' => 'required_if:method,mobile|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $method = $request->method;
        $user = $method === 'email'
            ? User::where('email', $request->email)->first()
            : User::where('phone', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this ' . $method
            ], 404);
        }

        // $expectedOtp = $method === 'email' ? '200000' : '300000';
        $expectedOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        if ($request->otp !== $expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified. Please set a new password.'
        ]);
    }

    public function registrationSendOTP(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // $otp = '300000';
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("otp:registration:mobile:{$request->mobile}", $otp, now()->addMinutes(10));

        Log::info("OTP sent for registration", ['mobile' => $request->mobile, 'otp' => $otp]);

        SendOtpSmsJob::dispatch($request->mobile, $otp, 'registration_mobile_otp');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => app()->environment('local') ? $otp : null,
        ]);
    }

    public function registrationVerifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6',
        ], [
            'mobile.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $expectedOtp = Cache::get("otp:registration:mobile:{$request->mobile}");

        if (!$expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if ($request->otp !== $expectedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function socialRedirect($provider)
    {
        $driver = Socialite::driver($provider);

        if ($provider === 'facebook') {
            $driver->scopes(['email']);
        }

        return $driver->redirect();
    }

    public function socialCallback($provider)
    {
        $frontendUrl = config('app.url');
        $genericError = 'Social login failed. Please try again or use another login method.';

        try {
            $socialUser = Socialite::driver($provider)->user();
            $providerId = $socialUser->getId();
            $email = $socialUser->getEmail();
            $name = $socialUser->getName() ?: ($email ? Str::before($email, '@') : 'User');
            $avatarUrl = $socialUser->getAvatar();
            $safeAvatar = $avatarUrl ? Str::limit($avatarUrl, 255, '') : null;
            
            $user = null;

            if ($providerId) {
                $user = User::where('provider', $provider)
                    ->where('provider_id', $providerId)
                    ->first();
            }

            if (!$user && $email) {
                $user = User::where('email', $email)->first();
            }
            
            if ($user) {
                // Update provider info if needed
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $safeAvatar,
                ]);
            } else {
                if (!$email) {
                    Log::warning('Social login missing email', [
                        'provider' => $provider,
                        'provider_id' => $providerId,
                    ]);

                    $redirectUrl = $frontendUrl . '/login?social_login=error&message=' . urlencode($genericError);

                    return redirect($redirectUrl);
                }

                // Create new user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $safeAvatar,
                ]);
            }

            $token = $user->createToken('API Token')->plainTextToken;

            // For SPA, redirect to frontend with token and user data
            $redirectUrl = $frontendUrl . '/login?social_login=success&token=' . urlencode($token) . '&user=' . urlencode(base64_encode(json_encode($user)));

            return redirect($redirectUrl);
        } catch (\Exception $e) {
            Log::error('Social login failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            // Redirect to frontend with error
            $redirectUrl = $frontendUrl . '/login?social_login=error&message=' . urlencode($genericError);
            
            return redirect($redirectUrl);
        }
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Prevent email change for social accounts
        if ($user->provider && $request->has('email') && $request->email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email cannot be changed for social accounts'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255', new AlphaSpace],
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone,' . $user->id,
            'role' => 'nullable|string|in:public,institute',
            'dob' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,transgender',
        ], [
            'phone.regex' => 'Please enter a valid phone number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->all());

        // Recalculate profile completion (this will award points if profile is now 100% complete)
        $user->calculateProfileCompletion();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
}
