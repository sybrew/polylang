<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use PLL_Links;
use WP_Syntex\Polylang\Language_Switcher\Elements;
use WP_Syntex\Polylang\Language_Switcher\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract class to display a language switcher.
 *
 * @since 3.9
 */
abstract class Abstract_Switcher {
	/**
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * @var PLL_Links
	 */
	protected PLL_Links $links;

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param Settings  $settings Instance of `Settings`.
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 */
	public function __construct( Settings $settings, PLL_Links $links ) {
		$this->settings = $settings;
		$this->links    = $links;
	}

	/**
	 * Returns the markup of the switcher.
	 *
	 * @since 3.9.0
	 *
	 * @param \PLL_Language[] $languages A list of language instances.
	 * @return string
	 */
	abstract public function get( array $languages ): string;

	/**
	 * Returns an instance of `Elements`.
	 *
	 * @since 3.9.0
	 *
	 * @param \PLL_Language[] $languages A list of language instances.
	 * @return Elements
	 */
	public function get_elements( array $languages ): Elements {
		return new Elements( $this->settings, $this->links, $languages );
	}

	/**
	 * Tells whether the current theme supports HTML5.
	 *
	 * @since 3.9.0
	 *
	 * @return bool
	 */
	protected function supports_html5(): bool {
		$format = current_theme_supports( 'html5' ) ? 'html5' : 'xhtml';

		/** This filter is documented in wp-includes/widgets/class-wp-nav-menu-widget.php */
		$format = apply_filters( 'navigation_widgets_format', $format );

		return 'html5' === $format;
	}
}
