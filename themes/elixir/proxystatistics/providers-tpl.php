<?php declare(strict_types=1);

use SimpleSAML\Module\elixir\stats\Templates;

?>

<div class="row">
    <div class="col-12"><?php Templates::timeRange([
        'tab' => $this->data['tab'],
    ]); ?></div>
</div>
<div class="row mt-4">
    <h3 class="col-12"><?php echo $this->t('{proxystatistics:stats:side_' . $this->data['side'] . 's}'); ?></h3>
    <p class="col-12"><?php Templates::showLegend($this, $this->data['side']); ?></p>
</div>
<div class="row tableMaxHehigh mt-4 mb-4">
    <div class="col-md-8">
        <?php
        Templates::pieChart($this->data['side'] . 'Chart');
        ?>
    </div>
    <div class="col-md-4">
        <div id="<?php echo $this->data['side']; ?>Table" class="table-container"></div>
    </div>
</div>
