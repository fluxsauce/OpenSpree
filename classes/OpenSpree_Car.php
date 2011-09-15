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
	private $color;
	private $player_in_car = TRUE;

	function __construct($color) {
    if (!OpenSpree_Game::isValidColor($color)) {
			throw new Exception('Invalid color; cannot construct car.');
		}
    $this->color = $color;
	}

	public function getColor() {
		return $this->color;
	}

	public function setPlayerInCar(boolean $player_in_car) {
		$this->player_in_car = $player_in_car;
	}

}

?>