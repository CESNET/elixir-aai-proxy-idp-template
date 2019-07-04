<?php

use SimpleSAML\Auth\State;
use SimpleSAML\Configuration;
use SimpleSAML\Error\Exception;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Module\elixir\Auth\Process\CSCMfa;


$mfaTokenUrl = null;
$mfaUserInfoUrl = null;

$conf = Configuration::getConfig(CSCMfa::CONFIG_FILE_NAME);

$clientId = $conf->getString(CSCMfa::CLIENT_ID, '');
if (empty($clientId)) {
    throw new Exception(
        'elixir:CSCMfa_continue: missing mandatory configuration option "' . CSCMfa::CLIENT_ID .
        '" in configuration file "' . CSCMfa::CONFIG_FILE_NAME . '".'    );
}

$clientSecret = $conf->getString(CSCMfa::CLIENT_SECRET, '');
if (empty($clientSecret)) {
    throw new Exception(
        'elixir:CSCMfa_continue: missing mandatory configuration option "' . CSCMfa::CLIENT_SECRET .
        '" in configuration file "' . CSCMfa::CONFIG_FILE_NAME . '".'    );
}

$openidConfigurationUrl = $conf->getString(CSCMfa::OPENID_CONFIGURATION_URL, '');
if (empty($openidConfigurationUrl)) {
    throw new Exception(
        'elixir:CSCMfa_continue: missing mandatory configuration option "' . CSCMfa::TOKEN_ENDPOINT .
        '" in configuration file "' . CSCMfa::CONFIG_FILE_NAME . '".'    );
}

$metadata = json_decode(file_get_contents($openidConfigurationUrl), true);

if (isset($metadata[CSCMfa::TOKEN_ENDPOINT])) {
    $mfaTokenUrl = $metadata[CSCMfa::TOKEN_ENDPOINT];
}

if (isset($metadata[CSCMfa::USERINFO_ENDPOINT])) {
    $mfaUserInfoUrl = $metadata[CSCMfa::USERINFO_ENDPOINT];
}

if ($mfaTokenUrl === null || $mfaUserInfoUrl === null) {
        throw new Exception(
        'elixir:CSCMfa_continue: Problem to get ' . CSCMfa::TOKEN_ENDPOINT . ' or ' .
        CSCMfa::USERINFO_ENDPOINT . ' from Openid configuration.'   );
}

$redirectUri = Module::getModuleURL('elixir') . '/CSCMfa_continue.php';

if (!isset($_GET['code'], $_GET['state'] )) {
    throw new Exception(
        'elixir:CSCMfa_continue: One of following required params: "code", "state" is missing.');
}

$code = $_GET['code'];
$stateId = $_GET['state'];

$state = State::loadState($stateId, 'elixir:CSCMfa');

# Prepare params for token endpoint
$params = [
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $redirectUri,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'nonce' => time(),
];

# Request to token endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mfaTokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

if (($response = curl_exec($ch)) === false) {
    throw new \Exception("Request to token endpoint wasn't successful : " . curl_error($ch));
}
$response = json_decode($response, true);

$accessToken = null;
$idToken = null;
if (isset($response['access_token'])) {
    $accessToken = $response['access_token'];
}

if (isset($response['id_token'])) {
    $idToken = $response['id_token'];
}

$params = array(
    'access_token' => $accessToken,
);

# Request to userinfo endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mfaUserInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));

if (($response = curl_exec($ch)) === false) {
    throw new \Exception("Request to token endpoint wasn't successful : " . curl_error($ch));
}

curl_close($ch);

$state['saml:sp:AuthnContext'] = CSCMfa::MFA_IDENTIFIER;

SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);

?>
