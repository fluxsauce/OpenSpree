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

  private $_player_color;
  private $_id;
  private $_initial_board_state;
  private $_initial_deck_state;

  private $_player_move_history = array();
  private $_player_move_distance = 0;
  private $_player_drove = FALSE;

  private $_player_roll = 0;
  private $_player_roll_modifier = 0;

  private $_player_shot = FALSE;
  private $_player_earned_another_turn = FALSE;

  function __construct($player_color, $id, $initial_board_state, $initial_deck_state) {
    $this->_player_color = $player_color;
    $this->_id = $id;
    $this->_initial_board_state = $initial_board_state;
    $this->_initial_deck_state = $initial_deck_state;
  }

  public function getPlayerColor() {
    return $this->_player_color;
  }

  public function getId() {
    return $this->_id;
  }

  public function setPlayerShot($boolean) {
  	$this->_player_shot = $boolean;
  }

  public function getPlayerShot() {
  	return $this->_player_shot;
  }

  public function setPlayerEarnedAnotherTurn($boolean) {
  	$this->_player_earned_another_turn = $boolean;
  }

  public function getPlayerEarnedAnotherTurn() {
  	return $this->_player_earned_another_turn;
  }

  function __toString() {
    $debug_string = 'Turn #' . $this->_id . ', ' . $this->_player_color . ' player.';
    $debug_string .= ' Player ' . ($this->hasPlayerRolled() ? 'has' : 'has not') . ' rolled.';
    if ($this->hasPlayerRolled()) {
      $debug_string .= ' Player rolled ' . $this->_player_roll . ' with ' . $this->_player_roll_modifier . ' modifier.';
    }
    if ($this->_player_move_distance > 0) {
      $debug_string .= ' Player move distance: ' . $this->_player_move_distance . '. ';
    }
    if (!empty($this->_player_move_history)) {
      $debug_string .= ' Player move history: ' . implode(', ', $this->_player_move_history) . '.';
    }
    return $debug_string;
  }

  public function hasPlayerMoved() {
    return count($this->_player_move_history) > 0;
  }

  public function addPlayerMove($coordinates) {
    foreach ($coordinates as $coordinate) {
      $this->_player_move_history[$coordinate] = $coordinate;
    }
    // Offset for initial square
    $this->_player_move_distance += count($coordinates) - 1;
  }

  public function hasPlayerRolled() {
    return $this->_player_roll > 0;
  }

  public function getPlayerRoll() {
    return $this->_player_roll;
  }

  public function getPlayerRemainingMoves() {
    $remaining_move_count = ($this->_player_roll + $this->_player_roll_modifier) - $this->_player_move_distance;
    return $remaining_move_count;
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

  public function getPlayerMoveHistory() {
    return $this->_player_move_history;
  }

  public function setPlayerDrove($boolean) {
    $this->_player_drove = $boolean;
  }

  public function getPlayerDrove($boolean) {
    return $this->_player_drove;
  }

}

?>