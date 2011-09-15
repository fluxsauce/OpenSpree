<?php
/**
 * OpenSpree Square
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Square
 *
 * @package OpenSpree
 */
class OpenSpree_Square {

  private $type;
  private $coordinates;
  private $card;
  private $cars = array();
  private $players = array();
  private $walls = array(
    FALSE,
    FALSE,
    FALSE,
    FALSE,
  );

  public function addCar(OpenSpree_Car $car) {
    $this->cars[] = $car;
  }

  public function getCars() {
    return $this->cars;
  }

  public function getWalls() {
    return $this->walls;
  }

  public function addPlayer(OpenSpree_Player $player) {
    $this->players[] = $player;
  }

  public function removePlayer(OpenSpree_Player $player) {
    $new_players = array();
    foreach ($this->players as $existing_player) {
      if ($existing_player->getColor() != $player->getColor()) {
        $new_players[] = $existing_player;
      }
    }
    $this->players = $new_players;
  }

  public function getCar(OpenSpree_Player $player) {
    foreach ($this->cars as $existing_car) {
      if ($existing_car->getColor() == $player->getColor()) {
        return $existing_car;
      }
    }
  }

  public function removeCar(OpenSpree_Car $car) {
    $new_cars = array();
    foreach ($this->cars as $existing_car) {
      if ($existing_car->getColor() != $car->getColor()) {
        $new_cars[] = $existing_car;
      }
    }
    $this->cars = $new_cars;
  }

  public function getPlayers() {
    return $this->players;
  }

  public function getCoordinates() {
    return $this->coordinates;
  }

  public function getType() {
    return $this->type;
  }

  public function getCard() {
    return $this->card;
  }

  public static $valid_types = array(
    'shop',
    'open',
    'parking_lot',
    'parking_space',
    'fountain',
  );

  public function __construct($type, $coordinates, $card = NULL, $walls = NULL) {
    if (!$this->isValidType($type)) {
      throw new Exception('Invalid square type; cannot construct square.');
    }
    $this->type = $type;
    if (!$this->isValidCoordinates($coordinates)) {
      throw new Exception('Invalid coordinates; cannot construct square.');
    }
    $this->coordinates = $coordinates;
    if (!is_null($card)) {
      if (!($card instanceof OpenSpree_Card)) {
        throw new Exception('Invalid card; cannot contruct square.');
      }
      $this->card = $card;
    }
    if (!is_null($walls)) {
      if (!$this->isValidWalls($walls)) {
        throw new Exception('Invalid walls; cannot contruct square.');
      }
      $this->walls = $walls;
    }
  }

  public function __toString() {
    // return $this->type . ' (' . hexdec($this->coordinates[0]) . ', ' . hexdec($this->coordinates[1]) . ')';
    return '(' . hexdec($this->coordinates[0]) . ', ' . hexdec($this->coordinates[1]) . ')';
  }

  public function toTableCell($board_modifier = array()) {
    $classes = array($this->type);
    if (TRUE == $this->walls[0]) $classes[] = 'w0';
    if (TRUE == $this->walls[1]) $classes[] = 'w1';
    if (TRUE == $this->walls[2]) $classes[] = 'w2';
    if (TRUE == $this->walls[3]) $classes[] = 'w3';

    $html = '<td id="' . $this->coordinates . '" class="' . implode(' ', $classes) . '">';
    if (isset($board_modifier['moves'])) {
      if (in_array($this->coordinates, $board_modifier['moves'])) {
        $html .= '<div class="action">[<a href="?action=move_to&target=' . $this->coordinates . '">Move</a>]</div>';
      }
    }
    if (!empty($this->cars)) {
      foreach ($this->cars as $car) {
        $html .= '<img src="/assets/images/car_' . $car->getColor() . '_40x30.png"/><br/>';
      }
    }
    if (!empty($this->players)) {
      foreach ($this->players as $player) {
        $html .= '<img src="/assets/images/sc_' . $player->getColor() . '_30x30.png"/><br/>';
      }
    }
    $html .= '<div style="font-size:8px;">' . $this . '</div>';
    if ($this->card instanceof OpenSpree_Card) {
      $html .= '<span class="card">' . $this->card->toHtml() . '</span>';
    }
    $html .= '</td>';
    return $html;
  }

  public static function isValidType($type) {
    return in_array($type, OpenSpree_Square::$valid_types);
  }

  public static function isValidCoordinates($coorindates) {
    $valid_x = OpenSpree_Square::isValidXCoordinate($coorindates[0]);
    $valid_y = OpenSpree_Square::isValidYCoordinate($coorindates[1]);
    return ($valid_x && $valid_y);
  }

  public static function isValidXCoordinate($x_coorindate_hex) {
    $x_coordinate_dec = hexdec($x_coorindate_hex);
    return ((0 <= $x_coordinate_dec) && (14 >= $x_coordinate_dec));
  }

  public static function isValidYCoordinate($y_coorindate_hex) {
    $y_coordinate_dec = hexdec($y_coorindate_hex);
    return ((0 <= $y_coordinate_dec) && (8 >= $y_coordinate_dec));
  }

  public static function isValidWalls($walls) {
    if (!is_array($walls)) return false;
    if (4 != count($walls)) return false;
    foreach ($walls as $wall) {
      if (!is_bool($wall)) return false;
    }
    return true;
  }

  public static function convertCoordinateToEdge($coordinate) {
    $x = hexdec($coordinate[0]);
    $y = hexdec($coordinate[1]);
    return $x . 'x' . $y;
  }

  public static function convertEdgeToCoordinate($edge) {
    $edge_coorindate = explode('x', $edge);
    return dechex($edge_coorindate[0]) . dechex($edge_coorindate[1]);
  }

  public function toEdgeCoordinate() {
    return OpenSpree_Square::convertCoordinateToEdge($this->coordinates);
  }

  public function toEdges() {
    $x = hexdec($this->coordinates[0]);
    $y = hexdec($this->coordinates[1]);
    $edges = array();
    if (!$this->walls[0]) {
      $edges[] = array($this->toEdgeCoordinate(), $x . 'x' . ($y - 1));
    }
    if (!$this->walls[1]) {
      $edges[] = array($this->toEdgeCoordinate(), ($x + 1) . 'x' .  $y);
    }
    if (!$this->walls[2]) {
      $edges[] = array($this->toEdgeCoordinate(), $x . 'x' .  ($y + 1));
    }
    if (!$this->walls[3]) {
      $edges[] = array($this->toEdgeCoordinate(), ($x - 1) . 'x' .  $y);
    }
    return $edges;
  }

}

?>