<?php

declare(strict_types=1);

use SimpleSAML\Module\elixir\Disco;
use SimpleSAML\Module\perun\model\WarningConfiguration;
use SimpleSAML\Utils\HTTP;

$warningAccepted = false;

if (isset($_POST['accepted'])) {
    $warningAccepted = true;
}

$idpEntityId = null;
$authContextClassRef = null;

$warningAttributes = $this->data[Disco::WARNING_ATTRIBUTES];
$continueUrl = $this->data[Disco::CONTINUE_URL];

$preventUserContinue = WarningConfiguration::WARNING_TYPE_ERROR === $warningAttributes->getType();

if (!$warningAttributes->isEnabled() || ($warningAccepted && !$preventUserContinue)) {
    HTTP::redirectTrustedURL($continueUrl);
}

if ($warningAttributes->isEnabled()) {
    $this->data['header'] = $this->t('{perun:disco:warning}');
}

$this->data['jquery'] = [
    'core' => true,
    'ui' => true,
    'css' => true,
];
$this->includeAtTemplateBase('includes/header.php');

if ($warningAttributes->isEnabled()) {
    $this->includeInlineTranslation('{perun:disco:warning_title}', $warningAttributes->getTitle());
    $this->includeInlineTranslation('{perun:disco:warning_text}', $warningAttributes->getText());
    if (WarningConfiguration::WARNING_TYPE_INFO === $warningAttributes->getType()) {
        echo '<div class="alert alert-info">';
    } elseif (WarningConfiguration::WARNING_TYPE_WARNING === $warningAttributes->getType()) {
        echo '<div class="alert alert-warning">';
    } elseif (WarningConfiguration::WARNING_TYPE_ERROR === $warningAttributes->getType()) {
        echo '<div class="alert alert-danger">';
    }
    echo '<h4><strong>' . $this->t('{perun:disco:warning_title}') . '</strong> </h4>';
    echo  $this->t('{perun:disco:warning_text}');
    echo '</div>';
    if (!$preventUserContinue) {
        echo '<form method="POST">';
        echo '<input class="btn btn-lg btn-primary btn-block" type="submit" name="accepted" value="Continue" />';
        echo '</form>';
    }
} else {
    $warningAccepted = true;
}

$this->includeAtTemplateBase('includes/footer.php');
