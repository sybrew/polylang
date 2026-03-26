<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Dropdown;

use WP_Syntex\Polylang\Language_Switcher\Nav;
use WP_Syntex\Polylang\Language_Switcher\Element;

defined( 'ABSPATH' ) || exit;

/**
 * Class that displays a language switcher as a list.
 *
 * @since 3.9
 */
class Switcher extends Nav\Switcher {
	/**
	 * Returns the markup of the switcher.
	 *
	 * @since 3.9.0
	 *
	 * @param \PLL_Language[] $languages A list of language instances.
	 * @return string
	 */
	public function get( array $languages ): string {
		$out      = "\n";
		$current  = null;
		$elements = array();

		if ( $this->settings->hide_current ) {
			// We need the current item to be displayed outside the list.
			$this->settings->hide_current = false;
			foreach ( $this->get_elements( $languages )->get() as $element ) {
				if ( $element->is_current ) {
					$current = $element;
				} else {
					$elements[] = $this->get_item( $element );
				}
			}
			$this->settings->hide_current = true;
		} else {
			foreach ( $this->get_elements( $languages )->get() as $element ) {
				if ( $element->is_current ) {
					$current = $element;
				}
				$elements[] = $this->get_item( $element );
			}
		}

		$out = implode( $elements );

		if ( trim( $out ) === '' || empty( $current ) ) {
			return $out;
		}

		$out = sprintf(
			"\n%s%s<ul>%s</ul>",
			$this->get_current_item( $current ),
			$this->get_button(),
			$out
		);

		if ( ! $this->settings->show_wrapper ) {
			return $out;
		}

		$outer_wrapper_classes = array_merge(
			$this->settings->wrapper_classes,
			array(
				'pll-switcher',
				"pll-layout-{$this->settings->layout}",
				"pll-alignment-{$this->settings->alignment}",
			)
		);

		if ( $this->settings->show_flags ) {
			$outer_wrapper_classes[] = "pll-aspect-ratio-{$this->settings->flag_aspect_ratio}";
		}

		$tag = $this->supports_html5() ? 'nav' : 'div';
		$out = sprintf(
			'<%1$s class="%2$s" aria-label="%3$s">%4$s</%1$s>',
			$tag,
			esc_attr( implode( ' ', $outer_wrapper_classes ) ),
			esc_attr( __( 'Choose a language', 'polylang' ) ),
			$out
		);

		return "\n{$out}\n";
	}

	/**
	 * Returns the markup of the current item.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	protected function get_current_item( Element $element ): string {
		return sprintf(
			'<a lang="%1$s" hreflang="%1$s" href="%2$s" class="current-lang" aria-current="true">%3$s</a>',
			esc_attr( $element->locale ),
			esc_url( $element->url ),
			$this->get_item_label( $element )
		);
	}

	/**
	 * Returns the markup of the button.
	 *
	 * @since 3.9
	 *
	 * @return string
	 */
	protected function get_button(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
		return sprintf(
			'<button aria-label="%1$s" class="pll-submenu-toggle" aria-expanded="false">%2$s</button>',
			esc_attr( __( 'Languages submenu', 'polylang' ) ),
			$svg
		);
	}
}
