<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers\Types;

use WP_Syntex\Polylang\Language_Switcher\Element;

defined( 'ABSPATH' ) || exit;

/**
 * Class that generates the markup for a language switcher displayed as a list.
 *
 * @since 3.9
 */
class Nav extends Abstract_Type {
	/**
	 * Wraps the given markup.
	 *
	 * @since 3.9
	 *
	 * @param string $inner Inner markup.
	 * @return string
	 */
	public function wrap( string $inner ): string {
		$cr  = $this->settings->preserve_spacing ? "\n" : '';
		$out = sprintf(
			'<%1$s id="%2$s" class="%3$s" aria-label="%4$s">%5$s</%1$s>',
			$this->supports_html5() ? 'nav' : 'div',
			esc_attr( $this->settings->unique_id ),
			esc_attr( implode( ' ', $this->get_wrapper_classes() ) ),
			esc_attr( __( 'Choose a language', 'polylang' ) ),
			"{$cr}{$inner}"
		);

		return "{$cr}{$out}{$cr}";
	}

	/**
	 * Returns the markup of a row.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	public function get_row( Element $element ): string {
		$link_atts = sprintf(
			'lang="%1$s" hreflang="%1$s" href="%2$s"',
			esc_attr( $element->locale ),
			esc_url( $element->url )
		);

		if ( ! empty( $element->link_classes ) ) {
			$link_atts .= sprintf(
				' class="%s"',
				esc_attr( implode( ' ', $element->link_classes ) )
			);
		}

		if ( $element->is_current ) {
			$link_atts .= ' aria-current="true"';
		}

		$out = sprintf(
			'<li class="%s"><a %s>%s</a></li>',
			esc_attr( implode( ' ', $element->item_classes ) ),
			$link_atts,
			$this->get_row_inner( $element )
		);

		if ( ! $this->settings->preserve_spacing ) {
			return $out;
		}

		return "\t{$out}\n";
	}

	/**
	 * Returns the markup of the label of a row.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	public function get_row_inner( Element $element ): string {
		if ( ! empty( $element->flag ) && ! empty( $element->label ) ) {
			return sprintf(
				'<span class="pll-switcher-flag">%s</span><span>%s</span>',
				(string) preg_replace( '/ style="[^"]*"/', '', $element->flag ),
				esc_html( $element->label )
			);
		}

		if ( ! empty( $element->flag ) ) {
			return (string) preg_replace( '/ style="[^"]*"/', '', $element->flag );
		}

		return esc_html( $element->label );
	}
}
