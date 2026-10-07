<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $user->calculateProfileCompletion();

        return response()->json([
            'user' => $user,
            'profile_completion' => $user->profile_completion_percentage,
            'is_complete' => $user->is_profile_complete,
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'dob' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'district' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'pincode' => 'sometimes|string|max:10',
            'assembly_constituency' => 'sometimes|string|max:255',
            'lsg_type' => 'sometimes|string|max:255',
            'lsg_name' => 'sometimes|string|max:255',
            'ward_no' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->all());
        $user->calculateProfileCompletion();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'profile_completion' => $user->profile_completion_percentage,
            'is_complete' => $user->is_profile_complete,
        ]);
    }

    /**
     * Get profile completion status
     */
    public function completion(Request $request)
    {
        $user = $request->user();
        $user->calculateProfileCompletion();

        return response()->json([
            'percentage' => $user->profile_completion_percentage,
            'is_complete' => $user->is_profile_complete,
        ]);
    }

    /**
     * Update profile picture/avatar
     */
    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $hadAvatar = !empty($user->avatar);

        // Delete old avatar if exists
        if ($user->avatar) {
            $oldAvatarPath = public_path('storage/' . $user->avatar);
            if (file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $avatarPath;
        $user->save();
        
        // Recalculate profile completion (this will award points if profile is now 100% complete)
        $user->calculateProfileCompletion();

        return response()->json([
            'message' => 'Profile picture updated successfully',
            'user' => $user,
            'avatar_url' => asset('storage/' . $avatarPath),
            'profile_completion' => $user->profile_completion_percentage,
            'is_complete' => $user->is_profile_complete,
        ]);
    }

    /**
     * Remove profile picture/avatar
     */
    public function removeAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user->avatar) {
            return response()->json([
                'message' => 'No profile picture to remove'
            ], 404);
        }

        // Delete avatar file
        $avatarPath = public_path('storage/' . $user->avatar);
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }

        $user->avatar = null;
        $user->calculateProfileCompletion();
        $user->save();

        return response()->json([
            'message' => 'Profile picture removed successfully',
            'user' => $user,
            'profile_completion' => $user->profile_completion_percentage,
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Deactivate user account
     */
    public function deactivateAccount(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
            'feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create deactivation record
        DB::table('account_deactivations')->insert([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'feedback' => $request->feedback,
            'deactivated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark user as inactive
        $user->is_active = false;
        $user->save();

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Account deactivated successfully',
        ]);
    }

    /**
     * Get user settings
     */
    public function getSettings(Request $request)
    {
        $user = $request->user();
        
        $settings = DB::table('user_settings')->where('user_id', $user->id)->first();
        
        // If no settings exist, return defaults
        if (!$settings) {
            return response()->json([
                'settings' => [
                    'profile_visibility' => 'public',
                    'show_email' => true,
                    'show_phone' => true,
                    'show_activities' => true,
                    'email_notifications' => true,
                    'task_reminders' => true,
                    'poll_notifications' => true,
                    'quiz_notifications' => true,
                    'achievement_notifications' => true,
                    'weekly_digest' => true,
                ]
            ]);
        }

        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'profile_visibility' => 'sometimes|in:public,friends,private',
            'show_email' => 'sometimes|boolean',
            'show_phone' => 'sometimes|boolean',
            'show_activities' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
            'task_reminders' => 'sometimes|boolean',
            'poll_notifications' => 'sometimes|boolean',
            'quiz_notifications' => 'sometimes|boolean',
            'achievement_notifications' => 'sometimes|boolean',
            'weekly_digest' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update or create settings
        DB::table('user_settings')->updateOrInsert(
            ['user_id' => $user->id],
            array_merge($request->all(), [
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())')
            ])
        );

        $settings = DB::table('user_settings')->where('user_id', $user->id)->first();

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => $settings
        ]);
    }

    /**
     * Get user skills and interests
     */
    public function getSkillsInterests(Request $request)
    {
        $user = $request->user();
        
        $data = DB::table('user_skills_interests')->where('user_id', $user->id)->first();
        
        // If no data exists, return empty arrays/nulls
        if (!$data) {
            return response()->json([
                'data' => [
                    'skills' => [],
                    'interests' => [],
                    'expertise_level' => null,
                    'occupation' => null,
                    'industry' => null,
                    'languages' => [],
                    'hobbies' => [],
                ]
            ]);
        }

        // Decode JSON fields
        $data->skills = json_decode($data->skills, true) ?? [];
        $data->interests = json_decode($data->interests, true) ?? [];
        $data->languages = json_decode($data->languages, true) ?? [];
        $data->hobbies = json_decode($data->hobbies, true) ?? [];

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Update user skills and interests
     */
    public function updateSkillsInterests(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'skills' => 'sometimes|array',
            'interests' => 'sometimes|array',
            'expertise_level' => 'nullable|in:beginner,intermediate,expert',
            'occupation' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'languages' => 'sometimes|array',
            'hobbies' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Encode arrays to JSON
        if (isset($data['skills'])) {
            $data['skills'] = json_encode($data['skills']);
        }
        if (isset($data['interests'])) {
            $data['interests'] = json_encode($data['interests']);
        }
        if (isset($data['languages'])) {
            $data['languages'] = json_encode($data['languages']);
        }
        if (isset($data['hobbies'])) {
            $data['hobbies'] = json_encode($data['hobbies']);
        }

        // Update or create record
        DB::table('user_skills_interests')->updateOrInsert(
            ['user_id' => $user->id],
            array_merge($data, [
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())')
            ])
        );

        $result = DB::table('user_skills_interests')->where('user_id', $user->id)->first();
        
        // Decode JSON fields for response
        $result->skills = json_decode($result->skills, true) ?? [];
        $result->interests = json_decode($result->interests, true) ?? [];
        $result->languages = json_decode($result->languages, true) ?? [];
        $result->hobbies = json_decode($result->hobbies, true) ?? [];

        return response()->json([
            'message' => 'Skills and interests updated successfully',
            'data' => $result
        ]);
    }
}

