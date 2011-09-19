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

  private $_type;
  private $_coordinates;
  private $_card;
  private $_car_colors = array();
  private $_player_colors = array();
  private $_walls = array(
    FALSE,
    FALSE,
    FALSE,
    FALSE,
  );

  public static $valid_types = array(
    'shop',
    'open',
    'parking_lot',
    'parking_space',
    'fountain',
  );

  public function addCarColor($car_color) {
    $this->_car_colors[$car_color] = $car_color;
  }

  public function getCarColors() {
    return $this->_car_colors;
  }

  public function getWalls() {
    return $this->_walls;
  }

  public function addPlayerColor($player_color) {
    $this->_player_colors[$player_color] = $player_color;
  }

  public function removePlayerColor($player_color) {
    unset($this->_player_colors[$player_color]);
  }

  public function removeCarColor($car_color) {
    unset($this->_car_colors[$car_color]);
  }

  public function getPlayerColors() {
    return $this->_player_colors;
  }

  public function getCoordinates() {
    return $this->_coordinates;
  }

  public function getType() {
    return $this->_type;
  }

  public function getCard() {
    return $this->_card;
  }

  public function __construct($type, $coordinates, $card = NULL, $walls = NULL) {
    if (!$this->isValidType($type)) {
      throw new Exception('Invalid square type; cannot construct square.');
    }
    $this->_type = $type;
    if (!$this->isValidCoordinates($coordinates)) {
      throw new Exception('Invalid coordinates; cannot construct square.');
    }
    $this->_coordinates = $coordinates;
    if (!is_null($card)) {
      if (!($card instanceof OpenSpree_Card)) {
        throw new Exception('Invalid card; cannot contruct square.');
      }
      $this->_card = $card;
    }
    if (!is_null($walls)) {
      if (!$this->isValidWalls($walls)) {
        throw new Exception('Invalid walls; cannot contruct square.');
      }
      $this->_walls = $walls;
    }
  }

  public function __toString() {
    return '(' . hexdec($this->_coordinates[0]) . ', ' . hexdec($this->_coordinates[1]) . ')';
  }

  public function toTableCell($board_modifier = array()) {
    $classes = array($this->_type);
    if (TRUE == $this->_walls[0]) $classes[] = 'w0';
    if (TRUE == $this->_walls[1]) $classes[] = 'w1';
    if (TRUE == $this->_walls[2]) $classes[] = 'w2';
    if (TRUE == $this->_walls[3]) $classes[] = 'w3';


    $html = '<td id="sq_' . $this->_coordinates . '" class="' . implode(' ', $classes) . '">';
    if (isset($board_modifier['moves'])) {
      if (array_key_exists($this->_coordinates, $board_modifier['moves'])) {
        $html .= '<div class="action">';
        $html .= '<a title="' . $this . '" href="?action=move_to&target=' . $this->_coordinates . '"><strong>Move</strong> (' . $board_modifier['moves'][$this->_coordinates] .')</a>';
        $html .= '</div>';
      }
    }
    if (isset($board_modifier['drive'])) {
      if (array_key_exists($this->_coordinates, $board_modifier['drive'])) {
        $html .= '<div class="action">';
        $html .= '<a title="' . $this . '" href="?action=drive&spot=' . $this->_coordinates . '"><strong>Park</strong></a>';
        $html .= '</div>';
      }
    }
    if (isset($board_modifier['shoot'])) {
      if (array_key_exists($this->_coordinates, $board_modifier['shoot'])) {
        $html .= '<div class="action">';
        $html .= '<a title="' . $this . '" href="?action=shoot&target=' . $board_modifier['shoot'][$this->_coordinates]['color'] . '"><strong>Shoot (' . $board_modifier['shoot'][$this->_coordinates]['distance'] . ')</strong></a>';
        $html .= '</div>';
      }
    }
    if (isset($board_modifier['path'])) {
      if (array_key_exists($this->_coordinates, $board_modifier['path']) && $this->_coordinates != $board_modifier['player_coordinates']) {
        $html .= '<div class="previous_move">&otimes;</div>';
      }
    }
    $card_is_goal = FALSE;
    if (isset($board_modifier['hand']) && ($this->_card instanceof OpenSpree_Card)) {
      foreach ($board_modifier['hand'] as $card) {
        if ($card->getNumber() == $this->_card->getNumber() && $card->getSuit() == $this->_card->getSuit()) {
          $card_is_goal = TRUE;
        }
      }
    }
    if (!empty($this->_car_colors)) {
      foreach ($this->_car_colors as $car_color) {
        $html .= '<img width="40" height="21" src="/assets/images/car_' . $car_color . '_40x21.png"/><br/>';
      }
    }
    if (!empty($this->_player_colors)) {
      foreach ($this->_player_colors as $player_color) {
        $html .= OpenSpree_Design::playerAvatar($player_color, $board_modifier['players'][$player_color]->getKnockedDown()) . '<br/>';
      }
    }
    // Coordinates - debug
    // $html .= '<div style="font-size:8px;">' . $this . '</div>';
    // Card
    if ($this->_card instanceof OpenSpree_Card) {
      $html .= '<span class="card' . ($card_is_goal ? ' goal' : '') . '">' . $this->_card->toHtml() . '</span>';
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
    return OpenSpree_Square::convertCoordinateToEdge($this->_coordinates);
  }

  public function toEdges() {
    $x = hexdec($this->_coordinates[0]);
    $y = hexdec($this->_coordinates[1]);
    $edges = array();
    // Fountain
    if (($x == 6 || $x == 7) && $y == 4) {
      return $edges;
    }
    if (!$this->_walls[0] && !((6 == $x && 5 == $y) || (7 == $x && 5 == $y))) {
      $edges[] = array($this->toEdgeCoordinate(), $x . 'x' . ($y - 1));
    }
    if (!$this->_walls[1] && !(5 == $x && 4 == $y)) {
      $edges[] = array($this->toEdgeCoordinate(), ($x + 1) . 'x' .  $y);
    }
    if (!$this->_walls[2] && !((6 == $x && 3 == $y) || (7 == $x && 3 == $y))) {
      $edges[] = array($this->toEdgeCoordinate(), $x . 'x' .  ($y + 1));
    }
    if (!$this->_walls[3] && !(8 == $x && 4 == $y)) {
      $edges[] = array($this->toEdgeCoordinate(), ($x - 1) . 'x' .  $y);
    }
    return $edges;
  }

  public static function shootableSquareDistance($coordinate_source, $coordinate_target) {
    $x = hexdec($coordinate_source[0]);
    $y = hexdec($coordinate_source[1]);
    $a = hexdec($coordinate_target[0]);
    $b = hexdec($coordinate_target[1]);
    return sqrt(pow($x - $a, 2) + pow($y - $b, 2));
  }

  public static function convertAngleToDirection($angle) {
    switch ($angle) {
      case '0': {
        return 0;
        break;
      }
      case '90': {
        return 1;
        break;
      }
      case '180': {
        return 2;
        break;
      }
      case '270': {
        return 3;
        break;
      }
    }
  }

  public static function calculateAngle($coordinate_source, $coordinate_target) {
    $x = hexdec($coordinate_source[0]);
    $y = hexdec($coordinate_source[1]);
    $a = hexdec($coordinate_target[0]);
    $b = hexdec($coordinate_target[1]);

    return ((rad2deg(atan2($y - $b, $x - $a)) + 270) % 360);
  }

}

?>