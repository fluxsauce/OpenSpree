<?php
/**
 * Page Header
 *
 * @package OpenSpree
 * @subpackage Templates
 * @author jpeck@fluxsauce.com
 */

global $_DESIGN;

if (!$_DESIGN['title']) {
	$_DESIGN['title'] = 'OpenSpree';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title><?php echo $_DESIGN['title']; ?></title>
  <link rel="stylesheet" href="assets/css/design.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/global.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/board.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/players.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/actions.css" type="text/css" media="all" />
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>

<body>
<div id="heading" class="shadow">
  <div style="float:right;padding-right:1em;">
    <a href="http://fluxsauce.com" title="Powered by FluxSauce">
      <img src="assets/images/Logo-v01-20110608-135x30.png" style="border:none;" height="30" width="135" alt="FluxSauce"/>
    </a>
  </div>
  <h1><a style="color: white;text-decoration: none;" title="OpenSpree" href="/">OpenSpree</a></h1>
  <p><a href="http://cheapass.com/freegames/spree" title="Spree! It's a little like shopping.">Spree!</a> is a board game about looting a shopping mall, one of the original <a href="http://cheapass.com" title="Cheapass Games">Cheapass</a> envelope games.</p>
</div>