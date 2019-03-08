<?php

use SimpleSAML\Module;

if (!empty($this->data['htmlinject']['htmlContentPost'])) {
    foreach ($this->data['htmlinject']['htmlContentPost'] as $c) {
        echo $c;
    }
}

?>

</div><!-- #content -->
</div><!-- #wrap -->

<div id="footer">

    <div style="margin: 0px auto; max-width: 1000px;">

        <div style="float: left;">
            <img src="<?php echo Module::getModuleUrl('elixir/res/img/logo_64.png') ?>">
        </div>

        <div style="float: left;">
            <p>ELIXIR, Wellcome Trust Genome Campus, Hinxton, Cambridgeshire, CB10 1SD, UK
                &nbsp; &nbsp; +44 (0)1223 492-670 &nbsp;
                <a href="mailto:info@elixir-europe.org">info@elixir-europe.org</a>
            </p>
            <p>Copyright © ELIXIR <?php echo date("Y"); ?> |
                <a href="https://www.elixir-europe.org/legal/privacy">Privacy</a> |
                <a href="https://www.elixir-europe.org/legal/cookies">Cookies</a> |
                <a href="https://www.elixir-europe.org/legal/terms-of-use">Terms of use</a>
            </p>
        </div>
    </div>

</div><!-- #footer -->

</body>
</html>
