<?php
/**
 * OpenSpree Car
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Car
 *
 * @package OpenSpree
 */
class OpenSpree_Car {
  private $_color;
  private $_player_in_car = FALSE;

  function __construct($color) {
    if (!OpenSpree_Game::isValidColor($color)) {
      throw new Exception('Invalid color; cannot construct car.');
    }
    $this->_color = $color;
  }

  public function getColor() {
    return $this->color;
  }

  public function setPlayerInCar($player_in_car) {
    $this->_player_in_car = $player_in_car;
  }

}

?>