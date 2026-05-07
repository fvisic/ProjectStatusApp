/**
 * Minimal WebAuthn helper — wraps navigator.credentials.create / .get
 * and posts the encoded payload to laragear/webauthn endpoints.
 *
 * Replaces the deprecated vendor stub at resources/js/vendor/webauthn/webauthn.js.
 */

const ROUTES = {
    registerOptions: '/webauthn/register/options',
    register: '/webauthn/register',
    loginOptions: '/webauthn/login/options',
    login: '/webauthn/login',
};

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const baseHeaders = () => ({
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': csrfToken(),
});

// --- Base64URL <-> ArrayBuffer helpers ---------------------------------------

const base64UrlToBuffer = (base64Url) => {
    const padding = '='.repeat((4 - (base64Url.length % 4)) % 4);
    const base64 = (base64Url + padding).replace(/-/g, '+').replace(/_/g, '/');
    const binary = atob(base64);
    const buffer = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) buffer[i] = binary.charCodeAt(i);
    return buffer.buffer;
};

const bufferToBase64Url = (buffer) => {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
    return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
};

// --- Decode/encode PublicKeyCredential payloads ------------------------------

const decodeCreationOptions = (options) => {
    options.challenge = base64UrlToBuffer(options.challenge);
    options.user.id = base64UrlToBuffer(options.user.id);
    if (Array.isArray(options.excludeCredentials)) {
        options.excludeCredentials = options.excludeCredentials.map((c) => ({
            ...c,
            id: base64UrlToBuffer(c.id),
        }));
    }
    return options;
};

const decodeRequestOptions = (options) => {
    options.challenge = base64UrlToBuffer(options.challenge);
    if (Array.isArray(options.allowCredentials)) {
        options.allowCredentials = options.allowCredentials.map((c) => ({
            ...c,
            id: base64UrlToBuffer(c.id),
        }));
    }
    return options;
};

const encodeAttestationResponse = (credential) => ({
    id: credential.id,
    rawId: bufferToBase64Url(credential.rawId),
    type: credential.type,
    response: {
        clientDataJSON: bufferToBase64Url(credential.response.clientDataJSON),
        attestationObject: bufferToBase64Url(credential.response.attestationObject),
    },
});

const encodeAssertionResponse = (credential) => ({
    id: credential.id,
    rawId: bufferToBase64Url(credential.rawId),
    type: credential.type,
    response: {
        clientDataJSON: bufferToBase64Url(credential.response.clientDataJSON),
        authenticatorData: bufferToBase64Url(credential.response.authenticatorData),
        signature: bufferToBase64Url(credential.response.signature),
        userHandle: credential.response.userHandle ? bufferToBase64Url(credential.response.userHandle) : null,
    },
});

// --- Public API --------------------------------------------------------------

export const webauthnSupported = () =>
    typeof window !== 'undefined' &&
    window.PublicKeyCredential !== undefined &&
    typeof navigator.credentials?.create === 'function';

/**
 * Register a new passkey for the currently authenticated user.
 *
 * @param {string} alias  Friendly name ("MacBook Touch ID")
 * @returns {Promise<void>}
 */
export const register = async (alias) => {
    const optionsResponse = await fetch(ROUTES.registerOptions, {
        method: 'POST',
        credentials: 'same-origin',
        headers: baseHeaders(),
        body: JSON.stringify({}),
    });

    if (!optionsResponse.ok) {
        throw new Error('Could not start passkey registration.');
    }

    const options = decodeCreationOptions(await optionsResponse.json());
    const credential = await navigator.credentials.create({ publicKey: options });

    const registerResponse = await fetch(ROUTES.register, {
        method: 'POST',
        credentials: 'same-origin',
        headers: baseHeaders(),
        body: JSON.stringify({
            ...encodeAttestationResponse(credential),
            alias: alias || null,
        }),
    });

    if (!registerResponse.ok) {
        throw new Error('Server rejected passkey registration.');
    }
};

/**
 * Authenticate using a passkey.
 *
 * @param {string|null} email  Optional — restricts allowed credentials to this user.
 * @returns {Promise<void>}
 */
export const login = async (email = null) => {
    const optionsResponse = await fetch(ROUTES.loginOptions, {
        method: 'POST',
        credentials: 'same-origin',
        headers: baseHeaders(),
        body: JSON.stringify(email ? { email } : {}),
    });

    if (!optionsResponse.ok) {
        throw new Error('Could not start passkey login.');
    }

    const options = decodeRequestOptions(await optionsResponse.json());
    const assertion = await navigator.credentials.get({ publicKey: options });

    const loginResponse = await fetch(ROUTES.login, {
        method: 'POST',
        credentials: 'same-origin',
        headers: baseHeaders(),
        body: JSON.stringify(encodeAssertionResponse(assertion)),
    });

    if (loginResponse.status !== 204) {
        throw new Error('Passkey verification failed.');
    }
};

window.Webauthn = { register, login, webauthnSupported };
