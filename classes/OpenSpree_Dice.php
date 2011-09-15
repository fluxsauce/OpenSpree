<?php
/**
 * OpenSpree Dice
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Dice
 *
 * @package OpenSpree
 */
class OpenSpree_Dice {
	/**
	 * Roll two standard d6 dice and return the sum.
	 * @return int Sum of 2d6
	 */
	public static function roll() {
		return (rand(1, 6) + rand(1, 6));
	}

	/**
	 * Validate a roll.
	 * @return boolean
	 */
	public static function isValidRoll($roll) {
		return (($roll >= 2) && ($roll <= 12));
	}
}

?>