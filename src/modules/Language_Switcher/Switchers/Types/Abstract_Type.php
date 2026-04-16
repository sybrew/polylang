<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers\Types;

use WP_Syntex\Polylang\Language_Switcher\Element;
use WP_Syntex\Polylang\Language_Switcher\Settings\Generic as Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract class to use to generate a switcher's markup.
 *
 * @since 3.9
 */
abstract class Abstract_Type {
	/**
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Instance of `Settings`.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Wraps the given markup.
	 *
	 * @since 3.9
	 *
	 * @param string $inner Inner markup.
	 * @return string
	 */
	abstract public function wrap( string $inner ): string;

	/**
	 * Returns the markup of a row.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	abstract public function get_row( Element $element ): string;

	/**
	 * Returns the markup of the label of a row.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	abstract public function get_row_inner( Element $element ): string;

	/**
	 * Returns the list of HTML classes to add to the wrapper tag.
	 *
	 * @since 3.9
	 *
	 * @return string[]
	 */
	protected function get_wrapper_classes(): array {
		$classes = array_merge(
			$this->settings->wrapper_classes,
			array(
				'pll-switcher',
				"pll-layout-{$this->settings->layout}",
				"pll-alignment-{$this->settings->alignment}",
			)
		);

		if ( $this->settings->show_flags ) {
			$classes[] = 'pll-aspect-ratio-' . str_replace( ':', '', $settings->flag_aspect_ratio );
		}

		return $classes;
	}

	/**
	 * Tells whether the current theme supports HTML5.
	 *
	 * @since 3.9
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
