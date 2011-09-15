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

	private $deck;
	private $board;
	private $players = array();
	private $graph;
	private $_turn_current;
	private $_turn_history = array();

	public static $action_definitions = array(
	  'move' => 'Move',
	  'shop' => 'Shop',
	  'stash' => 'Stash your loot',
	  'shoot' => 'Shoot another player',
	  'end' => 'End your turn',
	  'cancel' => 'Cancel action',
	  'move_modifier' => 'Play move modifier',
	  'drive' => 'Drive',
	);

	function getBoard() {
		return $this->board;
	}

	function getTurnCurrent() {
		return $this->_turn_current;
	}

	function getPlayers() {
		return $this->players;
	}

	public function newTurn() {
		if (is_null($this->_turn_current)) {
			reset($this->players);
		  $this->_turn_current = new OpenSpree_Turn(current($this->players), 1, $this->board, $this->deck);
		} else {
			$this->_turn_history[] = $this->_turn_current;
			$next_player = next($this->players);
			if (FALSE === $next_player) {
				reset($this->players);
			}
			$this->_turn_current = new OpenSpree_Turn(current($this->players), count($this->_turn_history) + 1, $this->board, $this->deck);
		}
	}

	function __construct($player_names_and_colors) {
		// Prepare the board
		$this->board = new OpenSpree_Board();
		// Prepare deck
		$this->deck = new OpenSpree_Deck();
		// Shuffle
		$this->deck->shuffle();
		// Prepare players
		foreach ($player_names_and_colors as $name => $color) {
			$this->players[] = new OpenSpree_Player($name, $color);
		}
		for ($card = 1; $card <= 5; $card++) {
			foreach ($this->players as $player) {
				$player->takeCard($this->deck->draw());
			}
		}
		// Randomize players
		shuffle($this->players);
		// Set up Dijkstra graph
		$this->rebuildGraph();
		// Place the cars on the board
		foreach ($this->players as $player) {
			$empty_parking_spaces = $this->board->getEmptyParkingSpaces();
			$shortest_distance = 1000;
			$shortest_coordinate = '';
			foreach ($player->getHand() as $card) {
				foreach ($empty_parking_spaces as $empty_parking_space) {
					if (array_key_exists($card->__toString(), $this->board->locations['cards'])) {
						// From
						$from = $empty_parking_space->toEdgeCoordinate();
					  list($distances, $prev) = $this->graph->paths_from($from);
					  // To
					  $to = OpenSpree_Square::convertCoordinateToEdge($this->board->locations['cards'][$card->__toString()]);
						$path = $this->graph->paths_to($prev, $to);
						$distance = $distances[$to];
						if ($distance < $shortest_distance) {
							$shortest_distance = $distance;
							$shortest_coordinate = $from;
						}
					}
				}
			}
			$shortest_hex_coordinate = OpenSpree_Square::convertEdgeToCoordinate($shortest_coordinate);
			$this->board->squares[$shortest_hex_coordinate]->addCar(new OpenSpree_Car($player->getColor()));
		}
		$this->board->rebuildLocations();
	}

	public function rebuildGraph() {
		$this->graph = new Graph();
		foreach ($this->board->toEdges() as $edge) {
			$this->graph->addedge($edge[0], $edge[1], 1);
		}
	}

	public static function isValidColor($color) {
		return (in_array($color, array('blue', 'green', 'purple', 'red')));
	}

	public function getMovesFromSquare(OpenSpree_Square $square, $distance, $previously_visited = array()) {
		$from = $square->toEdgeCoordinate();
		list($distances, $prev) = $this->graph->paths_from($from);
		$moves = array();
		foreach ($distances as $edge_coordinate => $edge_coordinate_distance) {
			if ($edge_coordinate_distance > 0 && $edge_coordinate_distance <= $distance && !in_array($edge_coordinate, $previously_visited)) {
				$moves[OpenSpree_Square::convertEdgeToCoordinate($edge_coordinate)] = $edge_coordinate_distance;
			}
		}
		return $moves;
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
		$robber_square = $this->board->squares[$this->board->locations['players'][$victim->getColor()]];
		// Determine location of victim
		$victim_square = $this->board->squares[$this->board->locations['players'][$victim->getColor()]];
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
	}

	public function playerCarEnter(OpenSpree_Player $player) {
		// Assume player can enter their car
		// Get player car
		$car_square = $this->board->squares[$this->board->locations['cars'][$player->getColor()]];
		// Get car
		$car = $car_square->getCar($player);
		// Remove car from square
		$car_square->removeCar($car);
		// Set player as in car
		$car->setPlayerInCar(TRUE);
		// Add the car back
		$car_square->addCar($car);
		// Remove player from board
		$player_square = $this->board->squares[$this->board->locations['players'][$player->getColor()]];
		$player_square->removePlayer($player);
		// Mark player as in car
		$player->setInCar(TRUE);
		// Reload board
		$this->board->rebuildLocations();
	}

	public function playerCarExit(OpenSpree_Player $player) {
		// Assume player can exit their car
		// Get player car
		$car_square = $this->board->squares[$this->board->locations['cars'][$player->getColor()]];
		// Get car
		$car = $car_square->getCar($player);
		// Remove car from square
		$car_square->removeCar($car);
		// Remove player from car
		$car->setPlayerInCar(FALSE);
		// Add the car back
		$car_square->addCar($car);
		// Mark player as not in car
		$player->setInCar(FALSE);
		// Add player to the board
		$car_square->addPlayer($player);
		// Reload board
		$this->board->rebuildLocations();
	}

	public function canPlayerStash(OpenSpree_Player $player) {
		// If the player is in their car, they can stash
		if ($player->isInCar()) return TRUE;
		// If the player is in the same space as their car, they can stash
		$car_square = $this->board->squares[$this->board->locations['cars'][$player->getColor()]];
	  $player_square = $this->board->squares[$this->board->locations['players'][$player->getColor()]];
	  return ($car_square->getCoordinates() == $player_square->getCoordinates()) ? TRUE : FALSE;
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
		$player_square = $this->board->squares[$this->board->locations['players'][$player->getColor()]];
		// Make a list of squares orthgraphically available that do not have any walls between them.
		for ($direction = 0; $direction < 4; $direction++) {
			$shootable_squares = $this->getShootableSquares($player_square, array(), $direction, 0, 6);
			if (!empty($shootable_squares)) {
				foreach ($shootable_squares as $shootable_square) {
					$square_players = $shootable_square->getPlayers();
					if (!empty($square_players)) {
						foreach ($square_players as $potential_victim) {
							if (!$potential_victim->isKnockedDown()) {
							  $potential_victims[$potential_victim->getColor()] = $potential_victim;
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
		if (isset($this->board->squares[$potential_square_coordinate])) {
			return $this->board->squares[$potential_square_coordinate];
		}
		return FALSE;
	}

	public function playerDraw(OpenSpree_Player $player) {
		$hand = $player->getHand();
		if (5 == count($hand)) return;
		$number_of_cards_to_draw = 5 - count($hand);
	  for ($card = 1; $card <= $number_of_cards_to_draw; $card++) {
      $player->takeCard($this->deck->draw());
		}
	}

	public function getPlayerCurrentSquare(OpenSpree_Player $player) {
	  if ($player->isInCar()) {
			return $this->board->squares[$this->board->locations['cars'][$player->getColor()]];
		} else {
			return $this->board->squares[$this->board->locations['players'][$player->getColor()]];
		}
	}

	public function playerAvailableActions(OpenSpree_Player $player) {
		$actions = array();
		// Moving
		$actions[] = 'move';
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
		$actions[] = 'end';
		return $actions;
	}

	public function renderActions($actions) {
		$html = '<ul>';
		$action_html = array();
		foreach ($actions as $action) {
			switch ($action) {
				case 'move': {
					$action_html[] = '<a href="/?action=move">Move</a>';
					break;
				}
				case 'shop': {
					$action_html[] = '<a href="/?action=shop">Shop</a>';
					break;
				}
				case 'stash': {
					$action_html[] = '<a href="/?action=stash">Stash your loot</a>';
					break;
				}
				case 'shoot': {
					$action_html[] = '<a href="/?action=shoot">Shoot another player</a>';
					break;
				}
				case 'end': {
					$action_html[] = '<a href="/?action=end">End turn</a>';
					break;
				}
			}
		}
		$html .= '<li>' . implode('</li><li>', $action_html) . '</li>';
		$html .= '</ul>';
		return $html;
	}

	public function movePlayer(OpenSpree_Player $player, OpenSpree_Square $square) {

	}

}

?>