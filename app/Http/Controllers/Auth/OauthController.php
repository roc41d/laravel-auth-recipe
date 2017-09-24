<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Socialite;
use \App\User;
use \Auth;

class OauthController extends Controller
{
    /**
     * Redirect the user to Oauth Provider
     *
     * @return Response
     */
    public function redirectToProvider($provider) {
        var_dump($provider);
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain user information from provider. Check if user already exists in database.
     * If user already exist, log user in. Otherwise, creat a new user and log in
     *
     *
     */
    public function handleProviderCallback($provider) {
        $user = Socialite::driver($provider)->user();

        $existingUser = User::where('email', '=', $user->getEmail())->first();
        
        if(isset($existingUser)) {
            if ($existingUser->email == $user->getEmail()) {
                // return "user already exist.";
                Auth::login($existingUser);
            }
        } else {
            $newUser = new User();
            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->password = \Hash::make(uniqid());
            $newUser->provider = $provider;
            $newUser->save();

            Auth::login($newUser);
        }

        return redirect('home')->with( array('message' => 'Successfully authenticated via '. $provider));
    }
}
