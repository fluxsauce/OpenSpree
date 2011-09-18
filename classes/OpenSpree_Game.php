<?php
/**
 * OpenSpree Game
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Game - Game state
 *
 * @package OpenSpree
 */
class OpenSpree_Game {

  private $_deck;
  private $_board;
  private $_players = array();
  private $_player_order = array();
  private $_cars = array();
  private $_graph;
  private $_turn_current;
  private $_turn_history = array();

  public static $action_definitions = array(
    'move' => 'Move',
    'move_to' => 'Move to square',
    'shop' => 'Shop',
    'stash' => 'Stash your loot',
    'shoot' => 'Shoot another player',
    'end_turn' => 'End your turn',
    'cancel' => 'Cancel action',
    'move_modifier' => 'Play move modifier',
    'drive' => 'Drive',
    'reset' => 'Reset game',
  );

  public function getBoard() {
    return $this->_board;
  }

  public function getTurnCurrent() {
    return $this->_turn_current;
  }

  public function getPlayers() {
    return $this->_players;
  }

  public function getPlayerByColor($color) {
    return $this->_players[$color];
  }

  public function newTurn() {
    if (is_null($this->_turn_current)) {
      $player = $this->getPlayerByColor($this->_player_order[0]);
      $this->_turn_current = new OpenSpree_Turn($player->getColor(), 1, $this->_board, $this->_deck);
    } else {
      // $this->_turn_history[] = $this->_turn_current;
      $this->_turn_history[] = 'beer'; // I do not have a good reason to store all turns in the session table right now.
      $current_player_color = $this->_turn_current->getPlayerColor();
      $current_player_index = array_search($current_player_color, $this->_player_order);
      $this->drawToFive($this->_players[$current_player_color]);
      $next_player_index = $current_player_index + 1;
      if ($next_player_index >= count($this->_player_order)) {
        $next_player_index = 0;
      }
      $next_player = $this->getPlayerByColor($this->_player_order[$next_player_index]);
      $this->drawToFive($next_player);
      $this->_turn_current = new OpenSpree_Turn($next_player->getColor(), count($this->_turn_history) + 1, $this->_board, $this->_deck);
    }
  }

  function __construct($player_colors_and_names) {
    // Prepare the board
    $this->_board = new OpenSpree_Board();
    // Prepare deck
    $this->_deck = new OpenSpree_Deck();
    // Shuffle
    $this->_deck->shuffle();
    // Prepare players
    foreach ($player_colors_and_names as $color => $name) {
      $this->_players[$color] = new OpenSpree_Player($color, $name);
      $this->_cars[$color] = new OpenSpree_Car($color);
    }
    for ($card = 1; $card <= 5; $card++) {
      foreach ($this->_players as $player) {
        $player->takeCard($this->_deck->draw());
      }
    }
    // Randomize players
    $shuffled_players = $this->_players;
    shuffle($shuffled_players);
    foreach ($shuffled_players as $player) {
      $this->_player_order[] = $player->getColor();
    }
    // Set up Dijkstra graph
    $this->rebuildGraph();
    // Place the cars on the board
    foreach ($this->_players as $player) {
      $empty_parking_spaces = $this->_board->getEmptyParkingSpaces();
      $shortest_distance = 1000;
      $shortest_coordinate = '';
      foreach ($player->getHand() as $card) {
        foreach ($empty_parking_spaces as $empty_parking_space) {
          if (array_key_exists($card->__toString(), $this->_board->locations['cards'])) {
            // From
            $from = $empty_parking_space->toEdgeCoordinate();
            list($distances, $prev) = $this->_graph->paths_from($from);
            // To
            $to = OpenSpree_Square::convertCoordinateToEdge($this->_board->locations['cards'][$card->__toString()]);
            $path = $this->_graph->paths_to($prev, $to);
            $distance = $distances[$to];
            if ($distance < $shortest_distance) {
              $shortest_distance = $distance;
              $shortest_coordinate = $from;
            }
          }
        }
      }
      $shortest_hex_coordinate = OpenSpree_Square::convertEdgeToCoordinate($shortest_coordinate);
      // Add car to board
      $this->_board->squares[$shortest_hex_coordinate]->addCarColor($player->getColor());
      // Add player to board
      $this->_board->squares[$shortest_hex_coordinate]->addPlayerColor($player->getColor());
    }
    $this->_board->rebuildLocations();
  }

