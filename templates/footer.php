<?php
/**
 * Page Footer
 *
 * @package OpenSpree
 * @subpackage Templates
 * @author jpeck@fluxsauce.com
 */
?>
<div id="footer" class="shadow">
<div id="donate_to_cheapass">
  <p>Donate to Cheapass:</p>
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_new">
    <input name="cmd" value="_donations" type="hidden">
    <input name="business" value="rat@cheapass.com" type="hidden">
    <input name="lc" value="US" type="hidden">
    <input name="item_name" value="Cheapass Games: Spree" type="hidden">
    <input name="no_note" value="0" type="hidden">
    <input name="currency_code" value="USD" type="hidden">
    <input name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest" type="hidden">
    <input src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" type="image" border="0">
  </form>
</div>
<p>Spree! is &copy; and &trade; 1997, 2011 James Ernest and Cheapass Games: <a href="http://www.cheapass.com" title="Cheapass Games">www.cheapass.com</a>.</p>
  <p>Implemented with written permission from author James Ernest, OpenSpree is <a title="Attribution-NonCommercial-NoDerivs 3.0 Unported (CC BY-NC-ND 3.0)" href="http://creativecommons.org/licenses/by-nc-nd/3.0/"><img height="15" width="80" src="/assets/images/cc_by_nc_sa-80x15.png"></a> by Jon Peck and <a href="http://fluxsauce.com" title="FluxSauce - Web Application Development">FluxSauce</a>.  <a title="OpenSpree on GitHub" href="https://github.com/fluxsauce/OpenSpree">Source code and support &raquo;</a></p>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/openspree.js"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23720295-7']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
