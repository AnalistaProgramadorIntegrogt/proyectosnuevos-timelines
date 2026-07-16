<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AzureAuthController extends Controller
{
    /**
     * Redirect the user to the Azure authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Obtain the user information from Azure.
     */
    public function callback(Request $request)
    {
        try {
            $azureUser = Socialite::driver('azure')->user();
            
            // Find user by Azure ID or Email
            $user = User::where('azure_id', $azureUser->getId())
                        ->orWhere('email', $azureUser->getEmail())
                        ->first();
                        
            if ($user) {
                // If user exists but doesn't have azure_id (matched by email), link it
                if (!$user->azure_id) {
                    $user->update([
                        'azure_id' => $azureUser->getId(),
                    ]);
                }
            } else {
                // If user doesn't exist, create a new one
                $user = User::create([
                    'name' => $azureUser->getName() ?? $azureUser->getNickname() ?? 'Usuario Microsoft',
                    'email' => $azureUser->getEmail(),
                    'password' => bcrypt(Str::random(24)), // Random password since they use OAuth
                    'azure_id' => $azureUser->getId(),
                ]);
                $user->assignRole('user');
            }
            
            Auth::login($user, true);
            
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Azure Login Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'No se pudo iniciar sesión con Microsoft. Por favor, inténtalo de nuevo.',
            ]);
        }
    }
}
