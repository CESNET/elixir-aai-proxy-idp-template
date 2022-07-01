<?php

declare(strict_types=1);

use SimpleSAML\Module\elixir\stats\Templates;
use SimpleSAML\Module\proxystatistics\Config;

Templates::showProviders(Config::MODE_SP, 2);
