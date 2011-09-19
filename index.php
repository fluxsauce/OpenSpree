<?php
/**
 * OpenSpree front conroller.
 *
 * @author jpeck@fluxsauce.com
 * @package OpenSpree
 *
 * @todo Robbery controls
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

$_DESIGN['title'] = 'openSpree';

include_once 'templates/header.php';

session_start();
$hide_board = FALSE;
$messages = array();

if (isset($_SESSION['game'])) {
  $game = $_SESSION['game'];
} else {
  $game = new OpenSpree_Game(array('red' => 'Jon', 'purple' => 'Sarah', 'blue' => 'Jeremiah', 'green' => 'Crystal'));
  $game->newTurn();
  $heading = 'Overview (only shows once per game):';
  $body = '<p>Springtime. Midnight. The Mall is beckoning.</p>';
  $body .= '<p>Welcome to LeGrand Mall, the oldest and most poorly secured shopping mall in the world. It\'s as leakproof as the Titanic and as solid as a wet paper bag. This place just begs to be robbed.</p>';
  $body .= '<p>Enter you. And your infantile collection of friends.</p>';
  $body .= '<p>It\'s not enough to simply loot this mall. You have to do it with "flair." Which boils down to an allnight looting race with shopping carts, flashlights, and guns.</p>';
  $body .= '<p>So park your car, dash into the mall, and load up your little red wagon. You can snitch stuff out of the stores if you like, but it\'s even more fun to steal it from your friends.</p>';
  $body .= '<p>This explains the guns.</p>';
  $body .= '<hr/>';
  $body .= '<p><i>2011.09.18 - This is a fairly complete hot-seat adaptation of <b>Spree!</b> implemented in object-oriented PHP and deployed to <a title="Orchestra.io - PHP platform as a service" href="http://orchestra.io">Orchestra.io</a>.  Some minor features and edge cases are missing, but the game is playable and winnable.  Keep checking back for new features like AI players, asynchronous play (less page loads), blood spatters, Hong Kong rules, new custom art, and more!  If you liked the game, donate to Cheapass games by using the donate button below.  Any feedback?  Use the support and feedback link at the bottom of the page, or contact me on Twitter at <a href="https://twitter.com/fluxsauce" title="@FluxSauce">@FluxSauce</a>.  Enjoy!  --Jon Peck, <a href="http://fluxsauce.com" title="FluxSauce - web application development">FluxSauce.com</a></i></p>';
  $body .= '<p><i>2011.09.15 - About 60% complete rule implementation, starting on user interface and interactions.</i></p>';
  $body .= '<p><i>2011.09.13 - Development started.</i></p>';
  $messages[] = OpenSpree_Design::msg('info', array('heading' => $heading, 'body' => $body));
}
$turn = $game->getTurnCurrent();

if (isset($_REQUEST['action'])) {
  switch ($_REQUEST['action']) {
    case 'reset': {
      session_destroy();
      echo OpenSpree_Design::msg('ok', array('heading' => 'The game was reset.', 'body' => '<div style="font-weight:bold;font-size:125%;"><a title="We\'ve only just begun..." href="?">Start a new game &raquo;</a></div>'));
      $hide_board = TRUE;
      break;
    }
  }
}
if (!$hide_board) {
  $board_modifier = array();
  $current_player = $game->getPlayerByColor($turn->getPlayerColor());
  $show_action_menu = $show_path = $end_turn = FALSE;
  $show_controls = TRUE;
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
        $control_message = OpenSpree_Design::msg('question', array('heading' => $heading, 'body' => '<ul><li>' . implode('</li><li>', $cards)));
      }
    }
    /*
     * Rob
     */
    if ('rob' == $_REQUEST['action']) {
      $hand = $current_player->getHand();
      $current_square = $game->getPlayerCurrentSquare($current_player);
      $current_player_color = $current_player->getColor();
      $victim_color = '';
      if (!isset($_REQUEST['v'])) {
        $square_colors = $current_square->getPlayerColors();
        unset($square_colors[$current_player_color]);
        if (count($square_colors) == 1) {
          $victim_color = array_pop($square_colors);
        } else {
          // TODO: Should be able to filter out those you can't rob from
          $player_select = '<ul>';
          foreach ($square_colors as $square_color) {
            $potential_victim = $game->getPlayerByColor($square_color);
            $player_select .= '<li><a href="/?action=rob&v=' . $square_color . '">' . $potential_victim->getName() . '</li>';
          }
          $player_select .= '</ul>';
          $control_message = OpenSpree_Design::msg('question', array('heading' => 'Who would you like to steal from?', 'body' => $html));
        }
      }
      if ($victim_color) {
        $victim = $game->getPlayerByColor($victim_color);
        $cards_to_steal = array();
        foreach ($hand as $card) {
          foreach ($victim->getHand() as $victim_card) {
            if ($card->getValue() == $victim_card->getValue()) {
              $cards_to_steal[$victim_card->getNumber() . $victim_card->getSuit()];
            }
          }
        }
        // Rob
        if (isset($_REQUEST['s']) && isset($_REQUEST['n'])) {
          if (!isset($cards_to_steal[$_REQUEST['n'] . $_REQUEST['s']])) {
            $show_action_menu = TRUE;
            $messages[] = OpenSpree_Design::msg('error', 'You don\'t have that card to play.');
          } else {

          }
        } else {
          // Which card?
          $html = '<ul>';
          foreach ($cards_to_steal as $card) {
            $html .= '<li><a href="/?action=rob&n=' . $card->getNumber() . '&s=' . $card->getSuit() . '">' . $card->toHtml() . '</li>';
          }
          $html .= '</ul>';
          $control_message = OpenSpree_Design::msg('question', array('heading' => 'Which card will you play from your hand to steal from their cart?', 'body' => $html));
        }
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
     * Shoot
     */
    if ('shoot' == $_REQUEST['action']) {
      $current_square = $game->getPlayerCurrentSquare($current_player);
      $potential_victims = $game->whoCanPlayerShoot($current_player);
      if (isset($_REQUEST['target'])) {
        if (array_key_exists($_REQUEST['target'], $potential_victims)) {
          $attempt = $game->playerShotAttempt($current_player, $game->getPlayerByColor($_REQUEST['target']));
          if ($attempt) {
            $messages[] = OpenSpree_Design::msg('ok', 'You hit the target, and they\'re down! Take another turn!');
          } else {
            $messages[] = OpenSpree_Design::msg('warning', array('heading' => 'Oh no!', 'body' => 'You missed the target and you fell over!'));
          }
          $end_turn = TRUE;
        } else {
          $messages[] = OpenSpree_Design::msg('error', 'You cannot shoot that target.');
        }
        $show_action_menu = TRUE;
      } else {
        $shootable_squares = array();
        foreach ($potential_victims as $potential_victim_color => $potential_victim) {
          $potential_victim_square = $game->getPlayerCurrentSquare($potential_victim);
          $shootable_squares[$potential_victim_square->getCoordinates()] = array(
            'color' => $potential_victim_color,
            'distance' => OpenSpree_Square::shootableSquareDistance($current_square->getCoordinates(), $potential_victim_square->getCoordinates()),
          );
        }
        $messages[] = OpenSpree_Design::msg('info', 'Click the victim that you want to try to shoot.');
        $board_modifier['shoot'] = $shootable_squares;
      }
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
    /*
     * Cancel
     */
    if ('cancel' == $_REQUEST['action']) {
      $messages[] = OpenSpree_Design::msg('ok', 'Action canceled.');
      $show_action_menu = TRUE;
    }
  }
  $who_won = $game->whoWon();
  if ($who_won instanceof OpenSpree_Player) {
    $body = $who_won->getName() . ' won!';
    $body .= '<br/>Would you like to play again?  <a style="font-weight:bold;font-size:125%;" href="/?action=reset" title="Reset game">Yes, of course &raquo;</a>';
    $messages[] = OpenSpree_Design::msg('ok', array('heading' => 'We have a winner!', 'body' => $body));
    $show_controls = FALSE;
  }
  // Messages
  if (!empty($messages)) {
    echo '<div style="margin-top: .5em; margin-right: 5px; margin-left: 5px;">' . implode("\n", $messages) . '</div>';
  }
  echo '<table id="control_panel">';
  echo '<tr>';
  echo '<td id="player_info" rowspan="3" class="rounded shadow">';
  echo '<div style="float:right;">' . OpenSpree_Design::playerAvatar($current_player->getColor()) . '</div>';
  echo '<h2 style="color:' . $current_player->getColor() . ';">' . $current_player->getName() . ' - Turn #' . $turn->getId() . '</h2>';
  // -- Hand
  echo '<div style="line-height:1.5em;"><b>Your hand:</b> ';
  $hand = $current_player->getHand();
  if (empty($hand)) {
    echo '<span style="color:grey;">Empty.</span>';
  } else {
    foreach ($hand as $card) {
      echo OpenSpree_Design::card($card);
    }
  }
  echo '</div>';
  // -- Cart
  echo '<div style="line-height:1.5em;"><b>Your cart:</b> ';
  $cart = $current_player->getShoppingCart();
  if (empty($cart)) {
    echo '<span style="color:grey;">Empty.</span>';
  } else {
    foreach ($cart as $card) {
      echo OpenSpree_Design::card($card);
    }
  }
  echo '</div>';
  // -- Score
  echo '<div style="line-height:1.5em;"><b>Your score:</b> ';
  $score = $current_player->getScore();
  $score_goal = $game::$target_scores[count($game->getPlayers())];
  $hint = 'Try robbing some other players.';
  if ($score == 0) {
    $hint = 'You should "Move" to yellow squares in stores - they contain the same card as your hand - and "Shop" them.';
  } else if ($score < ($score_goal / 4)) {
    $hint = 'Terrible, get some more cards.';
  } else if ($score < ($score_goal / 3)) {
    $hint = 'Respectible, but not enough... have you shot anyone yet?';
  } else if ($score < ($score_goal / 2)) {
    $hint = 'Half-way there, don\'t give up!  Remeber to stash often!';
  } else if ($score < ($score_goal - 25)) {
    $hint = 'Almost there, don\'t stray too far from your car!';
  }
  echo $current_player->getScore() . ' out of ' . $score_goal . '.';
  echo '</div>';
  if ($hint) {
    echo '<br/>' . OpenSpree_Design::msg('info', array('heading' => 'Hint:', 'body' => '<div style="font-size:75%;">' . $hint . '</div>'));
  }
  echo '</td>';
  echo '<td rowspan="3" class="rounded shadow">';
  if ($show_controls) {
    echo '<div style="font-size:125%;font-weight:bold;">Controls</div>';
    if ($show_action_menu || !isset($_REQUEST['action'])) {
      echo $game->renderActions($game->playerAvailableActions($current_player));
    } else {
      echo '<br/>';
      switch ($_REQUEST['action']) {
        case 'move' :{
          echo OpenSpree_Design::msg('question', array('heading' => 'Where would you like to move to?', 'body' => 'Please click on the square with a "Move" label on the board below.'));
          break;
        }
        case 'rob':
        case 'move_modifier': {
          if (isset($control_message)) {
            echo $control_message;
          }
          break;
        }
        default: {
          echo '<h3>' . OpenSpree_Game::$action_definitions[$_REQUEST['action']] . '...</h3>';
          break;
        }
      }
      echo '<div style="margin-top: 1em;" class="action_button shadow"><a href="/?action=cancel" title="Cancel action">Cancel</a></div>';
    }
  } else {
    echo '<i>If you liked this game or encountered any issues, please give feedback through the support link at the bottom of the page.</i>';
  }
  echo '</td>';

  $player_order = $game->getPlayerOrder();
  $ordered = array();
  // This is really hacky but I'm running out of time
  // Seriously, there's a lot of other really nice stuff in other places
  // IGNORE ME
  $current_player_index = array_search($current_player->getColor(), $player_order);
  $next_player_index = $current_player_index + 1;
  if ($next_player_index >= count($player_order)) {
    $next_player_index = 0;
  }
  $ordered[] = $game->getPlayerByColor($player_order[$next_player_index]);
  $next_player_index++;
  if ($next_player_index >= count($player_order)) {
    $next_player_index = 0;
  }
  $ordered[] = $game->getPlayerByColor($player_order[$next_player_index]);
  $next_player_index++;
  if ($next_player_index >= count($player_order)) {
    $next_player_index = 0;
  }
  $ordered[] = $game->getPlayerByColor($player_order[$next_player_index]);
  // It's over, sorry about that.
  foreach ($ordered as $player) {
    $html = '<div style="color:' . $player->getColor() . ';font-weight:bold;font-size:110%;">' . $player->getName() . '</div>';
    $html .= '<div style="line-height:1.5em;"><b>Cart:</b>';
    $cart = $player->getShoppingCart();
    if (empty($cart)) {
      $html .= '<span style="color:grey;">Empty.</span>';
    } else {
      foreach ($cart as $card) {
        $html .= OpenSpree_Design::card($card);
      }
    }
    $html .= '</div>';
    $html .= '<div style="line-height:1.5em;"><b>Score:</b> ' . $player->getScore() . ' out of ' . $score_goal . '.</div>';
    $ordered_html[] = $html;
  }
  echo '<td class="other_player rounded shadow"><div style="float: left;"><i>Next!</i></div>' . $ordered_html[0];
  echo '</td>';
  echo '</tr>';
  echo '<tr><td class="other_player rounded shadow">' . $ordered_html[1];
  echo '</td></tr>';
  echo '<tr><td class="other_player rounded shadow">' . $ordered_html[2];
  echo '</td></tr>';
  echo '</table>';
  // Board
  if ($show_path) {
    $board_modifier['path'] = $turn->getPlayerMoveHistory();
  }
  $board_modifier['hand'] = $current_player->getHand();
  $board_modifier['player_coordinates'] = $game->getPlayerCurrentSquare($current_player)->getCoordinates();
  $board_modifier['players'] = $game->getPlayers();
  // It works.  Take that, CSS purity.
  echo '<center>' . $game->getBoard()->toHtml($board_modifier) . '</center>';
  $_SESSION['game'] = $game;
}

include_once('templates/footer.php');