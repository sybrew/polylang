<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

use WP_Post;
use PLL_Links;
use PLL_Language;
use PLL_Frontend_Links;

defined( 'ABSPATH' ) || exit;

/**
 * Class that builds the data for the language switcher.
 *
 * @since 3.9
 *
 * @phpstan-import-type ElementData from Element
 */
class Elements {
	/**
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * @var PLL_Links
	 */
	protected PLL_Links $links;

	/**
	 * @var PLL_Language[]
	 */
	protected array $languages;

	/**
	 * Constructor.
	 *
	 * @since 3.9
	 *
	 * @param Settings       $settings  Instance of `Settings`.
	 * @param PLL_Links      $links     Instance of `PLL_Links`.
	 * @param PLL_Language[] $languages A list of language instances.
	 */
	public function __construct( Settings $settings, PLL_Links $links, array $languages ) {
		$this->settings  = $settings;
		$this->links     = $links;
		$this->languages = $languages;
	}

	/**
	 * Returns the switcher's data.
	 *
	 * @since 3.9
	 *
	 * @return Element[]
	 */
	public function get(): array {
		$is_first = true;
		$current  = $this->get_current_language();
		$out      = array();

		foreach ( $this->languages as $language ) {
			$item_classes = array_merge(
				array( 'lang-item', "lang-item-{$language->term_id}", "lang-item-{$language->slug}" ),
				$this->settings->item_classes
			);
			$element     = array(
				'id'           => (int) $language->term_id,
				'label'        => '',
				'slug'         => $language->slug,
				'locale'       => $language->get_locale( 'display' ),
				'url'          => '',
				'flag'         => '',
				'is_current'   => $current === $language->slug,
				'item_classes' => $item_classes,
				'link_classes' => $this->settings->link_classes,
			);

			if ( $element['is_current'] ) {
				if ( $this->settings->hide_current ) {
					// Hide current item.
					continue;
				}
				$element['item_classes'][] = 'current-lang';
			}

			$element = $this->add_url( $element, $language );

			if ( empty( $element['url'] ) ) {
				// Failed to get a URL.
				continue;
			}

			if ( ! empty( $this->settings->show_labels ) ) {
				$element['label'] = 'codes' === $this->settings->show_labels ? $language->slug : $language->name;
			}

			$element['flag'] = $this->get_element_flag( $language );

			if ( $is_first ) {
				$element['item_classes'][] = 'lang-item-first';
				$is_first                  = false;
			}

			$out[ $language->slug ] = new Element( $element );
		}

		return $out;
	}

	/**
	 * Fills the `url` key of the given element.
	 *
	 * @since 3.9
	 *
	 * @param array        $element  Element data.
	 * @param PLL_Language $language A language instance.
	 * @return array
	 *
	 * @phpstan-param ElementData $element
	 * @phpstan-return ElementData
	 */
	protected function add_url( array $element, PLL_Language $language ): array {
		if ( $this->settings->force_home ) {
			$element['url'] = $this->links->get_home_url( $language );
			return $element;
		}

		$element['url'] = $this->get_element_original_url( $language );

		if ( empty( $element['url'] ) ) {
			$element['item_classes'][] = 'no-translation';
		}

		/**
		 * Filter the link in the language switcher.
		 *
		 * @since 0.7
		 * @since 3.9 Return an empty string instead of `null`.
		 *
		 * @param string $url    The link URL, an empty string if no translation was found.
		 * @param string $slug   The language code.
		 * @param string $locale The language locale.
		 */
		$element['url'] = apply_filters( 'pll_the_language_link', $element['url'], $language->slug, $language->locale );

		if ( empty( $element['url'] ) && ! $this->settings->hide_if_no_translation ) {
			$element['url'] = $this->links->get_home_url( $language );
		}

		return $element;
	}

	/**
	 * Returns the original URL of the element.
	 *
	 * @since 3.9.0
	 *
	 * @param PLL_Language $language A language instance.
	 * @return string
	 */
	protected function get_element_original_url( PLL_Language $language ): string {
		global $post;

		// Priority to the post passed in parameters.
		if ( $this->settings->post_id ) {
			$tr_id = $this->links->model->post->get( $this->settings->post_id, $language );

			if ( $tr_id && $this->links->model->post->current_user_can_read( $tr_id ) ) {
				return (string) get_permalink( $tr_id );
			}
		}

		// If we are on frontend.
		if ( $this->links instanceof PLL_Frontend_Links ) {
			return $this->links->get_translation_url( $language );
		}

		// For blocks in posts in REST requests.
		if ( $post instanceof WP_Post ) {
			$tr_id = $this->links->model->post->get( $post->ID, $language );

			if ( $tr_id && $this->links->model->post->current_user_can_read( $tr_id ) ) {
				return (string) get_permalink( $tr_id );
			}
		}

		return '';
	}

	/**
	 * Returns the flag for the given language.
	 *
	 * @since 3.9.0
	 *
	 * @param PLL_Language $language A language instance.
	 * @return string
	 */
	protected function get_element_flag( PLL_Language $language ): string {
		if ( $this->settings->show_flags ) {
			return $language->get_display_flag( ! empty( $this->settings->show_labels ) ? 'no-alt' : 'alt' );
		}

		return '';
	}

	/**
	 * Returns the current language code.
	 *
	 * @since 3.9.0
	 *
	 * @return string
	 */
	protected function get_current_language(): string {
		if ( $this->settings->current_language_code ) {
			return $this->settings->current_language_code;
		}

		if ( isset( $this->links->curlang ) ) {
			return $this->links->curlang->slug;
		}

		return $this->links->options['default_lang'];
	}
}
