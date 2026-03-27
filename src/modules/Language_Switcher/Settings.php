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
 *     layout?: 'horizontal'|'vertical'|'dropdown'|'select',
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
 *     current_language_code?: string,
 *     unique_id?: string
 * }
 */
class Settings {
	/**
	 * @var string
	 *
	 * @phpstan-var 'horizontal'|'vertical'|'dropdown'|'select'
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
	 */
	public string $current_language_code = '';

	/**
	 * @var string
	 */
	public string $unique_id = '';

	/**
	 * @var int
	 */
	private static int $increment = 0;

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param array     $settings {
	 *     Optional switcher settings.
	 *
	 *     @type string   $layout                 Layout of the switcher. Possible values are `horizontal`, `vertical`,
	 *                                            `dropdown`, and `select`. Default is `horizontal`.
	 *     @type string   $alignment              Alignment of the items. Possible values are `left`, `center`, `right`,
	 *                                            `stretched`. Default is `center`.
	 *     @type bool     $show_wrapper           Display the wrapper or not. Default is `true`.
	 *     @type bool     $show_flags             Display the flags or not. Default is `false`.
	 *     @type string   $flag_aspect_ratio      Flags aspect ratio. Possible values are `32` and `11`. Default is `32`.
	 *     @type string   $show_labels            Display the labels. Possible values are an empty string (no labels),
	 *                                            `names` (language names), `codes` (languages codes). Default is `names`.
	 *     @type bool     $hide_if_empty          Hide languages that don't have any posts. Default is `true`.
	 *     @type bool     $hide_if_no_translation Hide languages that don't have a translation. Default is `false`.
	 *     @type bool     $hide_current           Hide the current language. Default is `false`.
	 *     @type bool     $force_home             Force elements to link to the home pages instead of the translations.
	 *                                            Default is `false`.
	 *     @type int      $post_id                Build the links according to the translations of the given post ID.
	 *                                            Default is `0`.
	 *     @type string[] $wrapper_classes        HTML classes to add to the wrapper. Default is an empty array.
	 *     @type string[] $item_classes           HTML classes to add to each item. Default is an empty array.
	 *     @type string[] $link_classes           HTML classes to add to each link. Default is an empty array.
	 *     @type string   $current_language_code  Current language's code.
	 *     @type string   $unique_id              A unique identifier. Default is an empty string.
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
		$settings   = apply_filters( 'pll_language_switcher_settings', $settings );
		$properties = array_diff_key( get_class_vars( self::class ), array( 'increment' => 0 ) );

		foreach ( array_intersect_key( $settings, $properties ) as $name => $value ) {
			$this->$name = $value;
		}

		if ( 'select' === $this->layout ) {
			$this->show_flags   = false;
			$this->show_labels  = 'names';
			$this->hide_current = false;
		}

		if ( ! $this->show_flags && empty( $this->show_labels ) ) {
			// Make sure something is displayed.
			$this->show_labels = 'names';
		}

		if ( $links instanceof PLL_Admin_Links || $this->force_home ) {
			// Force not to hide the language for the widget preview even if the option is checked.
			$this->hide_if_no_translation = false;
		}

		if ( '' === $this->unique_id ) {
			++self::$increment;
			$this->unique_id = (string) self::$increment;
		}
	}

	/**
	 * Returns options available for the language switcher.
	 *
	 * @since 3.9
	 *
	 * @param string $key Optional. Either `label` to return option labels, `default` to return default values,
	 *                    `choices` to return choices, `conditions` to return conditions, or an empty string to return
	 *                    everything. Defaults to an empty string.
	 * @return array List of switcher options strings, default values, options, or everything.
	 *
	 * @phpstan-param ''|'label'|'default'|'choices'|'conditions' $key
	 */
	public static function get_options( string $key = '' ): array {
		$options = array(
			'layout'                 => array(
				'label'   => __( 'Layout:', 'polylang' ),
				'default' => 'horizontal',
				'choices' => array(
					'horizontal' => __( 'Horizontal', 'polylang' ),
					'vertical'   => __( 'Vertical', 'polylang' ),
					'dropdown'   => __( 'Dropdown', 'polylang' ),
					'select'     => __( 'Selector', 'polylang' ),
				),
			),
			'alignment'              => array(
				'label'   => __( 'Alignment:', 'polylang' ),
				'default' => is_rtl() ? 'right' : 'left',
				'choices' => array(
					'left'      => _x( 'Left', 'alignment', 'polylang' ),
					'center'    => _x( 'Center', 'alignment', 'polylang' ),
					'right'     => _x( 'Right', 'alignment', 'polylang' ),
					'stretched' => _x( 'Stretched', 'alignment', 'polylang' ),
				),
			),
			'show_flags'             => array(
				'label'      => __( 'Display flags', 'polylang' ),
				'default'    => false,
				'conditions' => array(
					'layout' => 'select',
				),
			),
			'flag_aspect_ratio'      => array(
				'label'      => __( 'Flags aspect ratio:', 'polylang' ),
				'default'    => '32',
				'choices'    => array(
					'32' => '3:2',
					'11' => '1:1',
				),
				'conditions' => array(
					'layout'     => 'select',
					'show_flags' => false,
				),
			),
			'show_labels'            => array(
				'label'      => __( 'Display labels:', 'polylang' ),
				'default'    => 'names',
				'choices'    => array(
					''      => __( 'No', 'polylang' ),
					'names' => __( 'Language names', 'polylang' ),
					'codes' => __( 'Language codes', 'polylang' ),
				),
				'conditions' => array(
					'layout' => 'select',
				),
			),
			'force_home'             => array(
				'label'   => __( 'Force link to front page', 'polylang' ),
				'default' => false,
			),
			'hide_current'           => array(
				'label'      => __( 'Hide the current language', 'polylang' ),
				'default'    => false,
				'conditions' => array(
					'layout' => 'select',
				),
			),
			'hide_if_no_translation' => array(
				'label'      => __( 'Hide languages with no translation', 'polylang' ),
				'default'    => false,
				'conditions' => array(
					'force_home' => true,
				),
			),
		);

		if ( '' === $key ) {
			return $options;
		}

		if ( 'choices' === $key || 'conditions' === $key ) {
			$return = array();
			foreach ( $options as $name => $data ) {
				if ( isset( $data[ $key ] ) ) {
					$return[ $name ] = $data[ $key ];
				}
			}
			return $return;
		}

		return wp_list_pluck( $options, $key );
	}
}
