<?php declare(strict_types=1);

use SimpleSAML\Module;

/*
 * Support the htmlinject hook, which allows modules to change header, pre and post body on all pages.
 */
$this->data['htmlinject'] = [
    'htmlContentPre' => [],
    'htmlContentPost' => [],
    'htmlContentHead' => [],
];

$jquery = [];
if (array_key_exists('jquery', $this->data)) {
    $jquery = $this->data['jquery'];
}

if (array_key_exists('pageid', $this->data)) {
    $hookinfo = [
        'pre' => &$this->data['htmlinject']['htmlContentPre'],
        'post' => &$this->data['htmlinject']['htmlContentPost'],
        'head' => &$this->data['htmlinject']['htmlContentHead'],
        'jquery' => &$jquery,
        'page' => $this->data['pageid'],
    ];

    Module::callHooks('htmlinject', $hookinfo);
}
// - o - o - o - o - o - o - o - o - o - o - o - o -

/*
 * Do not allow to frame SimpleSAMLphp pages from another location. This prevents clickjacking attacks in modern
 * browsers.
 *
 * If you don't want any framing at all you can even change this to 'DENY', or comment it out if you actually want to
 * allow foreign sites to put SimpleSAMLphp in a frame. The latter is however probably not a good security practice.
 */
header('X-Frame-Options: SAMEORIGIN');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?php echo Module::getModuleUrl('elixir/res/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo Module::getModuleUrl('elixir/res/css/eduteams.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo Module::getModuleUrl('elixir/res/css/cmservice.css'); ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/<?php echo $this->data['baseurlpath']; ?>resources/script.js"></script>
    <title><?php echo (array_key_exists('header', $this->data)) ? $this->data['header'] : 'SimpleSAMLphp'; ?></title>
    <?php
    if (!empty($this->data['htmlinject']['htmlContentHead'])) {
        foreach ($this->data['htmlinject']['htmlContentHead'] as $c) {
            echo $c;
        }
    }
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <?php
    if (array_key_exists('head', $this->data)) {
        echo '<!-- head -->' . $this->data['head'] . '<!-- /head -->';
    }
    ?>
</head>
<body>
<div class="row">
    <div class="offset-1 col-10 offset-sm-1 col-sm-10 offset-md-2 col-md-8 offset-lg-3 col-lg-6 offset-xl-3 col-xl-6">
        <div class="card">
            <img class="card-img-top" src="<?php echo Module::getModuleURL('elixir/res/img/lsaai_logo.png'); ?>" alt="Life Science Login logo">
            <div class="card-body">
                <?php
                if (isset($this->data['header'])) {
                    echo '<h1>' . PHP_EOL;
                    echo $this->data['header'];
                    echo '</h1>' . PHP_EOL;
                }
