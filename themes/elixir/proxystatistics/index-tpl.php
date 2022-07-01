<?php

declare(strict_types=1);

use SimpleSAML\Module\proxystatistics\Config;

$this->data['header'] = 'LifeScience AAI Statistics';
$this->includeAtTemplateBase('includes/header-full.php');

?>

<div id="tabdiv" data-activetab="<?php echo htmlspecialchars(strval($this->data['tab'])); ?>">
    <ul class="tabset_tabs nav" width="100px">
        <li class="nav-item">
            <a class="nav-link" <?php echo $this->data['tabsAttributes']['PROXY']; ?>>
                <?php echo $this->t('{proxystatistics:stats:summary}'); ?>
            </a>
        </li>
        <?php foreach (Config::SIDES as $side) { ?>
        <li class="nav-item">
            <a class="nav-link" <?php echo $this->data['tabsAttributes'][$side]; ?>>
                <?php echo $this->t('{proxystatistics:stats:side' . $side . 'Detail}'); ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>

<?php
$this->includeAtTemplateBase('includes/footer-full.php');
?>
