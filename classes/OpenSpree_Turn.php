<?php
/**
 * OpenSpree Turn
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Turn
 *
 * @package OpenSpree
 */
class OpenSpree_Turn {

	private $_player;
	private $_id;
	private $_initial_board_state;
	private $_initial_deck_state;

	private $_player_move_history = array();
	private $_player_drove = FALSE;

	private $_player_roll = 0;
	private $_player_roll_modifier = 0;

	private $_player_shot = FALSE;
	private $_player_earned_another_turn = FALSE;

	function __construct(OpenSpree_Player $player, $id, $initial_board_state, $initial_deck_state) {
	  $this->_player = $player;
	  $this->_id = $id;
	  $this->_initial_board_state = $initial_board_state;
	  $this->_initial_deck_state = $initial_deck_state;
	}

	public function getPlayer() {
		return $this->_player;
	}

	public function getId() {
		return $this->_id;
	}

	function __toString() {
		return 'Turn #' . $this->_id . ' - Player ' . $this->_player->getName();
	}

	public function hasPlayerMoved() {
		return count($this->_player_move_history) > 0;
	}

	public function addPlayerMove($coordinates) {
		$this->_player_move_history[] = $coordinates;
	}

	public function hasPlayerRolled() {
		return $this->_player_roll > 0;
	}

	public function getPlayerRoll() {
		return $this->_player_roll;
	}

	public function getPlayerRemainingMoves() {
		return ($this->_player_roll + $this->_player_roll_modifier) - count($this->_player_move_history);
	}

	public function setPlayerRoll($player_roll) {
		$this->_player_roll = $player_roll;
	}

	public function getPlayerRollModifier() {
		return $this->_player_roll_modifier;
	}

	public function setPlayerRollModifier($player_roll_modifier) {
		$this->_player_roll_modifier = $player_roll_modifier;
	}

}

?>