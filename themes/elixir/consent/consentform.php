<?php declare(strict_types=1);

use SimpleSAML\Module\elixir\ConsentHelper;

assert(is_array($this->data['dstMetadata']));
assert(is_string($this->data['yesTarget']));
assert(is_array($this->data['yesData']));
assert(is_array($this->data['attributes']));
assert(false === $this->data['sppp'] || is_string($this->data['sppp']));

$dstName = ConsentHelper::getDestinationName($this->data, $this);
$parsedJurisdiction = ConsentHelper::getJurisdiction($this->data['dstMetadata']);
$this->includeAtTemplateBase('includes/header.php');
?>
<div class="aas-message">
    <p>
        The service <strong><?php echo $dstName; ?></strong> requires access to your personal data.
        <?php
        if (false !== $this->data['sppp']) {
            echo 'Please, read the <a target="_blank" href="' . $this->data['sppp'] . '">Privacy Policy</a> of the service to learn more about its commitments to protect your data.';
        }
        ?>
    </p>
</div>
<?php
    ConsentHelper::printPrivacyPolicyWarning($this->data['sppp']);
    ConsentHelper::printAcceptedTosWarning($this->data['dstMetadata']);
?>
<form name="confirmationForm" id="yesform" class="form-group"
      action="<?php echo htmlspecialchars($this->data['yesTarget']); ?>" method="post">
    <div id="accordion">
        <div class="section">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        User Information
                    </button>
                </h5>
            </div>
        </div>
        <div class="section">
            <div id="collapseOne" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                <?php ConsentHelper::printUserAttributes($this->data['attributes'], $this->getTranslator()); ?>
            </div>
            <div class="card-header" id="headingThree">
                <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseOne">
                        Technical Information
                    </button>
                </h5>
            </div>
            <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                <?php ConsentHelper::printTechnicalAttributes($this->data['attributes'], $this->getTranslator()); ?>
            </div>
        </div>
    </div>
    <?php

    ConsentHelper::printJurisdictionWarning($parsedJurisdiction, $this->data['sppp']);

    foreach ($this->data['yesData'] as $name => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars(
            $value
        ) . '" />';
    }

    ?>
    <div class="footer-buttons">
        <div class="remember">
            <label>Remember:</label>
            <div id="select-amount">
                <label for="month"></label>
                <select name="saveconsent" id="month" class="btn btn-sm btn-secondary amount">
                    <option value="0">Just this time</option>
                    <option value="1">Forever</option>
                </select>
            </div>
        </div>
        <div class="consent-button">
            <a id="abort" class="btn btn-danger" href="https://lifescience-ri.eu/index.php?id=409">Abort</a>
            <input type="submit" class="btn btn-primary" name="yes"
                <?php echo empty($parsedJurisdiction) ? '' : 'disabled=""'; ?> value="Consent" id="submit">
        </div>
    </div>
</form>
<?php
$this->includeAtTemplateBase('includes/footer.php');
