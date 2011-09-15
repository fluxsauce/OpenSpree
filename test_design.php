<?php
/**
 * Design test.
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('includes.php');

global $_DESIGN;

$_DESIGN['title'] = 'OpenSpree';

include_once 'templates/header.php';

?>
<h2>Messages</h2>
<pre>OpenSpree_Design::msg</pre>
<?php
echo OpenSpree_Design::msg('error', 'Whoops.');
echo OpenSpree_Design::msg('ok', 'Yay!');
echo OpenSpree_Design::msg('question', 'Why?');
echo OpenSpree_Design::msg('warning', 'Uh...');
echo OpenSpree_Design::msg('info', 'By the way...');

include_once('templates/footer.php');
