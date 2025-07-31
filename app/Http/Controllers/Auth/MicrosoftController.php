<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    /**
     * Redirect the user to the Microsoft authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    /**
     * Obtain the user information from Microsoft.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();
            
            // Check if user already exists with this Microsoft ID
            $user = User::where('microsoft_id', $microsoftUser->getId())->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->intended('/home');
            }
            
            // Check if user exists with this email
            $existingUser = User::where('email', $microsoftUser->getEmail())->first();
            
            if ($existingUser) {
                // Update existing user with Microsoft ID
                $existingUser->update([
                    'microsoft_id' => $microsoftUser->getId()
                ]);
                Auth::login($existingUser);
                return redirect()->intended('/home');
            }
            
            // OPTION: Uncomment this block to restrict access to pre-approved users only
            /*
            return redirect('/login')->withErrors([
                'error' => 'Access denied. Please contact your administrator to request access to this system.'
            ]);
            */
            
            // Create new user (auto-registration for organization members)
            $user = User::create([
                'name' => $microsoftUser->getName(),
                'email' => $microsoftUser->getEmail(),
                'microsoft_id' => $microsoftUser->getId(),
                'password' => Hash::make(uniqid()), // Random password since they'll use Microsoft auth
                'email_verified_at' => now(), // Microsoft emails are considered verified
            ]);
            
            Auth::login($user);
            return redirect()->intended('/home');
            
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Unable to authenticate with Microsoft. Please try again.']);
        }
    }
}
