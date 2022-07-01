<?php declare(strict_types=1);

use SimpleSAML\Module\elixir\stats\Templates;

$this->includeAtTemplateBase('includes/header-full.php');
?>
    <div class="row">
        <div class="col-12 col-md-8 order-1 order-md-0">
            <?php Templates::timeRange([
                'side' => $this->data['side'],
                'id' => $this->data['id'],
            ]); ?>
        </div>
        <div class="col-12 col-md-4 text-md-right order-0 order-md-1 mb-4 mb-md-0 ">
            <div class="go-to-stats-btn">
                <a href="./stats.php" class="btn btn-primary">
                    <?php echo $this->t('{proxystatistics:stats:back_to_stats}'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3><?php echo $this->t('{proxystatistics:stats:' . $this->data['side'] . 'Detail_dashboard_header}'); ?></h3>
            <p><?php echo $this->t('{proxystatistics:stats:' . $this->data['side'] . 'Detail_dashboard_legend}'); ?></p>
        </div>
        <div class="col-12">
            <?php Templates::loginsDashboard(); ?>
        </div>
    </div>

    <div class="row mt-5 mb-4">
        <div class="col-12">
            <h3><?php echo $this->t('{proxystatistics:stats:' . $this->data['side'] . 'Detail_graph_header}'); ?></h3>
            <p><?php echo $this->t('{proxystatistics:stats:' . $this->data['side'] . 'Detail_graph_legend}'); ?></p>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-md-8">
                    <?php Templates::pieChart('detail' . $this->data['other_side'] . 'Chart'); ?>
                </div>
                <div class="col-md-4">
                    <div id="detail<?php echo $this->data['other_side']; ?>Table" class="table-container"></div>
                </div>
            </div>
        </div>
    </div>
<?php
$this->includeAtTemplateBase('includes/footer-full.php');
