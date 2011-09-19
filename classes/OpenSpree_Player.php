<?php
/**
 * OpenSpree Player
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Player
 *
 * @package OpenSpree
 */
class OpenSpree_Player {
  private $_name;
  private $_color;
  private $_hand = array();
  private $_shopping_cart = array();
  private $_safe_cards = array();
  private $_score = 0;
  private $_in_car = FALSE;
  private $_knocked_down = FALSE;

  public function __construct($color, $name) {
    if (!OpenSpree_Game::isValidColor($color)) {
      throw new Exception('Invalid color; cannot construct player.');
    }
    $this->_color = $color;
    if (!$this->isValidName($name)) {
      throw new Exception('Invalid name; cannot construct player.');
    }
    $this->_name = $name;
  }

  public function isKnockedDown() {
    return $this->_knocked_down;
  }

  public function getScore() {
  	return $this->_score;
  }

  public function updateScore() {
    $score = 0;
    if (!empty($this->_safe_cards)) {
      foreach ($this->_safe_cards as $card) {
        switch ($card->getNumber()) {
        	case '2':
          case '3':
          case '4':
          case '5':
          case '6':
          case '7':
          case '8':
          case '9': {
            $score += $card->getNumber();
            break;
          }
          case 'J': {
            $score += 11;
            break;
          }
          case 'Q': {
            $score += 12;
            break;
          }
          case 'K': {
            $score += 13;
            break;
          }
          case 'A': {
            $score += 15;
          }
        }
      }
    }
    $this->_score = $score;
  }

  public function setKnockedDown($knocked_down) {
    $this->_knocked_down = $knocked_down;
  }

  public function getKnockedDown() {
    return $this->_knocked_down;
  }

  public function __toString() {
    // This isn't actually a string.  Well, it is, but it's not plain text.
    $html = '<dl>';
    $html .= '<dt style="color:' . $this->_color . ';">' . $this->_name . '</dt>';
    $hand = array();
    if (!empty($this->_hand)) {
      foreach ($this->_hand as $card) {
        $hand[] = $card->toHtml();
      }
    } else {
      $hand[] = '<span style="color:grey;">Empty.</span>';
    }
    $html .= '<dd>Hand: ' . implode(', ', $hand) . '</dd>';
    $cart = array();
    if (!empty($this->_shopping_cart)) {
      foreach ($this->_shopping_cart as $card) {
        $cart[] = $card->toHtml();
      }
    } else {
      $cart[] = '<span style="color:grey;">Empty.</span>';
    }
    $html .= '<dd>Cart: ' . implode(', ', $cart) . '</dd>';
    $html .= '<dd>Score: ' . $this->_score . '</dd>';
    $html .= '<dd>Vehicle: ' . ($this->_in_car ? 'Driving' : 'Idling') . '</dd>';
    $html .= '</dl>';
    return $html;
  }

  public static function isValidName($name) {
    return preg_match('/^[A-Z \'.-]{2,20}$/i', $name) ? true : false;
  }

  public function takeCard(OpenSpree_Card $card) {
    $this->_hand[] = $card;
  }

  public function removeCard(OpenSpree_Card $card) {
    $new_hand = array();
    if (!empty($this->_hand)) {
      foreach ($this->_hand as $existing_card) {
        if (($existing_card->getNumber() != $card->getNumber()) || ($existing_card->getSuit() != $card->getSuit())) {
          $new_hand[] = $existing_card;
        }
      }
    }
    $this->_hand = $new_hand;
  }

  public function getHand() {
    return $this->_hand;
  }

  public function getColor() {
    return $this->_color;
  }

  public function isInCar() {
    return $this->_in_car;
  }

  public function setInCar($boolean) {
    $this->_in_car = $boolean;
  }

  public function putCardInCart(OpenSpree_Card $card) {
    $this->_shopping_cart[$card->getSuit() . $card->getNumber()] = $card;
  }

  public function takeCardFromCart(OpenSpree_Card $card) {
    unset ($this->_shopping_cart[$card->getSuit() . $card->getNumber()]);
  }

  public function stashCart() {
    // Assume player can stash
    $this->_safe_cards = array_merge($this->_safe_cards, $this->_shopping_cart);
    $this->_shopping_cart = array();
    $this->updateScore();
  }

  public function getName() {
    return $this->_name;
  }

  public function getShoppingCart() {
    return $this->_shopping_cart;
  }

}

?>