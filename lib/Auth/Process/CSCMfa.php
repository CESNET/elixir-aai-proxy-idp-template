<?php

declare(strict_types=1);

namespace SimpleSAML\Module\elixir\Auth\Process;

use SimpleSAML\Auth\ProcessingFilter;
use SimpleSAML\Auth\State;
use SimpleSAML\Configuration;
use SimpleSAML\Error\Exception;
use SimpleSAML\Logger;
use SimpleSAML\Module;

class CSCMfa extends ProcessingFilter
{
    public const MFA_IDENTIFIER = 'https://refeds.org/profile/mfa';

    public const CONFIG_FILE_NAME = 'module_elixir.php';

    public const CLIENT_ID = 'clientId';

    public const CLIENT_SECRET = 'clientSecret';

    public const REQUESTED_SCOPES = 'requestedScopes';

    public const OPENID_CONFIGURATION_URL = 'openidConfigurationUrl';

    public const AUTHORIZATION_ENDPOINT = 'authorization_endpoint';

    public const TOKEN_ENDPOINT = 'token_endpoint';

    public const USERINFO_ENDPOINT = 'userinfo_endpoint';

    private $clientId;

    private $requestedScopes;

    private $authorizationEndpoint;

    private $redirectUri;

    public function __construct($config, $reserved)
    {
        assert('is_array($config)');
        parent::__construct($config, $reserved);

        $conf = Configuration::getConfig(self::CONFIG_FILE_NAME);

        $this->clientId = $conf->getString(self::CLIENT_ID, '');
        $this->requestedScopes = $conf->getArray(self::REQUESTED_SCOPES, []);
        $openidConfigurationUrl = $conf->getString(self::OPENID_CONFIGURATION_URL, '');

        if (empty($this->clientId)) {
            throw new Exception(
                'elixir:CSCMfa: missing mandatory configuration option "' . self::CLIENT_ID .
                '" in configuration file "' . self::CONFIG_FILE_NAME . '".'
            );
        }

        if (empty($this->requestedScopes)) {
            throw new Exception(
                'elixir:CSCMfa: missing mandatory configuration option "' . self::REQUESTED_SCOPES .
                '" in configuration file "' . self::CONFIG_FILE_NAME . '".'
            );
        }

        if (empty($openidConfigurationUrl)) {
            throw new Exception(
                'elixir:CSCMfa: missing mandatory configuration option "' . self::AUTHORIZATION_ENDPOINT .
                '" in configuration file "' . self::CONFIG_FILE_NAME . '".'
            );
        }

        $metadata = json_decode(file_get_contents($openidConfigurationUrl), true);
        $this->authorizationEndpoint = $metadata[self::AUTHORIZATION_ENDPOINT];

        $this->redirectUri = Module::getModuleURL('elixir') . '/CSCMfaContinue.php';
    }

    public function process(&$request)
    {
        assert('is_array($request)');

        $requestedAuthnContextClassRef = [];

        if (isset($request['saml:RequestedAuthnContext']['AuthnContextClassRef'])) {
            $requestedAuthnContextClassRef = $request['saml:RequestedAuthnContext']['AuthnContextClassRef'][0];
            if (! is_array($requestedAuthnContextClassRef)) {
                $requestedAuthnContextClassRef = [$requestedAuthnContextClassRef];
            }
        }

        if (! in_array(self::MFA_IDENTIFIER, $requestedAuthnContextClassRef, true)) {
            # Everything is OK, SP didn't requested MFA
            Logger::debug('Multi factor authentication is not required');
            return;
        }

        # Check if IdP did MFA
        $authContextClassRef = [];
        if (isset($request['saml:sp:AuthnContext'])) {
            $authContextClassRef = $request['saml:sp:AuthnContext'];
            if (! is_array($authContextClassRef)) {
                $authContextClassRef = [$authContextClassRef];
            }
        }
        if (in_array(self::MFA_IDENTIFIER, $authContextClassRef, true)) {
            # MFA was performed on IdP
            Logger::debug('Multi factor authentication was performed on Identity provider side');
            return;
        }

        Logger::debug('Multi factor authentication wasn\'t performed and will be performed on CSC side.');

        $stateId = State::saveState($request, 'elixir:CSCMfa', true);

        if (! isset($request['Attributes']['eduPersonUniqueId'][0])) {
            throw new Exception('elixir:CSCMfa: missing required attribute "eduPersonUniqueId" in request');
        }
        $elixirId = $request['Attributes']['eduPersonUniqueId'][0];

        # Prepare claims
        $claims = [
            'id_token' => [
                'sub' => [
                    'value' => $stateId,
                ],
                'otp_key' => [
                    'value' => $elixirId,
                ],
            ],
        ];

        if (isset($request['Attributes']['mobile'][0])) {
            $phoneNumber = $request['Attributes']['mobile'][0];
            $claims['id_token']['mobile']['value'] = $phoneNumber;
        }

        # Prepare params
        $params = [
            'response_type' => 'code',
            'scope' => implode(' ', $this->requestedScopes),
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $stateId,
            'claims' => json_encode($claims),
        ];

        $mfa_url = $this->authorizationEndpoint . '?' . http_build_query($params);

        Header('Location: ' . $mfa_url);
        exit();
    }
}
