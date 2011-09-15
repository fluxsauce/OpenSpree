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
  private $name;
  private $color;
  private $hand = array();
  private $shopping_cart = array();
  private $safe_cards = array();
  private $score = 0;
  private $in_car = TRUE;
  private $knocked_down = FALSE;

  public function isKnockedDown() {
    return $this->knocked_down;
  }

  public function updateScore() {
    $score = 0;
    if (!empty($this->safe_cards)) {
      foreach ($this->safe_cards as $card) {
        switch ($card->getNumber()) {
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
    $this->score = $score;
  }

  public function setKnockedDown(boolean $knocked_down) {
    $this->knocked_down = $knocked_down;
  }

  public function __construct($name, $color) {
    if (!$this->isValidName($name)) {
      throw new Exception('Invalid name; cannot construct player.');
    }
    $this->name = $name;
    if (!OpenSpree_Game::isValidColor($color)) {
      throw new Exception('Invalid color; cannot construct player.');
    }
    $this->color = $color;
  }

  public function __toString() {
    $html = '<dl>';
    $html .= '<dt style="color:' . $this->color . ';">' . $this->name . '</dt>';
    $hand = array();
    if (!empty($this->hand)) {
      foreach ($this->hand as $card) {
        $hand[] = $card->toHtml();
      }
    } else {
      $hand[] = '<span style="color:grey;">Empty.</span>';
    }
    $html .= '<dd>Hand: ' . implode(', ', $hand) . '</dd>';
    $cart = array();
    if (!empty($this->cart)) {
      foreach ($this->cart as $card) {
        $cart[] = $card->toHtml();
      }
    } else {
      $cart[] = '<span style="color:grey;">Empty.</span>';
    }
    $html .= '<dd>Cart: ' . implode(', ', $cart) . '</dd>';
    $html .= '<dd>Score: ' . $this->score . '</dd>';
    $html .= '</dl>';
    return $html;
  }

  public static function isValidName($name) {
    return preg_match('/^[A-Z \'.-]{2,20}$/i', $name) ? true : false;
  }

  public function takeCard(OpenSpree_Card $card) {
    $this->hand[] = $card;
  }

  public function removeCard(OpenSpree_Card $card) {
    $new_hand = array();
    if (!empty($this->hand)) {
      foreach ($this->hand as $existing_card) {
        if (($existing_card->getNumber() != $card->getNumber()) || ($existing_card->getSuit() != $card)) {
          $new_hand[] = $existing_card;
        }
      }
    }
    $this->hand = $new_hand;
  }

  public function getHand() {
    return $this->hand;
  }

  public function getColor() {
    return $this->color;
  }

  public function isInCar() {
    return $this->in_car;
  }

  public function setInCar(boolean $boolean) {
    $this->in_car = $boolean;
  }

  public function stashCart() {
    // Assume player can stash
    if (!empty($this->shopping_cart)) {
      $this->safe_cards = array_merge($this->safe_cards, $this->shopping_cart);
      $this->shopping_cart = array();
      $this->updateScore();
    }
  }

  public function getName() {
    return $this->name;
  }

}

?>