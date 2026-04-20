<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

use PLL_Links;
use PLL_Language;
use WP_Syntex\Polylang\Language_Switcher\Settings\Generic as Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Hold a collection of data representing each item.
 *
 * @since 3.9
 */
class Elements {
	/**
	 * @var Settings
	 */
	private Settings $settings;

	/**
	 * @var Element[]|null
	 */
	private ?array $elements = null;

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
	 * Returns the switcher's data.
	 *
	 * @since 3.9
	 *
	 * @return Element[]
	 *
	 * @phpstan-return array<non-empty-string, Element>
	 */
	public function get(): array {
		$out      = array();
		$is_first = true;

		foreach ( $this->get_all() as $element ) {
			if ( $this->settings->hide_current && $element->is_current ) {
				// Hide current item.
				continue;
			}

			if ( $this->settings->hide_if_empty && $element->is_empty ) {
				// Hide empty item.
				continue;
			}

			if ( $this->settings->hide_if_no_translation && ! $element->has_translations ) {
				// Hide item with no translations.
				continue;
			}

			if ( empty( $element->url ) ) {
				// Failed to get a URL.
				continue;
			}

			if ( $is_first ) {
				$is_first = false;
				$element  = clone( $element ); // We don't want the item class to be added permanently to the object.

				$element->item_classes[] = 'lang-item-first';
			}

			$out[ $element->slug ] = $element;
		}

		return $out;
	}

	/**
	 * Returns the current item data.
	 *
	 * @since 3.9
	 *
	 * @return ?Element
	 */
	public function get_current(): ?Element {
		foreach ( $this->get_all() as $element ) {
			if ( $element->is_current ) {
				return $element;
			}
		}
		return null;
	}

	/**
	 * Returns all the switcher's data, even the elements that should be excluded according to the settings.
	 *
	 * @since 3.9
	 *
	 * @return Element[]
	 *
	 * @phpstan-return array<non-empty-string, Element>
	 */
	public function get_all(): array {
		if ( is_array( $this->elements ) ) {
			return $this->elements;
		}

		$this->elements = array();

		foreach ( $this->settings->get_links()->model->languages->get_list() as $language ) {
			$this->elements[ $language->slug ] = new Element( $language, $this->settings );
		}

		return $this->elements;
	}
}
