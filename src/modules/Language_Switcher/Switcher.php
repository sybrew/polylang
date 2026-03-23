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
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param PLL_Model $model Polylang's model.
	 */
	public function __construct( PLL_Model $model ) {
		$this->model = $model;
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

		wp_enqueue_style( 'pll-language-switcher', plugins_url( "/css/build/switcher{$suffix}.css", POLYLANG_FILE ), array(), POLYLANG_FILE );
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
	 * @param array     $settings Settings.
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 * @return void
	 */
	public function print( array $settings, PLL_Links $links ): void {
		echo $this->get( $settings, $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Returns the switcher's markup.
	 *
	 * @since 3.9
	 *
	 * @param array     $settings Settings.
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 * @return string
	 */
	public function get( array $settings, PLL_Links $links ): string {
		$settings = new Settings( $settings, $links );
		$switcher = $this->get_switcher( $settings, $links );

		if ( empty( $switcher ) ) {
			return '';
		}

		return $switcher->get(
			$this->get_languages( $settings, $links )
		);
	}

	/**
	 * Returns the switcher's raw data.
	 *
	 * @since 3.9
	 *
	 * @param array     $settings Settings.
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 * @return Element[]
	 */
	public function get_elements( array $settings, PLL_Links $links ): array {
		$settings = new Settings( $settings, $links );
		$switcher = $this->get_switcher( $settings, $links );

		if ( empty( $switcher ) ) {
			return array();
		}

		return $switcher->get_elements(
			$this->get_languages( $settings, $links )
		)->get();
	}

	/**
	 * Returns an instance of the switcher.
	 *
	 * @since 3.9
	 *
	 * @param Settings  $settings Instance of `Settings`.
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 * @return Base\Abstract_Switcher|null
	 */
	private function get_switcher( Settings $settings, PLL_Links $links ): ?Base\Abstract_Switcher {
		switch ( $settings->layout ) {
			case 'horizontal':
			case 'vertical':
				return new Nav\Switcher( $settings, $links );

			default:
				return null;
		}
	}

	/**
	 * Returns the list of languages.
	 *
	 * @since 3.9
	 *
	 * @param Settings  $settings Instance of `Settings`.
	 * @param PLL_Links $links    Instance of `PLL_Links`.
	 * @return \PLL_Language[]
	 */
	private function get_languages( Settings $settings, PLL_Links $links ): array {
		$filter = $settings->hide_if_empty ? 'hide_empty' : '';
		return $links->model->languages->filter( $filter )->get_list();
	}
}