  public function rebuildGraph($exclude_edge_coordinates = array()) {
    $this->_graph = new Graph();
    if (!empty($exclude_edge_coordinates)) {
      foreach ($this->_board->toEdges() as $edge) {
        if (!in_array($edge[0], $exclude_edge_coordinates) && !in_array($edge[1], $exclude_edge_coordinates)) {
          $this->_graph->addedge($edge[0], $edge[1], 1);
        }
      }
    } else {
      foreach ($this->_board->toEdges() as $edge) {
        $this->_graph->addedge($edge[0], $edge[1], 1);
      }
    }
  }

  public function drawToFive(OpenSpree_Player $player) {
  	while (count($player->getHand()) < 5) {
  		$card = $this->_deck->draw();
  		$player->takeCard($card);
  	}
  	$this->_players[$player->getColor()] = $player;
  }

  public static function isValidColor($color) {
    return (in_array($color, array('blue', 'green', 'purple', 'red')));
  }

  public function getMovesFromSquare(OpenSpree_Square $square, $distance, $previously_visited_coordinates = array()) {
    $from_edge_coordinate = $square->toEdgeCoordinate();

    if (!empty($previously_visited_coordinates)) {
      $edge_coordinates = array();
      foreach ($previously_visited_coordinates as $coordinate) {
        $edge_coorindate = OpenSpree_Square::convertCoordinateToEdge($coordinate);
        if ($from_edge_coordinate != $edge_coorindate) {
          $edge_coordinates[] = $edge_coorindate;
        }
      }
      $this->rebuildGraph($edge_coordinates);
    } else {
      $this->rebuildGraph();
    }

    list($distances, $prev) = $this->_graph->paths_from($from_edge_coordinate);
    $moves = array();
    foreach ($distances as $edge_coordinate => $edge_coordinate_distance) {
      if (($edge_coordinate_distance > 0) && ($edge_coordinate_distance <= $distance)) {
        // Make sure the destination has not been visited...
        if (!isset($previously_visited_coordinates[OpenSpree_Square::convertEdgeToCoordinate($edge_coordinate)])) {
          // Make sure that no square along the path to the destination has not been visited
          $moves[OpenSpree_Square::convertEdgeToCoordinate($edge_coordinate)] = $edge_coordinate_distance;
        }
      }
    }
    return $moves;
  }

  public function getCoordinatePath(OpenSpree_Square $source_square, OpenSpree_Square $target_square) {
    list($distances, $prev) = $this->_graph->paths_from($source_square->toEdgeCoordinate());
    $path = $this->_graph->paths_to($prev, $target_square->toEdgeCoordinate());
    $coordinates = array();
    foreach ($path as $edge_coordinate) {
      $coordinates[] = OpenSpree_Square::convertEdgeToCoordinate($edge_coordinate);
    }
    return $coordinates;
  }

  public function playerShop(OpenSpree_Player $player, OpenSpree_Square $square) {
    // Assume they can shop
    $square_card = $square->getCard();
    $player->putCardInCart($square_card);
    $player->removeCard($square_card);
    $this->_players[$player->getColor()] = $player;
  }

