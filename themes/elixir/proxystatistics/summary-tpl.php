<?php declare(strict_types=1);

use SimpleSAML\Module\elixir\stats\Templates;
use SimpleSAML\Module\proxystatistics\Config;

?>

<div class="row">
    <div class="col-12"><?php Templates::timeRange([
        'tab' => $this->data['tab'],
    ]); ?></div>
</div>

<div class="row  mt-4">
    <h3 class="col-12"><?php echo $this->t('{proxystatistics:stats:graphs_logins}'); ?></h3>
    <p class="col-12"><?php echo $this->t('{proxystatistics:stats:summary_logins_info}'); ?></p>
    <div class="col-12">
        <?php Templates::loginsDashboard(); ?>
    </div>
</div>

<div class="row tableMaxHehigh mt-4 mb-4">
    <?php foreach (Config::SIDES as $side) { ?>
        <div class="<?php echo $this->data['summaryGraphs'][$side]['Providers']; ?>">
            <h3><?php echo $this->t('{proxystatistics:stats:side_' . $side . 's}'); ?></h3>
            <p><?php Templates::showLegend($this, $side); ?></p>
            <div>
                <?php Templates::pieChart($side . 'Chart'); ?>
            </div>
        </div>
    <?php } ?>
</div>
