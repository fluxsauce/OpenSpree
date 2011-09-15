<?php
/**
 * OpenSpree Design
 *
 * @package OpenSpree
 * @author jpeck@fluxsauce.com
 */
/**
 * OpenSpree Design - helper functions for rendering HTML content.
 *
 * @package OpenSpree
 */
class OpenSpree_Design {
	public static function msg($type, $content) {
		switch ($type) {
			case 'error': {
				$heading = 'Error.';
				break;
			}
			case 'ok': {
				$heading = 'Success.';
				break;
			}
		  case 'question': {
				$heading = 'Question:';
				break;
			}
		  case 'warning': {
				$heading = 'Warning!';
				break;
			}
		  case 'info': {
				$heading = 'Information:';
				break;
			}
		}
		if (is_array($content)) {
			if ($content['heading']) {
				$heading = $content['heading'];
			}
			if ($content['body']) {
				$body = $content['body'];
			}
		} else {
			$body = $content;
		}
		$html = '<div class="rounded msg ' . $type . '">';
		if ('' != $heading) {
			$html .= '<div class="heading">' . $heading . '</div>';
		}
		$html .= $body;
		$html .= '</div>';
		return $html;
	}
}

?>