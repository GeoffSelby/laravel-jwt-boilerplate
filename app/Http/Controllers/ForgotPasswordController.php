<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ForgotPasswordController extends Controller
{
    public function sendForgotPasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', '=', $request->get('email'))->firstOrFail();

        $broker = Password::broker();
        $sendResponse = $broker->sendResetLink($request->only('email'));

        if ($sendResponse !== Password::RESET_LINK_SENT) {
            throw new HttpException(500);
        }

        return response()->json([
            'status' => 'ok',
        ], 200);
    }
}
