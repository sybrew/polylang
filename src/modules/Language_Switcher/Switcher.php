<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

use PLL_Links;
use PLL_Model;

defined( 'ABSPATH' ) || exit;

/**
 * Class that can display a language switcher.
 *
 * @since 3.9
 *
 * @phpstan-import-type OptionalSettings from Settings
 */
class Switcher {
	/**
	 * @var PLL_Model
	 */
	private $model;

	/**
	 * @var PLL_Links
	 */
	private $links;

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param PLL_Model $model Polylang's model.
	 * @param PLL_Links $links Instance of `PLL_Links`.
	 */
	public function __construct( PLL_Model $model, PLL_Links $links ) {
		$this->model = $model;
		$this->links = $links;
	}

	/**
	 * Adds hooks.
	 *
	 * @since 3.9
	 *
	 * @return self
	 */
	public function init(): self {
		if ( $this->model->has_languages() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_admin_styles' ) );
		}
		return $this;
	}

	/**
	 * Enqueues CSS styles.
	 *
	 * @since 3.9
	 *
	 * @return void
	 */
	public function enqueue_styles(): void {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'pll-language-switcher', plugins_url( "/css/build/switcher{$suffix}.css", POLYLANG_FILE ), array(), POLYLANG_VERSION );
	}

	/**
	 * Enqueues CSS styles.
	 *
	 * @since 3.9
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'pll-language-switcher', plugins_url( "/js/build/switcher{$suffix}.js", POLYLANG_FILE ), array(), POLYLANG_VERSION, true );

		$i18n = array( 'openDropdown' => __( 'Open languages submenu', 'polylang' ), 'closeDropdown' => __( 'Close languages submenu', 'polylang' ) );
		wp_localize_script( 'pll-language-switcher', 'pllSwitcherI18n', $i18n );
	}

	/**
	 * Maybe enqueues CSS styles in admin.
	 *
	 * @since 3.9
	 *
	 * @return void
	 */
	public function maybe_enqueue_admin_styles(): void {
		$screen = get_current_screen();

		if ( empty( $screen ) ) {
			return;
		}

		if ( 'post' === $screen->base && ! empty( $screen->post_type ) && $this->model->is_translated_post_type( $screen->post_type ) ) {
			$this->enqueue_styles();
			return;
		}

		if ( 'term' === $screen->base && ! empty( $screen->taxonomy ) && $this->model->is_translated_taxonomy( $screen->taxonomy ) ) {
			$this->enqueue_styles();
			return;
		}

		if ( 'site-editor' === $screen->base ) {
			$this->enqueue_styles();
			return;
		}
	}

	/**
	 * Prints the switcher.
	 *
	 * @since 3.9
	 *
	 * @param array $settings Settings.
	 * @return void
	 *
	 * @phpstan-param OptionalSettings $settings
	 */
	public function print( array $settings ): void {
		echo $this->get( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Returns the switcher's markup.
	 *
	 * @since 3.9
	 *
	 * @param array $settings Settings.
	 * @return string
	 *
	 * @phpstan-param OptionalSettings $settings
	 */
	public function get( array $settings ): string {
		$settings = new Settings( $settings, $this->links );
		$switcher = $this->get_switcher( $settings );

		if ( empty( $switcher ) ) {
			return '';
		}

		$html = $switcher->get( $this->get_languages( $settings ) );

		/**
		 * Filter the whole switcher markup.
		 *
		 * @since 3.9.0
		 *
		 * @param string   $html     Switcher markup.
		 * @param Settings $settings Switcher settings.
		 */
		return (string) apply_filters( 'pll_language_switcher', $html, $settings );
	}

	/**
	 * Returns the switcher's raw data.
	 *
	 * @since 3.9
	 *
	 * @param array $settings Settings.
	 * @return Element[]
	 *
	 * @phpstan-param OptionalSettings $settings
	 */
	public function get_elements( array $settings ): array {
		$settings = new Settings( $settings, $this->links );
		$switcher = $this->get_switcher( $settings );

		if ( empty( $switcher ) ) {
			return array();
		}

		return $switcher->get_elements(
			$this->get_languages( $settings )
		)->get();
	}

	/**
	 * Returns an instance of the switcher.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Instance of `Settings`.
	 * @return Switchers\Abstract_Switcher|null
	 */
	private function get_switcher( Settings $settings ): ?Switchers\Abstract_Switcher {
		switch ( $settings->layout ) {
			case 'horizontal':
			case 'vertical':
				return new Switchers\Nav( $settings, $this->links );

			case 'dropdown':
				return new Switchers\Dropdown( $settings, $this->links );

			case 'select':
				return new Switchers\Select( $settings, $this->links );

			default:
				return null;
		}
	}

	/**
	 * Returns the list of languages.
	 *
	 * @since 3.9
	 *
	 * @param Settings $settings Instance of `Settings`.
	 * @return \PLL_Language[]
	 */
	private function get_languages( Settings $settings ): array {
		$filter = $settings->hide_if_empty ? 'hide_empty' : '';
		return $this->links->model->languages->filter( $filter )->get_list();
	}
}
