<?php

declare(strict_types=1);

namespace SimpleSAML\Module\elixir;

use SimpleSAML\Configuration;
use SimpleSAML\XHTML\Template;

/**
 * This class extends basic SimpleSAML template class. It provides some utils functions used in templates specific for
 * Discovery services so template do not have to access directly $this->data field.
 *
 * Here should NOT be defined any view specific methods.
 */
class DiscoTemplate extends Template
{

    public const NAME = 'name';

    /**
     * sspmod_perun_DiscoTemplate constructor.
     *
     * @param Configuration $configuration of SimpleSAMLphp
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration, 'elixir:disco-tpl.php', 'disco');

        // Translate title in header
        $this->data['header'] = $this->t(isset($this->data['header']) ? $this->data['header'] : 'selectidp');
    }
}
