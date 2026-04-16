<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use WP_Syntex\Polylang\Language_Switcher\Elements;
use WP_Syntex\Polylang\Language_Switcher\Switchers\Types\Nav as Type;
use WP_Syntex\Polylang\Language_Switcher\Settings\Generic as Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class that displays a language switcher as a list.
 *
 * @since 3.9
 */
class Nav extends Abstract_Switcher {
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
		$out = '';

		foreach ( $this->elements->get() as $element ) {
			$out .= $this->item_type->get_row( $element );
		}

		if ( empty( $out ) || ! $this->settings->show_wrapper ) {
			return $out;
		}

		return $this->item_type->wrap( "<ul>{$out}</ul>" );
	}
}
