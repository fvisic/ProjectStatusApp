<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

use function response;

class WebAuthnRegisterController
{
    /**
     * Returns a challenge to be verified by the user device.
     */
    public function options(AttestationRequest $request): Responsable
    {
        return $request
            ->fastRegistration()
            ->toCreate();
    }

    /**
     * Registers a device for further WebAuthn authentication.
     *
     * Captures optional `alias` from the request so users can name their
     * passkey ("MacBook Touch ID", "iPhone", "YubiKey 5"). The package's
     * AttestedRequest::save() reads the validated `alias` automatically
     * when present in the validated payload.
     */
    public function register(AttestedRequest $request): Response
    {
        $request->validate(['alias' => 'nullable|string|max:50']);

        $request->save();

        return response()->noContent();
    }
}
