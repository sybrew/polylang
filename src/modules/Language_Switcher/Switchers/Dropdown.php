<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use WP_Syntex\Polylang\Language_Switcher\Assets;
use WP_Syntex\Polylang\Language_Switcher\Element;
use WP_Syntex\Polylang\Language_Switcher\Elements;
use WP_Syntex\Polylang\Language_Switcher\Switchers\Types\Nav as Type;
use WP_Syntex\Polylang\Language_Switcher\Settings\Generic as Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class that displays a language switcher as a dropdown.
 *
 * @since 3.9
 */
class Dropdown extends Abstract_Switcher {
	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Instance of `Settings`.
	 * @param Elements $elements Instance of `Elements`.
	 */
	public function __construct( Settings $settings, Elements $elements ) {
		parent::__construct( $settings, $elements, new Type( $settings ) );
	}

	/**
	 * Returns the markup of the switcher.
	 *
	 * @since 3.9
	 *
	 * @return string
	 */
	public function get(): string {
		$out      = '';
		$current  = $this->elements->get_current();

		if ( empty( $current ) ) {
			return $out;
		}

		foreach ( $this->elements->get() as $element ) {
			$out .= $this->item_type->get_row( $element );
		}

		if ( empty( $out ) || ! $this->settings->show_wrapper ) {
			return $out;
		}

		Assets::enqueue_frontend_scripts();

		return $this->item_type->wrap(
			sprintf(
				'%1$s%2$s<ul>%3$s</ul>',
				$this->get_current_item( $current ),
				$this->get_button(),
				$out
			)
		);
	}

	/**
	 * Returns the markup of the current item.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	private function get_current_item( Element $element ): string {
		return sprintf(
			'<a lang="%1$s" hreflang="%1$s" href="%2$s" class="current-lang" aria-current="true">%3$s</a>',
			esc_attr( $element->locale ),
			esc_url( $element->url ),
			$this->item_type->get_row_inner( $element )
		);
	}

	/**
	 * Returns the markup of the button.
	 *
	 * @since 3.9
	 *
	 * @return string
	 */
	private function get_button(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
		return sprintf(
			'<button aria-label="%1$s" class="pll-submenu-toggle" aria-expanded="false">%2$s</button>',
			esc_attr( __( 'Open languages submenu', 'polylang' ) ),
			$svg
		);
	}
}
