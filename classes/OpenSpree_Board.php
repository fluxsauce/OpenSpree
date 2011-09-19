<?php
/**
 * OpenSpree Board
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Board - Collection of squares
 *
 * @package OpenSpree
 */
class OpenSpree_Board {

  public $squares = array();
  public $locations = array();

  function __construct() {
    $this->squares['00'] = new OpenSpree_Square('parking_lot', '00', NULL, array(TRUE, FALSE, FALSE, TRUE));
    $this->squares['10'] = new OpenSpree_Square('parking_lot', '10', NULL, array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['20'] = new OpenSpree_Square('parking_lot', '20', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['30'] = new OpenSpree_Square('parking_space', '30', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['40'] = new OpenSpree_Square('parking_lot', '40', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['50'] = new OpenSpree_Square('parking_lot', '50', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['60'] = new OpenSpree_Square('parking_lot', '60', NULL, array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['70'] = new OpenSpree_Square('parking_lot', '70', NULL, array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['80'] = new OpenSpree_Square('parking_lot', '80', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['90'] = new OpenSpree_Square('parking_lot', '90', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['a0'] = new OpenSpree_Square('parking_space', 'a0', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['b0'] = new OpenSpree_Square('parking_lot', 'b0', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['c0'] = new OpenSpree_Square('parking_lot', 'c0', NULL, array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['d0'] = new OpenSpree_Square('parking_lot', 'd0', NULL, array(TRUE, TRUE, FALSE, FALSE));

    $this->squares['01'] = new OpenSpree_Square('parking_space', '01', NULL, array(FALSE, FALSE, FALSE, TRUE));
    $this->squares['11'] = new OpenSpree_Square('shop', '11', new OpenSpree_Card('6', 'H'), array(FALSE, TRUE, TRUE, FALSE));
    $this->squares['21'] = new OpenSpree_Square('shop', '21', new OpenSpree_Card('K', 'H'), array(TRUE, FALSE, TRUE, TRUE));
    $this->squares['31'] = new OpenSpree_Square('shop', '31', new OpenSpree_Card('9', 'H'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['41'] = new OpenSpree_Square('shop', '41', new OpenSpree_Card('J', 'H'), array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['51'] = new OpenSpree_Square('shop', '51', new OpenSpree_Card('3', 'H'), array(TRUE, TRUE, FALSE, FALSE));
    $this->squares['61'] = new OpenSpree_Square('open', '61', NULL, array(FALSE, FALSE, FALSE, TRUE));
    $this->squares['71'] = new OpenSpree_Square('open', '71', NULL, array(FALSE, TRUE, FALSE, FALSE));
    $this->squares['81'] = new OpenSpree_Square('shop', '81', new OpenSpree_Card('3', 'S'), array(TRUE, FALSE, FALSE, TRUE));
    $this->squares['91'] = new OpenSpree_Square('shop', '91', new OpenSpree_Card('J', 'S'), array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['a1'] = new OpenSpree_Square('shop', 'a1', new OpenSpree_Card('9', 'S'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['b1'] = new OpenSpree_Square('shop', 'b1', new OpenSpree_Card('K', 'S'), array(TRUE, TRUE, TRUE, FALSE));
    $this->squares['c1'] = new OpenSpree_Square('shop', 'c1', new OpenSpree_Card('6', 'S'), array(FALSE, FALSE, TRUE, TRUE));
    $this->squares['d1'] = new OpenSpree_Square('parking_space', 'd1', NULL, array(FALSE, TRUE, FALSE, FALSE));

    $this->squares['02'] = new OpenSpree_Square('parking_space', '02', NULL, array(FALSE, TRUE, FALSE, TRUE));
    $this->squares['12'] = new OpenSpree_Square('shop', '12', new OpenSpree_Card('A', 'H'), array(TRUE, FALSE, TRUE, TRUE));
    $this->squares['22'] = new OpenSpree_Square('shop', '22', new OpenSpree_Card('8', 'H'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['32'] = new OpenSpree_Square('open', '32', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['42'] = new OpenSpree_Square('shop', '42', new OpenSpree_Card('4', 'H'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['52'] = new OpenSpree_Square('shop', '52', new OpenSpree_Card('7', 'H'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['62'] = new OpenSpree_Square('open', '62', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['72'] = new OpenSpree_Square('open', '72', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['82'] = new OpenSpree_Square('shop', '82', new OpenSpree_Card('7', 'S'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['92'] = new OpenSpree_Square('shop', '92', new OpenSpree_Card('4', 'S'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['a2'] = new OpenSpree_Square('open', 'a2', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['b2'] = new OpenSpree_Square('shop', 'b2', new OpenSpree_Card('8', 'S'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['c2'] = new OpenSpree_Square('shop', 'c2', new OpenSpree_Card('A', 'S'), array(TRUE, TRUE, TRUE, FALSE));
    $this->squares['d2'] = new OpenSpree_Square('parking_space', 'd2', NULL, array(FALSE, TRUE, FALSE, TRUE));

    $this->squares['03'] = new OpenSpree_Square('parking_lot', '03', NULL, array(FALSE, TRUE, FALSE, TRUE));
    $this->squares['13'] = new OpenSpree_Square('shop', '13', new OpenSpree_Card('Q', 'H'), array(TRUE, FALSE, TRUE, TRUE));
    $this->squares['23'] = new OpenSpree_Square('shop', '23', new OpenSpree_Card('3', 'H'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['33'] = new OpenSpree_Square('shop', '33', new OpenSpree_Card('5', 'H'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['43'] = new OpenSpree_Square('shop', '43', new OpenSpree_Card('2', 'H'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['53'] = new OpenSpree_Square('open', '53', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['63'] = new OpenSpree_Square('open', '63', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['73'] = new OpenSpree_Square('open', '73', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['83'] = new OpenSpree_Square('open', '83', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['93'] = new OpenSpree_Square('shop', '93', new OpenSpree_Card('2', 'S'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['a3'] = new OpenSpree_Square('shop', 'a3', new OpenSpree_Card('5', 'S'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['b3'] = new OpenSpree_Square('shop', 'b3', new OpenSpree_Card('3', 'S'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['c3'] = new OpenSpree_Square('shop', 'c3', new OpenSpree_Card('Q', 'S'), array(TRUE, TRUE, TRUE, FALSE));
    $this->squares['d3'] = new OpenSpree_Square('parking_lot', 'd3', NULL, array(FALSE, TRUE, FALSE, TRUE));

    $this->squares['04'] = new OpenSpree_Square('parking_lot', '04', NULL, array(FALSE, FALSE, FALSE, TRUE));
    $this->squares['14'] = new OpenSpree_Square('open', '14', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['24'] = new OpenSpree_Square('open', '24', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['34'] = new OpenSpree_Square('open', '34', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['44'] = new OpenSpree_Square('open', '44', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['54'] = new OpenSpree_Square('open', '54', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['64'] = new OpenSpree_Square('fountain', '64', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['74'] = new OpenSpree_Square('fountain', '74', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['84'] = new OpenSpree_Square('open', '84', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['94'] = new OpenSpree_Square('open', '94', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['a4'] = new OpenSpree_Square('open', 'a4', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['b4'] = new OpenSpree_Square('open', 'b4', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['c4'] = new OpenSpree_Square('open', 'c4', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['d4'] = new OpenSpree_Square('parking_lot', 'd4', NULL, array(FALSE, TRUE, FALSE, FALSE));

    $this->squares['05'] = new OpenSpree_Square('parking_lot', '05', NULL, array(FALSE, TRUE, FALSE, TRUE));
    $this->squares['15'] = new OpenSpree_Square('shop', '15', new OpenSpree_Card('Q', 'C'), array(TRUE, FALSE, TRUE, TRUE));
    $this->squares['25'] = new OpenSpree_Square('shop', '25', new OpenSpree_Card('3', 'C'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['35'] = new OpenSpree_Square('shop', '35', new OpenSpree_Card('5', 'C'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['45'] = new OpenSpree_Square('shop', '45', new OpenSpree_Card('2', 'C'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['55'] = new OpenSpree_Square('open', '55', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['65'] = new OpenSpree_Square('open', '65', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['75'] = new OpenSpree_Square('open', '75', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['85'] = new OpenSpree_Square('open', '85', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['95'] = new OpenSpree_Square('shop', '95', new OpenSpree_Card('2', 'D'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['a5'] = new OpenSpree_Square('shop', 'a5', new OpenSpree_Card('5', 'D'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['b5'] = new OpenSpree_Square('shop', 'b5', new OpenSpree_Card('3', 'D'), array(TRUE, FALSE, FALSE, FALSE));
    $this->squares['c5'] = new OpenSpree_Square('shop', 'c5', new OpenSpree_Card('Q', 'D'), array(TRUE, TRUE, TRUE, FALSE));
    $this->squares['d5'] = new OpenSpree_Square('parking_lot', 'd5', NULL, array(FALSE, TRUE, FALSE, TRUE));

    $this->squares['06'] = new OpenSpree_Square('parking_space', '06', NULL, array(FALSE, TRUE, FALSE, TRUE));
    $this->squares['16'] = new OpenSpree_Square('shop', '16', new OpenSpree_Card('A', 'C'), array(TRUE, FALSE, TRUE, TRUE));
    $this->squares['26'] = new OpenSpree_Square('shop', '26', new OpenSpree_Card('8', 'C'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['36'] = new OpenSpree_Square('open', '36', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['46'] = new OpenSpree_Square('shop', '46', new OpenSpree_Card('4', 'C'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['56'] = new OpenSpree_Square('shop', '56', new OpenSpree_Card('7', 'C'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['66'] = new OpenSpree_Square('open', '66', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['76'] = new OpenSpree_Square('open', '76', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['86'] = new OpenSpree_Square('shop', '86', new OpenSpree_Card('7', 'D'), array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['96'] = new OpenSpree_Square('shop', '96', new OpenSpree_Card('4', 'D'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['a6'] = new OpenSpree_Square('open', 'a6', NULL, array(FALSE, FALSE, FALSE, FALSE));
    $this->squares['b6'] = new OpenSpree_Square('shop', 'b6', new OpenSpree_Card('8', 'D'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['c6'] = new OpenSpree_Square('shop', 'c6', new OpenSpree_Card('A', 'D'), array(TRUE, TRUE, TRUE, FALSE));
    $this->squares['d6'] = new OpenSpree_Square('parking_space', 'd6', NULL, array(FALSE, TRUE, FALSE, TRUE));

    $this->squares['07'] = new OpenSpree_Square('parking_space', '07', NULL, array(FALSE, FALSE, FALSE, TRUE));
    $this->squares['17'] = new OpenSpree_Square('shop', '17', new OpenSpree_Card('6', 'C'), array(TRUE, TRUE, FALSE, FALSE));
    $this->squares['27'] = new OpenSpree_Square('shop', '27', new OpenSpree_Card('K', 'C'), array(TRUE, FALSE, TRUE, TRUE));
    $this->squares['37'] = new OpenSpree_Square('shop', '37', new OpenSpree_Card('9', 'C'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['47'] = new OpenSpree_Square('shop', '47', new OpenSpree_Card('J', 'C'), array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['57'] = new OpenSpree_Square('shop', '57', new OpenSpree_Card('3', 'C'), array(FALSE, TRUE, TRUE, FALSE));
    $this->squares['67'] = new OpenSpree_Square('open', '67', NULL, array(FALSE, FALSE, FALSE, TRUE));
    $this->squares['77'] = new OpenSpree_Square('open', '77', NULL, array(FALSE, TRUE, FALSE, FALSE));
    $this->squares['87'] = new OpenSpree_Square('shop', '87', new OpenSpree_Card('3', 'D'), array(FALSE, FALSE, TRUE, TRUE));
    $this->squares['97'] = new OpenSpree_Square('shop', '97', new OpenSpree_Card('J', 'D'), array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['a7'] = new OpenSpree_Square('shop', 'a7', new OpenSpree_Card('9', 'D'), array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['b7'] = new OpenSpree_Square('shop', 'b7', new OpenSpree_Card('K', 'D'), array(TRUE, TRUE, TRUE, FALSE));
    $this->squares['c7'] = new OpenSpree_Square('shop', 'c7', new OpenSpree_Card('6', 'D'), array(TRUE, FALSE, FALSE, TRUE));
    $this->squares['d7'] = new OpenSpree_Square('parking_space', 'd7', NULL, array(FALSE, TRUE, FALSE, FALSE));

    $this->squares['08'] = new OpenSpree_Square('parking_lot', '08', NULL, array(FALSE, FALSE, TRUE, TRUE));
    $this->squares['18'] = new OpenSpree_Square('parking_lot', '18', NULL, array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['28'] = new OpenSpree_Square('parking_lot', '28', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['38'] = new OpenSpree_Square('parking_space', '38', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['48'] = new OpenSpree_Square('parking_lot', '48', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['58'] = new OpenSpree_Square('parking_lot', '58', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['68'] = new OpenSpree_Square('parking_lot', '68', NULL, array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['78'] = new OpenSpree_Square('parking_lot', '78', NULL, array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['88'] = new OpenSpree_Square('parking_lot', '88', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['98'] = new OpenSpree_Square('parking_lot', '98', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['a8'] = new OpenSpree_Square('parking_space', 'a8', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['b8'] = new OpenSpree_Square('parking_lot', 'b8', NULL, array(TRUE, FALSE, TRUE, FALSE));
    $this->squares['c8'] = new OpenSpree_Square('parking_lot', 'c8', NULL, array(FALSE, FALSE, TRUE, FALSE));
    $this->squares['d8'] = new OpenSpree_Square('parking_lot', 'd8', NULL, array(FALSE, TRUE, TRUE, FALSE));

    $this->rebuildLocations();
  }

  public function rebuildLocations() {
    $this->locations = array();
    foreach ($this->squares as $square) {
      if ($square->getCard() instanceof OpenSpree_Card) {
        $this->locations['cards'][$square->getCard()->__toString()] = $square->getCoordinates();
      }
      $player_colors_in_square = $square->getPlayerColors();
      if (!empty($player_colors_in_square)) {
        foreach ($player_colors_in_square as $player_color) {
          $this->locations['players'][$player_color] = $square->getCoordinates();
        }
      }
      $car_colors_in_square = $square->getCarColors();
      if (!empty($car_colors_in_square)) {
        foreach ($car_colors_in_square as $car_color) {
          $this->locations['cars'][$car_color] = $square->getCoordinates();
        }
      }
      $this->locations[$square->getType()][] = $square->getCoordinates();
    }
  }

  public function getLocations() {
    return $this->locations;
  }

  public function getEmptyParkingSpaces() {
    $empty_parking_spaces = array();
    foreach ($this->locations['parking_space'] as $hex_coordinates) {
      $car_colors = $this->squares[$hex_coordinates]->getCarColors();
      if (empty($car_colors)) {
        $empty_parking_spaces[$hex_coordinates] = $this->squares[$hex_coordinates];
      }
    }
    return $empty_parking_spaces;
  }

  public function toHtml($board_modifier = array()) {
    $html = '<table class="board shadow">';
    for ($y = 0; $y < 9; $y++) {
      $html .= '<tr>';
      for ($x = 0; $x < 14; $x++) {
        $html .= $this->squares[dechex($x) . dechex($y)]->toTableCell($board_modifier);
      }
      $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
  }

  public function toEdges() {
    $ret_val = array();
    for ($x = 0; $x < 14; $x++) {
      for ($y = 0; $y < 9; $y++) {
        $ret_val = array_merge($ret_val, $this->squares[dechex($x) . dechex($y)]->toEdges());
      }
    }
    return $ret_val;
  }
}

?>