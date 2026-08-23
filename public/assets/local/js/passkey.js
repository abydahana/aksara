/**
 * Aksara Passkey / WebAuthn Helper
 */

(function (window) {
  'use strict';

  const Passkey = {
    /**
     * Check if WebAuthn is supported by browser
     */
    isSupported: function () {
      return window.PublicKeyCredential !== undefined && typeof window.PublicKeyCredential === 'function';
    },

    /**
     * Convert ArrayBuffer / Uint8Array to Base64URL string
     */
    bufferToBase64Url: function (buffer) {
      const bytes = new Uint8Array(buffer);
      let binary = '';
      for (let i = 0; i < bytes.byteLength; i++) {
        binary += String.fromCharCode(bytes[i]);
      }
      return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    },

    /**
     * Convert Base64URL string to Uint8Array buffer
     */
    base64UrlToBuffer: function (base64url) {
      let base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
      while (base64.length % 4) {
        base64 += '=';
      }
      const binary = atob(base64);
      const bytes = new Uint8Array(binary.length);
      for (let i = 0; i < binary.length; i++) {
        bytes[i] = binary.charCodeAt(i);
      }
      return bytes.buffer;
    },

    /**
     * Trigger Passkey Registration flow
     */
    register: function (optionsUrl, verifyUrl, deviceNameInputId) {
      if (!this.isSupported()) {
        return throw_exception(404, 'WebAuthn / Passkey is not supported on this browser or origin.');
      }

      const self = this;
      const deviceName = deviceNameInputId ? document.getElementById(deviceNameInputId)?.value || '' : '';

      // Fetch registration options from server
      $.ajax({
        url: optionsUrl,
        type: 'POST',
        data: {
          device_name: deviceName
        },
        dataType: 'json',
        success: function (response) {
          if (response.status !== 200 && response.code !== 200) {
            return throw_exception(response.code || response.status || 400, response.message, response.target || response.redirect, response.redirect);
          }

          const options = response.options;
          options.challenge = self.base64UrlToBuffer(options.challenge);
          options.user.id = self.base64UrlToBuffer(options.user.id);

          if (options.excludeCredentials) {
            options.excludeCredentials = options.excludeCredentials.map(function (cred) {
              cred.id = self.base64UrlToBuffer(cred.id);
              return cred;
            });
          }

          // Request browser biometrics credential creation
          navigator.credentials
            .create({ publicKey: options })
            .then(function (credential) {
              const credentialData = {
                id: credential.id,
                rawId: self.bufferToBase64Url(credential.rawId),
                type: credential.type,
                device_name: deviceName,
                response: {
                  clientDataJSON: self.bufferToBase64Url(credential.response.clientDataJSON),
                  attestationObject: self.bufferToBase64Url(credential.response.attestationObject),
                  transports: credential.response.getTransports ? credential.response.getTransports() : []
                }
              };

              // Send credential payload back to server for verification & storage
              $.ajax({
                url: verifyUrl,
                type: 'POST',
                data: JSON.stringify(credentialData),
                contentType: 'application/json',
                dataType: 'json',
                success: function (res) {
                  return throw_exception(res.code || res.status || 200, res.message, res.target || res.redirect, res.redirect);
                },
                error: function (err) {
                  const res = err.responseJSON || {};
                  return throw_exception(err.status || res.code || 400, res.message || 'Passkey verification failed.', res.target || res.redirect, res.redirect);
                }
              });
            })
            .catch(function (err) {
              return throw_exception(500, 'Passkey creation cancelled or failed: ' + err.message);
            });
        },
        error: function (err) {
          const res = err.responseJSON || {};
          const code = err.status || res.code || 404;
          const msg = res.message || 'Failed to fetch registration options.';

          return throw_exception(code, msg, res.target || res.redirect, res.redirect);
        }
      });
    },

    /**
     * Trigger Passkey Sign-In (Authentication) flow
     */
    login: function (optionsUrl, verifyUrl) {
      if (!this.isSupported()) {
        return throw_exception(404, 'WebAuthn / Passkey is not supported on this browser or origin.');
      }

      const self = this;

      // Fetch assertion options from server
      $.ajax({
        url: optionsUrl,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
          if (response.status !== 200 && response.code !== 200) {
            return throw_exception(response.code || response.status || 400, response.message, response.target || response.redirect, response.redirect);
          }

          const options = response.options;
          options.challenge = self.base64UrlToBuffer(options.challenge);

          if (options.allowCredentials) {
            options.allowCredentials = options.allowCredentials.map(function (cred) {
              cred.id = self.base64UrlToBuffer(cred.id);
              return cred;
            });
          }

          // Request browser biometrics verification
          navigator.credentials
            .get({ publicKey: options })
            .then(function (assertion) {
              const assertionData = {
                id: assertion.id,
                rawId: self.bufferToBase64Url(assertion.rawId),
                type: assertion.type,
                response: {
                  clientDataJSON: self.bufferToBase64Url(assertion.response.clientDataJSON),
                  authenticatorData: self.bufferToBase64Url(assertion.response.authenticatorData),
                  signature: self.bufferToBase64Url(assertion.response.signature),
                  userHandle: assertion.response.userHandle ? self.bufferToBase64Url(assertion.response.userHandle) : null
                }
              };

              // Send assertion payload to server for authentication
              $.ajax({
                url: verifyUrl,
                type: 'POST',
                data: JSON.stringify(assertionData),
                contentType: 'application/json',
                dataType: 'json',
                success: function (res) {
                  return throw_exception(res.code || res.status || 301, res.message, res.target || res.redirect, res.redirect);
                },
                error: function (err) {
                  const res = err.responseJSON || {};
                  return throw_exception(err.status || res.code || 400, res.message || 'Passkey authentication failed.', res.target || res.redirect, res.redirect);
                }
              });
            })
            .catch(function (err) {
              return throw_exception(500, err.message);
            });
        },
        error: function (err) {
          const res = err.responseJSON || {};
          const code = err.status || res.code || 404;
          const msg = res.message || 'Failed to fetch login options.';

          return throw_exception(code, msg, res.target || res.redirect, res.redirect);
        }
      });
    }
  };

  window.Passkey = Passkey;
})(window);
