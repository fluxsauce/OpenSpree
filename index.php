<?php
/**
 * OpenSpree front conroller.
 *
 * @author jpeck@fluxsauce.com
 * @package OpenSpree
 *
 * @todo Movement controls
 * @todo Driving controls
 * @todo Robbery controls
 * @todo Shoot someone through the fountain
 * @todo Knock someone back through the fountain
 * @todo Stop cards
 * @todo Player avatars
 * @todo Front controller or formal framework
 * @todo A.I. players
 * @todo Asynchronous communication
 * @todo Database logging
 * @todo Multiplayer
 * @todo CSS sprites
 * @todo Reimplement all art, including all variations to allow overlay effects
 * @todo Blood splatters (fade over turns)
 *
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('includes.php');

global $_DESIGN;

$_DESIGN['title'] = 'OpenSpree';

include_once 'templates/header.php';

echo OpenSpree_Design::msg('warning', array(
  'heading' => 'Work in progress.',
  'body' => 'OpenSpree is undergoing development; at this time, it is approximately 60% complete.  It is not possible to complete a game yet.',
));

session_start();
$hide_board = FALSE;

if (isset($_SESSION['game'])) {
  $game = $_SESSION['game'];
} else {
  $game = new OpenSpree_Game(array('Jon' => 'red', 'Sarah' => 'purple', 'Ed' => 'blue', 'Crystal' => 'green'));
  $game->newTurn();
}
$turn = $game->getTurnCurrent();

if (isset($_REQUEST['action'])) {
  switch ($_REQUEST['action']) {
    case 'reset': {
      session_destroy();
      echo '<h2>Game reset. <a href="?">refresh &raquo;</a></h2>';
      $hide_board = TRUE;
      break;
    }
  }
} else {
  echo '<div style="float:right;font-weight:bold;font-size:125%;">[<a title="Reset the game state." style="color:red;" href="?action=reset">RESET</a>]</div>';
}
if (!$hide_board) {
  $board_modifier = array();
  // Players
  echo '<h2>Players</h2>';
  echo '<table class="players">';
  echo '<tr>';
  foreach ($game->getPlayers() as $player) {
    echo '<td class="shadow">' . $player . '</td>';
  }
  echo '</tr>';
  echo '</table>';
  // Turn
  echo '<table class="actions">';
  echo '<tr>';
  echo '<td class="shadow">';
  echo '<h2>Turn <span style="color:grey;">(debugging)</span></h2>';
  echo $turn;
  // Actions
  echo '<h2>Actions</h2>';
  if (isset($_REQUEST['action'])) {
    echo $_REQUEST['action'];
  } else {
    echo $game->renderActions($game->playerAvailableActions($turn->getPlayer()));
  }
  echo '</td>';
  // Action workspace
  if (isset($_REQUEST['action'])) {
    echo '<td class="workspace shadow">';
    echo '<h2>Action workspace</h2>';
    if ('move_to' == $_REQUEST['action']) {
      $target = $_REQUEST['target'];
      $all_moves_from_square = $game->getMovesFromSquare($game->getPlayerCurrentSquare($turn->getPlayer()), $turn->getPlayerRemainingMoves());
      if (in_array($target, $all_moves_from_square)) {
        // Make the move
        // $turn->addPlayerMove($target);

      } else {
        // ERROR
      }
    }
    if ('move_modifier' == $_REQUEST['action']) {
      echo 'SELECT THE CARD THAT YOU WISH TO PLAY';
      /*
      foreach ($hand as $card) {
        if (in_array($card->getNumber(), array('0', '2', 'U'))) {
          switch ($card->getNumber()) {
            case 'U': {
              $bonus_distance = 10;
              break;
            }
            case '0': {
              $bonus_distance = 10;
              break;
            }
            case '2': {
              $bonus_distance = 2;
              break;
            }
          }
        }
      }
      */
    }
    if ('move' == $_REQUEST['action']) {
      echo '<div>';
      echo '<pre style="font-size:8px;">';
      if ($turn->hasPlayerRolled()) {
        $player_roll = $turn->getPlayerRoll();
      } else {
        $player_roll = OpenSpree_Dice::roll();
        $turn->setPlayerRoll($player_roll);
      }
      $hand = $turn->getPlayer()->getHand();
      $all_moves_from_square = $game->getMovesFromSquare($game->getPlayerCurrentSquare($turn->getPlayer()), $turn->getPlayerRemainingMoves());
      $board_modifier['moves'] = $all_moves_from_square;
      // DEBUG
      var_dump($all_moves_from_square);
      echo '</pre>';
      echo '</div>';
    }
    echo '</td>';
  }
  echo '</tr>';
  echo '</table>';
  // Board
  echo $game->getBoard()->toHtml($board_modifier);
  $_SESSION['game'] = $game;
}

include_once('templates/footer.php');