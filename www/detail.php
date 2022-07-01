<?php

declare(strict_types=1);

use SimpleSAML\Module\elixir\stats\Templates;
use SimpleSAML\Module\proxystatistics\Config;

if (empty($_GET['side']) || !in_array($_GET['side'], Config::SIDES, true)) {
    throw new \Exception('Invalid argument');
}
Templates::showDetail($_GET['side']);
