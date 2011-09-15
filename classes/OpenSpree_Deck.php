<?php
/**
 * OpenSpree Deck
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Deck
 *
 * @package OpenSpree
 */
class OpenSpree_Deck {
  private $cards = array();
  private $discard = array();

  public function __construct() {
    for ($i = 0; $i < 2; $i++) {
      foreach (OpenSpree_Card::$valid_numbers as $number) {
      	if ('U' != $number) {
	        foreach (OpenSpree_Card::$valid_suits as $suit) {
	          $this->cards[] = new OpenSpree_Card($number, $suit);
	        }
      	} else {
      		for ($j = 1; $j <= 2; $j++) {
	      		// Joker
	      		$this->cards[] = new OpenSpree_Card($number);
      		}
      	}
      }
    }
  }

  public function draw() {
  	if (empty($this->cards)) {
  		$this->cards = $this->discard;
  		$this->discard = array();
  		$this->shuffle();
  	}
  	return array_pop($this->cards);
  }

  public function discard(OpenSpree_Card $card) {
  	if (!$card->isValidCard()) {
  		throw new Exception('Cannot add invalid card to discard pile.');
  	}
  	array_push($this->discard, $card);
  }

  public function shuffle() {
  	shuffle($this->cards);
  }

  /**
   * Render the deck as a string for debugging
   */
  public function __toString() {
    return implode('<br/>', $this->cards);
  }

  public function toHtml() {
  	$cards = array();
  	foreach ($this->cards as $card) {
  		$cards[] = $card->toHtml();
  	}
    return '<ol><li>' . implode('</li><li>', $cards) . '</li></ol>';
  }
}