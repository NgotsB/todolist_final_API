<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    // EmailVerificationController.php

public function verify(Request $request)
{
    $user = User::findOrFail($request->id);

    if ($user->email_verified_at) {
        return '';
    }

    if (!$user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return view('email-verified');// The deep link
}
    	

}
