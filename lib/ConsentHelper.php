<?php

namespace SimpleSAML\Module\elixir;

class ConsentHelper
{

    public function __construct()
    {
    }

    const EU_EAA = [
        'AT' => 'Austria',
        'BE' => 'Belgium',
        'BG' => 'Bulgaria',
        'HR' => 'Croatia',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'EE' => 'Estonia',
        'FI' => 'Finland',
        'FR' => 'France',
        'DE' => 'Germany',
        'EL' => 'Greece',
        'HU' => 'Hungary',
        'IE' => 'Ireland',
        'IT' => 'Italy',
        'LV' => 'Latvia',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MT' => 'Malta',
        'NL' => 'Netherlands',
        'PT' => 'Portugal',
        'RO' => 'Romania',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'ES' => 'Spain',
        'SE' => 'Sweden',
        'NO' => 'Norway',
        'IS' => 'Iceland',
        'LI' => 'Liechtenstein',
        'GB' => 'United Kingdom',
    ];

    public static function getJurisdiction($dstMetadata): string
    {
        $countryCodes = json_decode(file_get_contents('http://country.io/names.json'), true);
        $jurisdiction = empty($dstMetadata['jurisdiction']) ? '' : $dstMetadata['jurisdiction'];
        if (empty($jurisdiction) || array_key_exists($jurisdiction, self::EU_EAA)) {
            return '';
        }
        if ('INT' === $jurisdiction || 'EMBL' === $jurisdiction) {
            return $jurisdiction;
        }

        return 'in ' . $countryCodes[$jurisdiction];
    }

    public static function printUserAttributes(array $attributes, $translator)
    {
        $newAttributes = [];
        foreach ($attributes as $name => $value) {
            if ($name === 'mail' || $name === 'displayname' || $name === 'givenName' || $name === 'sn') {
                $newAttributes[$name] = $value;
            }
        }
        ConsentHelper::printAttributes($newAttributes, $translator);
    }

    public static function printTechnicalAttributes(array $attributes, $translator)
    {
        $newAttributes = [];
        foreach ($attributes as $name => $value) {
            if ($name !== 'mail' && $name !== 'displayname' && $name !== 'givenName' && $name !== 'sn') {
                $newAttributes[$name] = $value;
            }
        }
        ConsentHelper::printAttributes($newAttributes, $translator);
    }

    public static function printAttributes($attributes, $translator)
    {
        echo '<div class="card-body">' . PHP_EOL;
        foreach ($attributes as $name => $value) {
            $name = $translator->getAttributeTranslation($name);
            echo '    <div class="attribute-row">' . PHP_EOL;
            echo '        <div class="attribute">' . PHP_EOL;
            echo '            <div class="attribute-name">' . PHP_EOL;
            echo                  '<p>' . htmlspecialchars($name) . '</p>' . PHP_EOL;
            echo '            </div>' . PHP_EOL;
            echo '        </div>' . PHP_EOL;
            echo '        <div class="attribute-values">' . PHP_EOL;
            if (count($value) > 0) {
                foreach ($value as $subValue) {
                    echo '<div class="attribute-choose">' . PHP_EOL;
                    echo '    <div class="attribute-value">' . PHP_EOL;
                    echo '        <code>' . htmlspecialchars($subValue) . '</code>';
                    echo '    </div>' . PHP_EOL;
                    echo '</div>' . PHP_EOL;
                }
            } else {
                echo '<div class="attribute-choose">' . PHP_EOL;
                echo '    <div class="attribute-value">' . PHP_EOL;
                echo '        <code>-</code>';
                echo '    </div>' . PHP_EOL;
                echo '</div>' . PHP_EOL;
            }
            echo '        </div>' . PHP_EOL;
            echo '    </div>' . PHP_EOL;
        }
        echo '</div>' . PHP_EOL;
    }

    public static function printJurisdictionWarning(string $parsedJurisdiction, $spPrivacyPolicy)
    {
        if (!empty($parsedJurisdiction)) {
            echo '<div class="alert alert-danger" role="alert">' . PHP_EOL;
            if ($parsedJurisdiction === 'INT' || $parsedJurisdiction === 'EMBL') {
                echo '    <h6>This service is provided by an international organization.</h6>' . PHP_EOL;
            } else {
                echo '    <h6>This service is ' . $parsedJurisdiction . '</h6>' . PHP_EOL;
            }
            if ($parsedJurisdiction === 'EMBL') {
                echo '    <p>In order to access the requested services, the Life Science Login needs to transfer your personal data to an international organization outside EU/EEA jurisdictions.<br/><i>Please be aware that upon transfer your personal data will be protected by <a href="https://www.embl.org/documents/document/internal-policy-no-68-on-general-data-protection/" target="_blank">EMBL’s Internal Policy 68 on General Data Protection</a>.</i>' . PHP_EOL;
            } else {
                echo '    <p>In order to access the requested services, the Life Science Login needs to transfer your personal data to a country outside EU/EEA. We cannot guarantee that this country offers an adequately high level of personal data protection as EU/EEA countries.</p>' . PHP_EOL;
            }
            if (false !== $spPrivacyPolicy) {
                echo '<h6>Please, read the <a target="_blank" href="' .$spPrivacyPolicy . '">Privacy Policy</a> of the service provider to learn more about its commitments to protect your data.' . PHP_EOL;
            }
            echo '    <div class="form-check">' . PHP_EOL;
            echo '        <input class="form-check-input" type="checkbox" name="transfer" id="transfer" data-np-checked="1">' . PHP_EOL;
            echo '        <label class="form-check-label" for="transfer">To continue, consent to the transfer of your personal data.</label>' . PHP_EOL;
            echo '    </div>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
    }

    public static function printPrivacyPolicyWarning($spPrivacyPolicy)
    {
        if (false === $spPrivacyPolicy) {
            echo '<div class="alert alert-warning" role="alert">' . PHP_EOL;
            echo '    <h6>This service is missing a Privacy Policy document.</h6>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
    }

    public static function printAcceptedTosWarning($dstMetadata)
    {
        if ((!empty($dstMetadata['test.sp']) && $dstMetadata['test.sp']) || empty($dstMetadata['accepted_tos'])) {
            echo '<div class="alert alert-warning" role="alert">' . PHP_EOL;
            echo '    <p>You are entering a service that is in the test environment of Life Science Login. The test environment is for service developers to test their relying service’s AAI integration before requesting to move them to the Life Science Login production environment.</p>' . PHP_EOL;
            echo '    <p>The test environment is not intended for common users. You are able to access the service because you have opted in as a test user. You need to refresh your registration every 30 days.</p>' . PHP_EOL;
            echo '</div>' . PHP_EOL;
        }
    }

    public static function getDestinationName($templateData, $t)
    {
        if (isset($templateData['dstMetadata']['UIInfo']['DisplayName'])) {
            $dstName = $templateData['dstMetadata']['UIInfo']['DisplayName'];
        } elseif (array_key_exists('name', $templateData['dstMetadata'])) {
            $dstName = $templateData['dstMetadata']['name'];
        } elseif (array_key_exists('OrganizationDisplayName', $templateData['dstMetadata'])) {
            $dstName = $templateData['dstMetadata']['OrganizationDisplayName'];
        } else {
            $dstName = $templateData['dstMetadata']['entityid'];
        }

        if (is_array($dstName)) {
            $dstName = $t->t($dstName);
        }

        return htmlspecialchars($dstName);

    }

}