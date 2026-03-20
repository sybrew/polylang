<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

use WP_Syntex\Polylang\Language_Switcher\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Data representing an item.
 *
 * @since 3.9.0
 *
 * @phpstan-type ElementData array{
 *     id: int,
 *     slug: non-empty-string,
 *     locale: non-empty-string,
 *     url: string,
 *     label: string,
 *     flag: string,
 *     is_current?: bool,
 *     item_classes?: string[],
 *     link_classes?: string[]
 * }
 */
class Element {
	/**
	 * @since 3.9.0
	 *
	 * @var int
	 */
	public int $id;

	/**
	 * @since 3.9.0
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	public string $slug;

	/**
	 * @since 3.9.0
	 *
	 * @var string
	 *
	 * @phpstan-var non-empty-string
	 */
	public string $locale;

	/**
	 * @since 3.9.0
	 *
	 * @var string
	 */
	public string $url;

	/**
	 * @since 3.9.0
	 *
	 * @var string
	 */
	public string $label;

	/**
	 * @since 3.9.0
	 *
	 * @var string
	 */
	public string $flag;

	/**
	 * @since 3.9.0
	 *
	 * @var bool
	 */
	public $is_current = false;

	/**
	 * @since 3.9.0
	 *
	 * @var string[]
	 */
	public $item_classes = array();

	/**
	 * @since 3.9.0
	 *
	 * @var string[]
	 */
	public $link_classes = array();

	/**
	 * Constructor.
	 *
	 * @since 3.9.0
	 *
	 * @param array $data {
	 *     Data.
	 *
	 *     @type int      $id           Language ID (`term_id`).
	 *     @type string   $slug         Language slug.
	 *     @type string   $locale       Language locale.
	 *     @type string   $url          URL to link to.
	 *     @type string   $label        Label to display.
	 *     @type string   $flag         Flag HTML.
	 *     @type bool     $is_current   Optional. Tells if it's the item to highlight. Default is `false`.
	 *     @type string[] $item_classes Optional. HTML classes to add to each item. Default is an empty array.
	 *     @type string[] $link_classes Optional. HTML classes to add to each link. Default is an empty array.
	 * }
	 *
	 * @phpstan-param ElementData $data
	 */
	public function __construct( array $data ) {
		foreach ( array_intersect_key( $data, get_class_vars( self::class ) ) as $name => $value ) {
			$this->$name = $value;
		}
	}
}
