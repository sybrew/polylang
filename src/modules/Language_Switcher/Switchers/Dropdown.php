<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use WP_Syntex\Polylang\Language_Switcher\Element;

defined( 'ABSPATH' ) || exit;

/**
 * Class that displays a language switcher as a dropdown.
 *
 * @since 3.9
 */
class Dropdown extends Nav {
	/**
	 * Returns the markup of the switcher.
	 *
	 * @since 3.9.0
	 *
	 * @param \PLL_Language[] $languages A list of language instances.
	 * @return string
	 */
	public function get( array $languages ): string {
		$out      = "\n";
		$current  = null;
		$elements = array();

		if ( $this->settings->hide_current ) {
			// We need the current item to be displayed outside the list.
			$this->settings->hide_current = false;
			foreach ( $this->get_elements( $languages )->get() as $element ) {
				if ( $element->is_current ) {
					$current = $element;
				} else {
					$elements[] = $this->get_item( $element );
				}
			}
			$this->settings->hide_current = true;
		} else {
			foreach ( $this->get_elements( $languages )->get() as $element ) {
				if ( $element->is_current ) {
					$current = $element;
				}
				$elements[] = $this->get_item( $element );
			}
		}

		$out = implode( $elements );

		if ( ! $this->settings->show_wrapper || trim( $out ) === '' || empty( $current ) ) {
			return $out;
		}

		$out = sprintf(
			'<%1$s id="pll-switcher-%2$s" class="%3$s" aria-label="%4$s">%5$s%6$s<ul>%7$s</ul></%1$s>',
			$this->supports_html5() ? 'nav' : 'div',
			esc_attr( $this->settings->unique_id ),
			esc_attr( implode( ' ', $this->get_wrapper_classes() ) ),
			esc_attr( __( 'Choose a language', 'polylang' ) ),
			$this->get_current_item( $current ),
			$this->get_button(),
			$out
		);

		return "\n{$out}\n";
	}

	/**
	 * Returns the markup of the current item.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	protected function get_current_item( Element $element ): string {
		return sprintf(
			'<a lang="%1$s" hreflang="%1$s" href="%2$s" class="current-lang" aria-current="true">%3$s</a>',
			esc_attr( $element->locale ),
			esc_url( $element->url ),
			$this->get_item_label( $element )
		);
	}

	/**
	 * Returns the markup of the button.
	 *
	 * @since 3.9
	 *
	 * @return string
	 */
	protected function get_button(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
		return sprintf(
			'<button aria-label="%1$s" class="pll-submenu-toggle" aria-expanded="false">%2$s</button>',
			esc_attr( __( 'Open languages submenu', 'polylang' ) ),
			$svg
		);
	}
}
