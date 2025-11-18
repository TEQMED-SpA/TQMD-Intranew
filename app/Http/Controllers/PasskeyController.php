<?php

namespace App\Http\Controllers;

use App\Models\Passkey;
use App\Services\PasskeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PasskeyController extends Controller
{
    public function options(Request $request, PasskeyService $passkeyService): JsonResponse
    {
        return response()->json($passkeyService->createRegistrationOptions($request->user()));
    }

    public function store(Request $request, PasskeyService $passkeyService): JsonResponse
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $passkey = $passkeyService->storeCredential($request, $request->user());

        return response()->json([
            'message' => 'Passkey registrada correctamente',
            'passkey' => $passkey,
        ]);
    }

    public function destroy(Passkey $passkey, Request $request): RedirectResponse
    {
        abort_unless($request->user()->id === $passkey->user_id, 403);

        $passkey->delete();

        return back()->with('status', 'Passkey eliminada.');
    }
}
