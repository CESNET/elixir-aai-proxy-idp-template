<?php

declare(strict_types=1);

namespace SimpleSAML\Module\elixir;

use SimpleSAML\Auth\State;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Module\authswitcher\DiscoUtils;
use SimpleSAML\Module\discopower\PowerIdPDisco;
use SimpleSAML\Module\perun\Auth\Process\MultifactorAcrs;
use SimpleSAML\Module\perun\model\WarningConfiguration;
use SimpleSAML\Utils\HTTP;

/**
 * This class implements a IdP discovery service.
 *
 * This module extends the DiscoPower IdP disco handler, so it needs to be avaliable and enabled and configured.
 *
 * It adds functionality of whitelisting and greylisting IdPs. for security reasons for blacklisting please manipulate
 * directly with metadata. In case of manual idps comment them out or in case of automated metadata fetching configure
 * blacklist in config-metarefresh.php
 */
class Disco extends PowerIdPDisco
{
    public const CONFIG_FILE_NAME = 'module_perun.php';

    public const URN_CESNET_PROXYIDP_IDPENTITYID = 'urn:cesnet:proxyidp:idpentityid:';

    public const LS_IDP = 'https://proxy.aai.lifescience-ri.eu/proxy';

    // ROOT CONFIGURATION ENTRY
    public const WAYF = 'wayf_config';

    public const INTERFACE = 'interface';

    public const RPC = 'rpc';

    public const REMOVE_AUTHN_CONTEXT_CLASS_PREFIXES = 'remove_authn_context_class_ref_prefixes';

    public const ADD_AUTHN_CONTEXT_CLASSES_FOR_MFA = 'add_authn_context_classes_for_mfa';

    public const RETURN = 'return';

    public const AUTHN_CONTEXT_CLASS_REF = 'AuthnContextClassRef';

    public const WARNING_ATTRIBUTES = 'warningAttributes';

    public const AUTH_ID = 'AuthID';

    public const CONTINUE_URL = 'continueUrl';

    // STATE KEYS
    public const SAML_REQUESTED_AUTHN_CONTEXT = 'saml:RequestedAuthnContext';

    public const STATE_AUTHN_CONTEXT_CLASS_REF = 'AuthnContextClassRef';

    public const SAML_SP_SSO = 'saml:sp:sso';

    public const NAME = 'name';

    // VARIABLES

    private array $originalAuthnContextClassRef = [];

    private $wayfConfiguration;

    private Configuration $perunModuleConfiguration;

    private $proxyIdpEntityId;

    private $state;

    public function __construct(array $metadataSets, $instance)
    {
        //LOAD CONFIG FOR MODULE PERUN, WHICH CONTAINS WAYF CONFIGURATION
        try {
            $this->perunModuleConfiguration = Configuration::getConfig(self::CONFIG_FILE_NAME);
            $this->wayfConfiguration = $this->perunModuleConfiguration->getConfigItem(self::WAYF);
        } catch (\Exception $ex) {
            Logger::error("perun:disco-tpl: missing or invalid '" . self::CONFIG_FILE_NAME . "' config file");
            throw $ex;
        }

        if (!array_key_exists(self::RETURN, $_GET)) {
            throw new \Exception('Missing parameter: ' . self::RETURN);
        }
        $returnURL = HTTP::checkURLAllowed($_GET[self::RETURN]);

        parse_str(parse_url($returnURL)['query'], $query);

        if (isset($query[self::AUTH_ID])) {
            $id = explode(':', $query[self::AUTH_ID])[0];
            $state = State::loadState($id, self::SAML_SP_SSO, true);
            if (null !== $state) {
                if (isset($state[self::SAML_REQUESTED_AUTHN_CONTEXT][self::AUTHN_CONTEXT_CLASS_REF])) {
                    $this->originalAuthnContextClassRef = $state[self::SAML_REQUESTED_AUTHN_CONTEXT][self::AUTHN_CONTEXT_CLASS_REF];

                    $this->removeAuthContextClassRefWithPrefixes($state);
                    DiscoUtils::setUpstreamRequestedAuthnContext($state);
                    if (isset($state['IdPMetadata']['entityid'])) {
                        $this->proxyIdpEntityId = $state['IdPMetadata']['entityid'];
                    }
                    State::saveState($state, self::SAML_SP_SSO);
                }

                $e = explode('=', $returnURL)[0];
                $newReturnURL = $e . '=' . urlencode($id);
                $_GET[self::RETURN] = $newReturnURL;
            }
            $this->state = $state;
        }

        parent::__construct($metadataSets, $instance);
    }


