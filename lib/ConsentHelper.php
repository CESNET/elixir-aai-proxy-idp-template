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
        if ('INT' === $jurisdiction) {
            return 'provided by an international organization.';
        }

        return 'in ' . $countryCodes[$jurisdiction];
    }

    public static function printUserAttributes(array $attributes, $getTranslator)
    {
        $newAttributes = [];
        foreach ($attributes as $name => $value) {
            if ($name === 'mail' || $name === 'displayname' || $name === 'givenName' || $name === 'sn') {
                $newAttributes[$name] = $value;
            }
        }
        ConsentHelper::printAttributes($newAttributes, $getTranslator);
    }

    public static function printTechnicalAttributes(array $attributes, $getTranslator)
    {
        $newAttributes = [];
        foreach ($attributes as $name => $value) {
            if ($name !== 'mail' && $name !== 'displayname' && $name !== 'givenName' && $name !== 'sn') {
                $newAttributes[$name] = $value;
            }
        }
        ConsentHelper::printAttributes($newAttributes, $getTranslator);
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
            echo '    <h6>This service is ' . $parsedJurisdiction . '</h6>' . PHP_EOL;
            echo '    <p>In order to access the requested services, the Life Science Login needs to transfer your personal data to a country outside EU/EEA. We cannot guarantee that this country offers an adequately high level of personal data protection as EU/EEA countries.</p>' . PHP_EOL;
            if (false !== $spPrivacyPolicy) {
                echo 'Please, read the <a target="_blank" href="' .$spPrivacyPolicy . '">Privacy Policy</a> of the service provider to learn more about its commitments to protect your data.' . PHP_EOL;
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
        if (empty($dstMetadata['accepted_tos'])) {
            echo '<div class="alert alert-warning" role="alert">' . PHP_EOL;
            echo '    <h6>This service has not declared compliance with the <a target="_blank" href="https://lifescience-ri.eu/aai/terms-of-use">Terms of Use for service providers</a> that govern the service\'s use of Life Science Login.</h6>' . PHP_EOL;
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