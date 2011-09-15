<?php
/**
 * OpenSpree Card
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Card
 *
 * @package OpenSpree
 */
class OpenSpree_Card {
	private $number;
	private $suit;

	/**
	 * Valid numbers of a card.  '0' is 10, 'U' is Joker,
	 * as "Joker" is believed to be mispronunciation of "Juker".
	 * @var array
	 */
	public static $valid_numbers = array(
	  '2',
	  '3',
	  '4',
	  '5',
	  '6',
	  '7',
	  '8',
	  '9',
	  '0',
	  'J',
	  'Q',
	  'K',
	  'A',
	  'U',
  );

  public static $valid_suits = array(
	  'S',
	  'H',
	  'D',
	  'C',
	);

	public function __construct($number, $suit = NULL) {
	  if (!$this->isValidNumber($number)) {
			throw new Exception('Invalid card number; cannot construct card.');
		}
		$this->number = $number;
		if (('U' != $number) && !$this->isValidSuit($suit)) {
			throw new Exception('Invalid card suit; cannot construct card.');
		}
		$this->suit = $suit;
	}

	public function getNumber() {
		return $this->number;
	}

	public function getSuit() {
		return $this->suit;
	}

  public function __toString() {
  	return $this->number . $this->suit;
	}

	public function toHtml() {
		$html = '';
		switch ($this->number) {
			case '0': {
				$html .= '10';
				break;
			}
			case 'U': {
				$html .= 'Joker';
				break;
			}
			default: {
				$html .= $this->number;
				break;
			}
		}
		if ('U' != $this->number) {
		  $html .= $this->_suitToHtml();
		}
		if (in_array($this->suit, array('H', 'D'))) {
			$html = '<span class="red">' . $html . '</span>';
		}
		return $html;
	}

	private function _suitToHtml() {
		switch ($this->suit) {
			case 'S': {
				return '&spades;';
				break;
			}
			case 'H': {
				return '&hearts;';
				break;
			}
			case 'D': {
				return '&diams;';
				break;
			}
			case 'C': {
				return '&clubs;';
				break;
			}
		}
		throw new Exception('Unknown suit, cannot convert to HTML.');
		return false;
	}

	public static function isValidSuit($suit) {
		return in_array(strtoupper($suit), OpenSpree_Card::$valid_suits);
	}

  public static function isValidNumber($number) {
		return in_array(strtoupper($number), OpenSpree_Card::$valid_numbers);
	}

	public function isValidCard() {
		return ($this->isValidSuit($this->suit) && $this->isValidNumber($this->number));
	}
}