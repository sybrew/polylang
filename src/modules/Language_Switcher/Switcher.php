<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

use WP_Syntex\Polylang\Language_Switcher\Settings\Generic as Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class that can display a language switcher.
 *
 * @since 3.9
 */
class Switcher {
	/**
	 * Prints the switcher.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Settings.
	 * @return void
	 */
	public function print( Settings $settings ): void {
		echo $this->get( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Returns the switcher's markup.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Settings.
	 * @return string
	 */
	public function get( Settings $settings ): string {
		switch ( $settings->layout ) {
			case 'horizontal':
			case 'vertical':
				$switcher = new Switchers\Nav( $settings, $this->get_elements( $settings ) );
				break;

			case 'dropdown':
				$switcher = new Switchers\Dropdown( $settings, $this->get_elements( $settings ) );
				break;

			case 'select':
				$switcher = new Switchers\Select( $settings, $this->get_elements( $settings ) );
				break;

			default:
				return '';
		}

		/**
		 * Filter the whole switcher markup.
		 *
		 * @since 3.9
		 *
		 * @param string   $html     Switcher markup.
		 * @param Settings $settings Switcher settings.
		 */
		return (string) apply_filters( 'pll_language_switcher', $switcher->get(), $settings );
	}

	/**
	 * Returns the switcher's raw data.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Settings.
	 * @return Elements
	 */
	public function get_elements( Settings $settings ): Elements {
		return new Elements( $settings );
	}
}
