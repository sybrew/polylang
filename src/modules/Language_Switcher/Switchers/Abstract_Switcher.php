<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use WP_Syntex\Polylang\Language_Switcher\Elements;
use WP_Syntex\Polylang\Language_Switcher\Settings\Generic as Settings;
use WP_Syntex\Polylang\Language_Switcher\Switchers\Types\Abstract_Type;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract class to display a certain type of language switcher.
 *
 * @since 3.9
 */
abstract class Abstract_Switcher {
	/**
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * @var Elements
	 */
	protected Elements $elements;

	/**
	 * @var Abstract_Type
	 */
	protected Abstract_Type $item_type;

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param Settings      $settings  Instance of `Settings`.
	 * @param Elements      $elements  Instance of `Elements`.
	 * @param Abstract_Type $item_type Instance of `Abstract_Type`.
	 */
	public function __construct( Settings $settings, Elements $elements, Abstract_Type $item_type ) {
		$this->settings  = $settings;
		$this->elements  = $elements;
		$this->item_type = $item_type;
	}

	/**
	 * Returns the markup of the switcher.
	 *
	 * @since 3.9
	 *
	 * @return string
	 */
	abstract public function get(): string;
}
