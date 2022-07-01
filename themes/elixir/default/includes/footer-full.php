<?php declare(strict_types=1);

use SimpleSAML\Module;

if (!empty($this->data['htmlinject']['htmlContentPost'])) {
    foreach ($this->data['htmlinject']['htmlContentPost'] as $c) {
        echo $c;
    }
}

?>

</div> <!-- ENDCOL -->
</div> <!-- ENDROW -->
<footer>
    <div class="footer offset-1 col-10 offset-sm-1 col-sm-10 offset-md-2 col-md-8 offset-lg-3 col-lg-6 offset-xl-3 col-xl-6">
        <div class="footer-contact">
            <a class="contact-link" href="mailto:support@aai.lifescience-ri.eu">Contact us</a>
        </div>
        <div class="footer-policy">
            <a class="footer-policy-link" href="https://lifescience-ri.eu/ls-login/ls-aai-aup.html">Privacy Policy</a>
        </div>
    </div>
</footer>
<script type="text/javascript" src="<?php echo Module::getModuleURL('elixir/res/js/jquery-3.5.1.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo Module::getModuleURL('elixir/res/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo Module::getModuleURL('elixir/res/js/cmservice.js'); ?>"></script>
<?php
    if (array_key_exists('scripts', $this->data)) {
        echo $this->data['scripts'];
    }
?>
</body>
</html>
