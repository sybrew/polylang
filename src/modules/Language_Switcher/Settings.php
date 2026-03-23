<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

use PLL_Links;
use PLL_Admin_Links;

defined( 'ABSPATH' ) || exit;

/**
 * Class that holds the language switcher's settings.
 *
 * @since 3.9
 *
 * @phpstan-type OptionalSettings array{
 *     layout?: 'horizontal'|'vertical'|'dropdown',
 *     alignment?: 'left'|'center'|'right'|'stretched',
 *     show_wrapper?: bool,
 *     show_flags?: bool,
 *     flag_aspect_ratio?: '32'|'11',
 *     show_labels?: ''|'names'|'codes',
 *     hide_if_empty?: bool,
 *     hide_if_no_translation?: bool,
 *     hide_current?: bool,
 *     force_home?: bool,
 *     post_id?: int,
 *     wrapper_classes?: non-empty-string[],
 *     item_classes?: non-empty-string[],
 *     link_classes?: non-empty-string[],
 *     context?: 'frontend'|'admin',
 *     current_language_code?: string
 * }
 */
class Settings {
	/**
	 * @var string
	 *
	 * @phpstan-var 'horizontal'|'vertical'|'dropdown'
	 */
	public string $layout = 'horizontal';

	/**
	 * @var string
	 *
	 * @phpstan-var 'left'|'center'|'right'|'stretched'
	 */
	public string $alignment = 'center';

	/**
	 * @var bool
	 */
	public bool $show_wrapper = true;

	/**
	 * @var bool
	 */
	public bool $show_flags = false;

	/**
	 * @var string
	 *
	 * @phpstan-var '32'|'11'
	 */
	public string $flag_aspect_ratio = '32';

	/**
	 * @var string
	 *
	 * @phpstan-var ''|'names'|'codes'
	 */
	public string $show_labels = 'names';

	/**
	 * @var bool
	 */
	public bool $hide_if_empty = true;

	/**
	 * @var bool
	 */
	public bool $hide_if_no_translation = false;

	/**
	 * @var bool
	 */
	public bool $hide_current = false;

	/**
	 * @var bool
	 */
	public bool $force_home = false;

	/**
	 * @var int
	 */
	public int $post_id = 0;

	/**
	 * @var array
	 *
	 * @phpstan-var non-empty-string[]
	 */
	public array $wrapper_classes = array();

	/**
	 * @var array
	 *
	 * @phpstan-var non-empty-string[]
	 */
	public array $item_classes = array();

	/**
	 * @var array
	 *
	 * @phpstan-var non-empty-string[]
	 */
	public array $link_classes = array();

	/**
	 * @var string
	 *
	 * @phpstan-var 'frontend'|'admin'
	 */
	public string $context = 'frontend';

	/**
	 * @var string
	 */
	public string $current_language_code = '';

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param array     $settings {
	 *     Optional switcher settings.
	 *
	 *     @type string   $layout                 Layout of the switcher. Possible values are `horizontal`, `vertical`, `dropdown`. Default is `horizontal`.
	 *     @type string   $alignment              Alignment of the items. Possible values are `left`, `center`, `right`, `stretched`. Default is `center`.
	 *     @type bool     $show_wrapper           Display the wrapper or not. Default is `true`.
	 *     @type bool     $show_flags             Display the flags or not. Default is `false`.
	 *     @type string   $flag_aspect_ratio      Flags aspect ratio. Possible values are `32` and `11`. Default is `32`.
	 *     @type string   $show_labels            Display the labels. Possible values are an empty string (no labels), `names` (language names), `codes` (languages codes). Default is `names`.
	 *     @type bool     $hide_if_empty          Hide languages that don't have any posts. Default is `true`.
	 *     @type bool     $hide_if_no_translation Hide languages that don't have a translation. Default is `false`.
	 *     @type bool     $hide_current           Hide the current language. Default is `false`.
	 *     @type bool     $force_home             Force elements to link to the home pages instead of the translations. Default is `false`.
	 *     @type int      $post_id                Build the links according to the translations of the given post ID. Default is `0`.
	 *     @type string[] $wrapper_classes        HTML classes to add to the wrapper. Default is an empty array.
	 *     @type string[] $item_classes           HTML classes to add to each item. Default is an empty array.
	 *     @type string[] $link_classes           HTML classes to add to each link. Default is an empty array.
	 *     @type string   $context                Tell in which context the switcher is displayed. Possible values are `frontend`, `admin`. Default is `frontend`.
	 *     @type string   $current_language_code  Current language's code.
	 * }
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 *
	 * @phpstan-param OptionalSettings $settings
	 */
	public function __construct( array $settings, PLL_Links $links ) {
		/**
		 * Filter the language switcher settings.
		 *
		 * @since 3.9
		 *
		 * @param array $settings Settings.
		 */
		$settings = apply_filters( 'pll_language_switcher_settings', $settings );

		foreach ( array_intersect_key( $settings, get_class_vars( self::class ) ) as $name => $value ) {
			$this->$name = $value;
		}

		if ( ! $this->show_flags && empty( $this->show_labels ) ) {
			// Make sure something is displayed.
			$this->show_labels = 'names';
		}

		if ( $links instanceof PLL_Admin_Links ) {
			// Force not to hide the language for the widget preview even if the option is checked.
			$this->hide_if_no_translation = false;
		}
	}
}