  public function canPlayerUseModifier(OpenSpree_Player $player) {
    $hand = $player->getHand();
    if (empty($hand)) return FALSE;
    foreach ($hand as $card) {
      if (in_array($card->getNumber(), array('0', '2', 'U'))) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function canPlayerUseCardAsModifier(OpenSpree_Player $player, $card_string) {
    $hand = $player->getHand();
    if (empty($hand)) return FALSE;
    foreach ($hand as $card) {
      if ($card->getNumber() == 'U' && $card_string == 'U') {
        return TRUE;
      }
      if ($card->getSuit() == $card_string[0] && $card->getNumber() == $card_string[1]) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function playerUseModifier(OpenSpree_Player $player, $card_string) {
  	$hand = $player->getHand();
    foreach ($hand as $card) {
      if ($card->getNumber() == 'U' && $card_string == 'U') {
        $target_card = $card;
      }
      if ($card->getSuit() == $card_string[0] && $card->getNumber() == $card_string[1]) {
        $target_card = $card;
      }
    }
    if ('U' == $_REQUEST['card']) {
      if ('2' == $_REQUEST['distance']) {
        $this->_turn_current->setPlayerRollModifier(2);
      } elseif ('0' == $_REQUEST['distance']) {
        $this->_turn_current->setPlayerRollModifier(10);
      }
    } elseif ('2' == $_REQUEST['card'][1]) {
      $this->_turn_current->setPlayerRollModifier(2);
    } elseif ('0' == $_REQUEST['card'][1]) {
      $this->_turn_current->setPlayerRollModifier(10);
    }
    $player->removeCard($target_card);
    $this->_players[$player->getColor()] = $player;
  }

  public function canPlayerShop(OpenSpree_Player $player, OpenSpree_Square $square) {
    // Does the square have a card?
    $square_card = $square->getCard();
    if (!$square_card instanceof OpenSpree_Card) return FALSE;
    // Does the player have any cards?
    $hand = $player->getHand();
    if (empty($hand)) return FALSE;
    foreach ($hand as $card) {
      // If the card in hand is the card of the square...
      if ($card->getNumber() == $square_card->getNumber() && $card->getSuit() == $square_card->getSuit()) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function canPlayerRob(OpenSpree_Player $robber, OpenSpree_Player $victim) {
    // If robber is in a car, they cannot rob
    if ($robber->isInCar()) return FALSE;
    // If victim is in a car, they cannot be robbed
    if ($victim->isInCar()) return FALSE;
    // If robber has no cards, they cannot rob
    $robber_hand = $robber->getHand();
    if (empty($robber_hand)) return FALSE;
    // If victim has no cards, they cannot be robbed
    $victim_hand = $victim->getHand();
    if (empty($victim_hand)) return FALSE;
    // Determine location of robber
    $robber_square = $this->_board->squares[$this->_board->locations['players'][$victim->getColor()]];
    // Determine location of victim
    $victim_square = $this->_board->squares[$this->_board->locations['players'][$victim->getColor()]];
    // If they are in the same location, then a robbery can take place
    return ($robber_square->getCoordinates() == $victim_square->getCoordinates()) ? TRUE : FALSE;
  }

  public function robPlayerAttempt(OpenSpree_Player $robber, OpenSpree_Player $victim, OpenSpree_Card $card) {
    // Assume robbery can take place
    $victim_hand = $victim->getHand();
    foreach ($victim_hand as $victim_card) {
      if ($victim_card->getValue() == $card->getValue()) {
        $victim->removeCard($victim_card);
        $robber->takeCard($victim_card);
      }
    }
    $this->_players[$robber->getColor()] = $robber;
    $this->_players[$victim->getColor()] = $victim;
  }

  public function playerCarEnter(OpenSpree_Player $player) {
    // Assume player can enter their car
    // Get car location
    $car_square = $this->_board->squares[$this->_board->locations['cars'][$player->getColor()]];
    // Set player as in car
    $this->_cars[$player->getColor()]->setPlayerInCar(TRUE);
    // Remove player from the square
    if (isset($this->_board->locations['players'][$player->getColor()])) {
      $player_square = $this->_board->squares[$this->_board->locations['players'][$player->getColor()]];
      $player_square->removePlayerColor($player->getColor());
    }
    // Mark player as in car
    $player->setInCar(TRUE);
    $this->_players[$player->getColor()] = $player;
    // Reload board
    $this->_board->rebuildLocations();
  }

  public function playerCarExit(OpenSpree_Player $player) {
    // Assume player can exit their car
    // Get car location
    $car_square = $this->_board->squares[$this->_board->locations['cars'][$player->getColor()]];
    // Remove player from car
    $this->_cars[$player->getColor()]->setPlayerInCar(FALSE);
    // Mark player as not in car
    $player->setInCar(FALSE);
    // Add player to the board
    $car_square->addPlayerColor($player->getColor());
    $this->_players[$player->getColor()] = $player;
    // Reload board
    $this->_board->rebuildLocations();
  }

  public function canPlayerStash(OpenSpree_Player $player) {
    // If the player has nothing to stash, they can't stash
    if (0 == count($player->getShoppingCart())) {
      return FALSE;
    }
    // If the player is in their car, they can stash
    if ($player->isInCar()) {
      return TRUE;
    }
    // If the player is in the same space as their car, they can stash
    $car_square = $this->_board->squares[$this->_board->locations['cars'][$player->getColor()]];
    $player_square = $this->_board->squares[$this->_board->locations['players'][$player->getColor()]];
    return ($car_square->getCoordinates() == $player_square->getCoordinates()) ? TRUE : FALSE;
  }

  public function playerStash(OpenSpree_Player $player) {
  	$player->stashCart();
  	$this->_players[$player->getColor()] = $player;
  }

  public function canPlayerPlayStopCard(OpenSpree_Player $player) {
    // If player is knocked down, they cannot play a stop card
    if ($player->isKnockedDown()) return FALSE;
    // If player has no cards, they cannot play a stop card
    $hand = $player->getHand();
    if (empty($hand)) return FALSE;
    // Aces are stop cards, but Jokers can be played as an ace
    foreach ($hand as $card) {
      if (in_array($card->getNumber(), array('U', 'A'))) return TRUE;
    }
    // No stop cards found
    return FALSE;
  }

  public function whoCanPlayerShoot(OpenSpree_Player $player) {
    $potential_victims = array();
    // If player is knocked down, they cannot shoot
    if ($player->isKnockedDown()) return $potential_victims;
    // If player is in their car, they cannot shoot
    if ($player->isInCar()) return $potential_victims;
    // Determine where player is...
    $player_square = $this->_board->squares[$this->_board->locations['players'][$player->getColor()]];
    // Make a list of squares orthgraphically available that do not have any walls between them.
    for ($direction = 0; $direction < 4; $direction++) {
      $shootable_squares = $this->getShootableSquares($player_square, array(), $direction, 0, 6);
      if (!empty($shootable_squares)) {
        foreach ($shootable_squares as $shootable_square) {
          $square_player_colors = $shootable_square->getPlayerColors();
          if (!empty($square_player_colors)) {
            foreach ($square_player_colors as $potential_victim_color) {
              if (!$this->_players[$potential_victim_color]->isKnockedDown()) {
                $potential_victims[$potential_victim_color] = $this->_players[$potential_victim_color];
              }
            }
          }
        }
      }
    }
    return $potential_victims;
  }

  public function getShootableSquares(OpenSpree_Square $square, $shootable_squares = array(), $direction, $current_depth, $max_depth) {
    $potential_square = $this->getNextShootableSquare($square, $direction);
    if ($potential_square) {
      while ($potential_square instanceof OpenSpree_Square && $current_depth++ < $max_depth) {
        $shootable_squares = array_merge(array($potential_square->getCoordinates() => $potential_square), $shootable_squares);
        $potential_square = $this->getNextShootableSquare($potential_square, $direction);
      }
    }
    return $shootable_squares;
  }

  public function getNextShootableSquare(OpenSpree_Square $current_square, $direction) {
    // Cannot shoot through walls
    /*
     * @todo Exception for fountain
     */
    $walls = $current_square->getWalls();
    if ($walls[$direction]) return FALSE;
    // Find the next square
    $coordinates = $current_square->getCoordinates();
    $x = hexdec($coordinates[0]);
    $y = hexdec($coordinates[1]);
    switch ($direction) {
      case '0': {
        $y = $y - 1;
        break;
      }
      case '1': {
        $x = $x + 1;
        break;
      }
      case '2': {
        $y = $y + 1;
        break;
      }
      case '3': {
        $x = $x - 1;
        break;
      }
    }
    $potential_square_coordinate = dechex($x) . dechex($y);
    // Check if square exists
    if (isset($this->_board->squares[$potential_square_coordinate])) {
      return $this->_board->squares[$potential_square_coordinate];
    }
    return FALSE;
  }

  public function playerDraw(OpenSpree_Player $player) {
    $hand = $player->getHand();
    if (5 == count($hand)) return;
    $number_of_cards_to_draw = 5 - count($hand);
    for ($card = 1; $card <= $number_of_cards_to_draw; $card++) {
      $player->takeCard($this->_deck->draw());
    }
  }

  public function drivePlayer(OpenSpree_Player $player, $coordinate) {
  	// Player is in car
  	$this->playerCarEnter($player);
  	// Move car
  	$target_square = $this->_board->squares[$coordinate];
  	$current_square = $this->getPlayerCurrentSquare($player);
    // Remove player
    $current_square->removeCarColor($player->getColor());
    // Put player in new location
    $target_square->addCarColor($player->getColor());
    $this->_board->rebuildLocations();
  }

  public function canPlayerDrive(OpenSpree_Player $player) {
  	if ($this->_turn_current->hasPlayerMoved() || ($this->_turn_current->getPlayerRemainingMoves() > 0)) {
  		return FALSE;
  	}
  	if ($player->isInCar()) {
  		return TRUE;
  	}
  	$car_square = $this->_board->squares[$this->_board->locations['cars'][$player->getColor()]];
  	$player_square = $this->_board->squares[$this->_board->locations['players'][$player->getColor()]];
  	return ($car_square->getCoordinates() == $player_square->getCoordinates()) ? TRUE : FALSE;
  }

  public function getPlayerCurrentSquare(OpenSpree_Player $player) {
    if ($player->isInCar()) {
      return $this->_board->squares[$this->_board->locations['cars'][$player->getColor()]];
    } else {
      return $this->_board->squares[$this->_board->locations['players'][$player->getColor()]];
    }
  }

  public function playerAvailableActions(OpenSpree_Player $player) {
    $actions = array();
    // Moving
    $turn = $this->_turn_current;
    if (!$turn->hasPlayerRolled() || ($turn->hasPlayerRolled() && $turn->getPlayerRemainingMoves() > 0)) {
    	if ($turn->getPlayerRemainingMoves() > 0) {
	    	$all_moves_from_square = $this->getMovesFromSquare($this->getPlayerCurrentSquare($player), $turn->getPlayerRemainingMoves(), $turn->getPlayerMoveHistory());
	    	if (!empty($all_moves_from_square)) {
	    		$actions[] = 'move';
	    	}
    	} else {
    		$actions[] = 'move';
    	}
    }
    // Driving
    if ($this->canPlayerDrive($player)) {
      $actions[] = 'drive';
    }
    // Move modifier
    if ($this->canPlayerUseModifier($player)) {
      $actions[] = 'move_modifier';
    }
    // Shop
    if ($this->canPlayerShop($player, $this->getPlayerCurrentSquare($player))) {
      $actions[] = 'shop';
    }
    // Stashing
    if ($this->canPlayerStash($player)) {
      $actions[] = 'stash';
    }
    // Shooting
    $potential_victims = $this->whoCanPlayerShoot($player);
    if (!empty($potential_victims)) {
      $actions[] = 'shoot';
    }
    // End turn
    $actions[] = 'end_turn';
    return $actions;
  }

  public function renderActions($actions) {
    $html = '<ul>';
    $action_html = array();
    foreach ($actions as $action) {
      $move_html = '<a href="/?action=' . $action . '">' . OpenSpree_Game::$action_definitions[$action] . '</a>';
      if ($action == 'move') {
        if (!$this->_turn_current->hasPlayerRolled()) {
          $move_html .= ' (you have not rolled yet)';
        } else {
          $remaining_moves = $this->_turn_current->getPlayerRemainingMoves();
          $move_html .= ' (' . $remaining_moves . ' remaining move' . ($remaining_moves == 1 ? '' : 's') . ')';
        }
      }
      $action_html[] = $move_html;
    }
    $html .= '<li>' . implode('</li><li>', $action_html) . '</li>';
    $html .= '</ul>';
    return $html;
  }

  public function movePlayer(OpenSpree_Player $player, OpenSpree_Square $square) {
    $current_square = $this->getPlayerCurrentSquare($player);
    // Remove player
    $current_square->removePlayerColor($player->getColor());
    // Put player in new location
    $square->addPlayerColor($player->getColor());
    $this->_board->rebuildLocations();
  }
}

?>