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
  $_DESIGN['title'] = 'openSpree';
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
  <link rel="stylesheet" href="assets/css/control_panel.css" type="text/css" media="all" />
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>

<body>
<table id="heading" class="group shadow rounded">
  <tr>
    <td class="logo"><a title="OpenSpree" href="/"><img style="border:none;" src="assets/images/openspree_logo_252x75.png" height="75" width="252" alt="OpenSpree"/></a></td>
    <td class="description"><p><i style="border-bottom: 1px dotted grey;">Springtime. Midnight. The Mall is beckoning.</i><br/><a href="http://cheapass.com/freegames/spree" title="Spree! It's a little like shopping.">Spree!</a> is a board game about looting a shopping mall, one of the original <a href="http://cheapass.com" title="Cheapass Games">Cheapass</a> envelope games.<br/><i style="border-top:1px dotted gray;font-size:80%;">Designed for and deployed exclusively on <a href="http://orchestra.io" title="Orchestra.io - PHP Platform as a service">Orchestra.io</a>.</i></p></td>
    <td class="fluxsauce"><a href="http://fluxsauce.com" title="Powered by FluxSauce"><img src="assets/images/fluxsauce_logo_206x65.png" style="border:none;" height="65" width="206" alt="FluxSauce"/></a></td>
  </tr>
</table>