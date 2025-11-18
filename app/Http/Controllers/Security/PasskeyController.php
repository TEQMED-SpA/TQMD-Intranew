<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Passkey;
use App\Services\WebAuthnService;
use Illuminate\Http\Request;

class PasskeyController extends Controller
{
    public function options(Request $request, WebAuthnService $service)
    {
        return response()->json($service->creationOptions($request->user()));
    }

    public function store(Request $request, WebAuthnService $service)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'attestation' => ['required', 'array'],
        ]);

        $user = $request->user();

        $passkey = $service->storePasskey($request, $user, $request->input('name', 'Passkey'));

        return response()->json(['id' => $passkey->id]);
    }

    public function index(Request $request)
    {
        return response()->json(
            $request->user()->passkeys()->latest()->get()
        );
    }

    public function destroy(Request $request, Passkey $passkey)
    {
        abort_if($passkey->user_id !== $request->user()->id, 403);

        $passkey->delete();

        return response()->noContent();
    }
}
