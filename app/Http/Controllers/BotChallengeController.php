<?php

namespace App\Http\Controllers;

use App\Services\Security\BotGuard;
use Illuminate\Http\Request;

class BotChallengeController extends Controller
{
    public function verify(Request $request, BotGuard $guard)
    {
        $data = $request->validate([
            'id'       => 'required|string|size:32',
            'solution' => 'nullable|string|max:20',
            'answer'   => 'nullable|string|max:10',
        ]);

        $token = $guard->verify($request, $data['id'], $data['solution'] ?? null, $data['answer'] ?? null);
        if (!$token) {
            return response()->json(['success' => false], 422);
        }

        return response()->json(['success' => true])->cookie(
            BotGuard::PASS_COOKIE, $token, (int) ceil(config('botguard.pass_seconds') / 60),
            '/', null, $request->isSecure(), true, false, 'lax'
        );
    }
}