    /**
     * Handles a request to this discovery service. It is entry point of Discovery service.
     *
     * The IdP disco parameters should be set before calling this function.
     */
    public function handleRequest()
    {
        $this->start();

        // IF IS SET AUTHN CONTEXT CLASS REF, REDIRECT USER TO THE IDP
        if (!empty($this->originalAuthnContextClassRef)) {
            // Check authnContextClassRef and select IdP directly if the correct value is set
            foreach ($this->originalAuthnContextClassRef as $value) {
                // VERIFY THE PREFIX IS CORRECT AND WE CAN PERFORM THE REDIRECT
                $acrStartSubstr = substr($value, 0, strlen(self::URN_CESNET_PROXYIDP_IDPENTITYID));
                if (self::URN_CESNET_PROXYIDP_IDPENTITYID === $acrStartSubstr) {
                    $idpEntityId = substr($value, strlen(self::URN_CESNET_PROXYIDP_IDPENTITYID), strlen($value));
                    if ($idpEntityId === $this->proxyIdpEntityId) {
                        continue;
                    }
                    Logger::info('Redirecting to ' . $idpEntityId);
                    $continueUrl = self::buildContinueUrl(
                        $this->spEntityId,
                        $this->returnURL,
                        $this->returnIdParam,
                        $idpEntityId
                    );
                    HTTP::redirectTrustedURL($continueUrl);
                    exit;
                }
            }
        }

        $continueUrl = self::buildContinueUrl(
                $this->spEntityId,
                $this->returnURL,
                $this->returnIdParam,
                !empty($this->state['aarc_hinted_idp']) ? $this->state['aarc_hinted_idp'] : self::LS_IDP
        );

        $warningInstance = WarningConfiguration::getInstance();
        $warningAttributes = $warningInstance->getWarningAttributes();

        $t = new DiscoTemplate($this->config);
        $t->data[self::WARNING_ATTRIBUTES] = $warningAttributes;
        $t->data[self::CONTINUE_URL] = $continueUrl;
        $t->show();
    }

   /**
     * @param $entityID
     * @param $return
     * @param $returnIDParam
     * @param $idpEntityId
     *
     * @return string url where user should be redirected when he choose idp
     */
    public static function buildContinueUrl(
        string $entityID,
        string $return,
        string $returnIDParam,
        string $idpEntityId
    ): string {
        return '?' .
            'entityID=' . urlencode($entityID) . '&' .
            'return=' . urlencode($return) . '&' .
            'returnIDParam=' . urlencode($returnIDParam) . '&' .
            'idpentityid=' . urlencode($idpEntityId);
    }

    /**
     * This method remove all AuthnContextClassRef which start with prefix from configuration.
     *
     * @param mixed $state
     */
    public function removeAuthContextClassRefWithPrefixes(&$state)
    {
        $prefixes = $this->wayfConfiguration->getArray(self::REMOVE_AUTHN_CONTEXT_CLASS_PREFIXES, []);

        if (empty($prefixes)) {
            return;
        }
        unset($state[self::SAML_REQUESTED_AUTHN_CONTEXT][self::STATE_AUTHN_CONTEXT_CLASS_REF]);
        $filteredAcrs = [];
        foreach ($this->originalAuthnContextClassRef as $acr) {
            $acr = trim($acr);
            $retain = true;
            foreach ($prefixes as $prefix) {
                if (substr($acr, 0, strlen($prefix)) === $prefix) {
                    $retain = false;
                    break;
                }
            }
            if ($retain) {
                $filteredAcrs[] = $acr;
            }
        }
        if (!empty($filteredAcrs)) {
            $state[self::SAML_REQUESTED_AUTHN_CONTEXT][self::STATE_AUTHN_CONTEXT_CLASS_REF] = $filteredAcrs;
        }
    }
}
