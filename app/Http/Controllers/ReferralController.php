<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    /**
     * Get user's referral code and stats
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Get or create referral code
        $referralCode = DB::table('referral_codes')->where('user_id', $user->id)->first();

        if (!$referralCode) {
            // Generate unique referral code with user prefix
            do {
                $code = $this->generateReferralCode($user->name);
                $exists = DB::table('referral_codes')->where('code', $code)->exists();
            } while ($exists);

            // Create referral code
            $referralCodeId = DB::table('referral_codes')->insertGetId([
                'user_id' => $user->id,
                'code' => $code,
                'referral_count' => 0,
                'points_earned' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $referralCode = DB::table('referral_codes')->find($referralCodeId);
        }

        // Get referral stats
        $referrals = DB::table('user_referrals')
            ->join('users', 'user_referrals.referred_user_id', '=', 'users.id')
            ->where('user_referrals.referrer_id', $user->id)
            ->select('users.name', 'users.email', 'user_referrals.points_awarded', 'user_referrals.created_at')
            ->orderBy('user_referrals.created_at', 'desc')
            ->get();

        return response()->json([
            'code' => $referralCode->code,
            'referral_count' => $referralCode->referral_count,
            'points_earned' => $referralCode->points_earned,
            'referrals' => $referrals,
        ]);
    }

    /**
     * Apply referral code during registration
     */
    public function applyReferralCode(Request $request)
    {
        $referralCode = $request->input('referral_code');
        
        if (!$referralCode) {
            return response()->json(['valid' => false]);
        }

        $code = DB::table('referral_codes')->where('code', $referralCode)->first();

        if (!$code) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid referral code'
            ]);
        }

        return response()->json([
            'valid' => true,
            'referrer_id' => $code->user_id
        ]);
    }

    /**
     * Process referral after successful registration
     */
    public function processReferral($userId, $referralCode)
    {
        $code = DB::table('referral_codes')->where('code', $referralCode)->first();

        if (!$code) {
            return;
        }

        // Check if user is trying to use their own referral code
        if ($code->user_id == $userId) {
            return;
        }

        // Check if this user was already referred by someone
        $existingReferral = DB::table('user_referrals')
            ->where('referred_user_id', $userId)
            ->first();

        if ($existingReferral) {
            return; // User can only be referred once
        }

        $pointsAwarded = 50; // Points for each successful referral

        // Create referral record
        DB::table('user_referrals')->insert([
            'referrer_id' => $code->user_id,
            'referred_user_id' => $userId,
            'points_awarded' => $pointsAwarded,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update referral code stats
        DB::table('referral_codes')
            ->where('id', $code->id)
            ->increment('referral_count');

        DB::table('referral_codes')
            ->where('id', $code->id)
            ->increment('points_earned', $pointsAwarded);

        // Award points to referrer
        $referrerPoints = DB::table('user_points')->where('user_id', $code->user_id)->first();

        if ($referrerPoints) {
            DB::table('user_points')
                ->where('user_id', $code->user_id)
                ->increment('total_points', $pointsAwarded);
        } else {
            DB::table('user_points')->insert([
                'user_id' => $code->user_id,
                'total_points' => $pointsAwarded,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Generate a memorable referral code with user prefix
     * Uses only unambiguous characters (no 0/O, 1/I/L confusion)
     */
    private function generateReferralCode(string $userName): string
    {
        // Unambiguous characters - excludes 0, O, 1, I, L to avoid confusion
        $characters = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        
        // Get first 2 letters from user's name (uppercase, letters only)
        $cleanName = preg_replace('/[^A-Za-z]/', '', $userName);
        $prefix = strtoupper(substr($cleanName, 0, 2));
        
        // If name is too short, use 'XX' as prefix
        if (strlen($prefix) < 2) {
            $prefix = str_pad($prefix, 2, 'X');
        }
        
        // Generate 6 random unambiguous characters
        $randomPart = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < 6; $i++) {
            $randomPart .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $prefix . $randomPart;
    }
}

