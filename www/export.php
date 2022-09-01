<?php


use SimpleSAML\Module\proxystatistics\DatabaseCommand;

$dbCmd = new DatabaseCommand();
echo json_encode($dbCmd->getLoginCountPerDay(0), JSON_NUMERIC_CHECK);
