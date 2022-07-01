<?php declare(strict_types=1);

namespace SimpleSAML\Module\elixir\stats;

use SimpleSAML\Auth\Simple;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\XHTML\Template;

class Utils
{
    public static function theOther($arr, $val)
    {
        return current(array_diff($arr, [$val]));
    }

    public static function metaData($id, $data)
    {
        return '<meta name="' . $id . '" id="' . $id . '" ' .
            'content="' . htmlspecialchars(json_encode($data, JSON_NUMERIC_CHECK)) . '">';
    }
}
