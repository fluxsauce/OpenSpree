<?php
/**
 * OpenSpree front conroller.
 *
 * @author jpeck@fluxsauce.com
 * @package OpenSpree
 *
 * @todo Driving controls
 * @todo Robbery controls
 * @todo Shoot someone through the fountain
 * @todo Knock someone back through the fountain
 * @todo Stop cards
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
  'body' => 'OpenSpree is undergoing development; at this time, it is approximately 83% functionally complete.  It is not quite possible to complete a game yet.',
));

session_start();
$hide_board = FALSE;

if (isset($_SESSION['game'])) {
  $game = $_SESSION['game'];
} else {
  $game = new OpenSpree_Game(array('red' => 'Jon', 'purple' => 'Sarah', 'blue' => 'Ed', 'green' => 'Crystal'));
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
  $current_player = $game->getPlayerByColor($turn->getPlayerColor());
  $messages = array();
  $show_action_menu = $show_path = $end_turn = FALSE;
  // Actions
	if (isset($_REQUEST['action'])) {
		/**
		 * Move to
		 */
    if ('move_to' == $_REQUEST['action']) {
    	$show_path = TRUE;
      $target_coordinates = $_REQUEST['target'];
      $source_square = $game->getPlayerCurrentSquare($current_player);
      $all_moves_from_square = $game->getMovesFromSquare($source_square, $turn->getPlayerRemainingMoves(), $turn->getPlayerMoveHistory());
      if (array_key_exists($target_coordinates, $all_moves_from_square)) {
      	$target_square = $game->getBoard()->squares[$target_coordinates];
        // Make the move
        $game->movePlayer($current_player, $target_square);
        $turn->addPlayerMove($game->getCoordinatePath($source_square, $target_square));
        $moved_message = 'Moved ' . $all_moves_from_square[$target_coordinates] . ' square' . ($all_moves_from_square[$target_coordinates] == 1 ? '' : 's') . '.';
        $messages[] = OpenSpree_Design::msg('ok', $moved_message);
        $show_action_menu = TRUE;
      } else {
        $messages[] = OpenSpree_Design::msg('error', 'Illegal move.');
      }
    }
    /**
     * Move modifier
     */
    if ('move_modifier' == $_REQUEST['action']) {
    	$hand = $current_player->getHand();
    	if (isset($_REQUEST['card'])) {
    		$show_action_menu = TRUE;
    		if ($game->canPlayerUseCardAsModifier($current_player, $_REQUEST['card'])) {
    			$game->playerUseModifier($current_player, $_REQUEST['card']);
    			if ('U' == $_REQUEST['card']) {
    				if ('2' == $_REQUEST['distance']) {
    					$messages[] = OpenSpree_Design::msg('ok', 'Paid Joker for two more moves.');
    					$turn->setPlayerRollModifier(2);
    				} elseif ('10' == $_REQUEST['distance']) {
    					$messages[] = OpenSpree_Design::msg('ok', 'Paid Joker for ten more moves.');
    					$turn->setPlayerRollModifier(10);
    				}
    			} elseif ('2' == $_REQUEST['card'][1]) {
    				$messages[] = OpenSpree_Design::msg('ok', 'Paid for two more moves.');
    				$turn->setPlayerRollModifier(2);
    			} elseif ('0' == $_REQUEST['card'][1]) {
    				$messages[] = OpenSpree_Design::msg('ok', 'Paid for ten more moves.');
    				$turn->setPlayerRollModifier(10);
    			}
    		} else {
    			$messages[] = OpenSpree_Design::msg('error', 'You cannot use that card.');
    		}
    	} else {
    		$heading = 'Select the card that you want to play:';
    		$cards = array();
	    	foreach ($hand as $card) {
	        if (in_array($card->getNumber(), array('0', '2', 'U'))) {
	        	if ('U' == $card->getNumber()) {
	        		$cards[] = '<a href="/?action=move_modifier&card=U&distance=2">Pay Joker (wild) for two more moves</a>';
	        		$cards[] = '<a href="/?action=move_modifier&card=U&distance=10">Pay Joker (wild) for ten more moves</a>';
	        	}
	        	else {
		        	$html = '<a href="/?action=move_modifier&card=' . $card->getSuit() . $card->getNumber() . '">';
		          switch ($card->getNumber()) {
		            case '0': {
		              $html .= 'Pay ' . $card->toHtml() . ' for ten more moves';
		              break;
		            }
		            case '2': {
		              $html .= 'Pay ' . $card->toHtml() . ' for two more moves';
		              break;
		            }
		          }
		          $html .= '</a>';
		          $cards[] = $html;
	        	}
	        }
	      }
	      $messages[] = OpenSpree_Design::msg('question', array('heading' => $heading, 'body' => '<ul><li>' . implode('</li><li>', $cards)));
    	}
    }
    /*
     * Move
     */
    if ('move' == $_REQUEST['action']) {
      if ($turn->hasPlayerRolled()) {
        $player_roll = $turn->getPlayerRoll();
      } else {
        $player_roll_first = OpenSpree_Dice::roll();
        $player_roll_second = OpenSpree_Dice::roll();
        $turn->setPlayerRoll($player_roll_first + $player_roll_second);
        $messages[] = OpenSpree_Design::msg('info', 'You rolled a ' . $player_roll_first . ' and a ' . $player_roll_second . '.');
      }
      $hand = $current_player->getHand();
      $all_moves_from_square = $game->getMovesFromSquare($game->getPlayerCurrentSquare($current_player), $turn->getPlayerRemainingMoves(), $turn->getPlayerMoveHistory());
      $board_modifier['moves'] = $all_moves_from_square;
      $show_path = TRUE;
    }
    /*
     * Drive
     */
    if ('drive' == $_REQUEST['action']) {
    	if ($game->canPlayerDrive($current_player)) {
	    	$available_spots = $game->getBoard()->getEmptyParkingSpaces();
	    	if (!empty($available_spots)) {
		    	if (isset($_REQUEST['spot'])) {
		    		if (array_key_exists($_REQUEST['spot'], $available_spots)) {
		    			$messages[] = OpenSpree_Design::msg('ok', 'Vroom vroom, you\'ve moved to a new parking spot.');
		    			$game->drivePlayer($current_player, $_REQUEST['spot']);
		    			$turn->setPlayerDrove(TRUE);
		    			$end_turn = TRUE;
		    			$show_action_menu = TRUE;
		    		} else {
		    			$messages[] = OpenSpree_Design::msg('error', 'Invalid parking spot, cannot drive.');
		    			$show_action_menu = TRUE;
		    		}
		    	} else {
		    		$messages[] = OpenSpree_Design::msg('info', 'Click the parking spot that you want to move to.');
		    		$board_modifier['drive'] = $available_spots;
		    	}
	    	} else {
	    		$messages[] = OpenSpree_Design::msg('error', 'No available parking spots, which probably means a game bug.');
	    		$show_action_menu = TRUE;
	    	}
    	} else {
    		$messages[] = OpenSpree_Design::msg('error', 'You are not allowed to drive.');
    		$show_action_menu = TRUE;
    	}
    }
    /*
     * Stash loot
     */
    if ('stash' == $_REQUEST['action']) {
    	if ($game->canPlayerStash($current_player)) {
    		$game->playerStash($current_player);
    		$messages[] = OpenSpree_Design::msg('ok', 'You stashed your loot!');
    	} else {
    		$messages[] = OpenSpree_Design::msg('error', 'You cannot stash at this time.');
    	}
    	$show_action_menu = TRUE;
    }
    /*
     * Shop
     */
    if ('shop' == $_REQUEST['action']) {
    	$current_square = $game->getPlayerCurrentSquare($current_player);
    	if ($game->canPlayerShop($current_player, $current_square)) {
    		$game->playerShop($current_player, $current_square);
    		$messages[] = OpenSpree_Design::msg('ok', 'You robbed the store!');
    	} else {
    		$messages[] = OpenSpree_Design::msg('error', 'You cannot shop at this time.');
    	}
    	$show_action_menu = TRUE;
    }
    /*
     * End turn
     */
    if (('end_turn' == $_REQUEST['action']) || $end_turn) {
    	$game->newTurn();
    	$turn = $game->getTurnCurrent();
    	$current_player = $game->getPlayerByColor($turn->getPlayerColor());
    	$messages[] = OpenSpree_Design::msg('ok', 'Turn complete.');
    	$show_action_menu = TRUE;
    }
  }
  // Players
  echo '<table class="players">';
  echo '<tr>';
  foreach ($game->getPlayers() as $player) {
    echo '<td class="shadow">' . $player . '</td>';
  }
  echo '</tr>';
  echo '</table>';
  // Messages
  if (!empty($messages)) {
  	echo '<div style="margin-top: .5em; margin-right: 5px; margin-left: 5px;">' . implode("\n", $messages) . '</div>';
  }
  // Turn
  echo '<table class="actions">';
  echo '<tr>';
  echo '<td class="shadow">';
  echo '<h2>Turn <span style="color:grey;">(debugging)</span></h2>';
  echo $turn;
  // Actions
  echo '<h2>Actions</h2>';
  if ($show_action_menu || !isset($_REQUEST['action'])) {
  	echo $game->renderActions($game->playerAvailableActions($current_player));
  } else {
  	// This should be nicer.
    echo OpenSpree_Game::$action_definitions[$_REQUEST['action']];
  }
  echo '</td>';
  echo '</tr>';
  echo '</table>';
  // Board
  if ($show_path) {
  	$board_modifier['path'] = $turn->getPlayerMoveHistory();
  }
  $board_modifier['hand'] = $current_player->getHand();
  $board_modifier['player_coordinates'] = $game->getPlayerCurrentSquare($current_player)->getCoordinates();
  echo $game->getBoard()->toHtml($board_modifier);
  $_SESSION['game'] = $game;
}

include_once('templates/footer.php');