<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers\Types;

use WP_Syntex\Polylang\Language_Switcher\Element;

defined( 'ABSPATH' ) || exit;

/**
 * Class that generates the markup for a language switcher displayed as a selector.
 *
 * @since 3.9
 */
class Select extends Abstract_Type {
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
			'<div class="%1$s"><label class="screen-reader-text" for="%2$s">%3$s</label><select class="pll-switcher-select" id="%2$s">%4$s</select></div>',
			esc_attr( implode( ' ', $this->get_wrapper_classes() ) ),
			"lang_choice_polylang-{$this->settings->unique_id}",
			esc_html( __( 'Choose a language', 'polylang' ) ),
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
		$item_atts = sprintf(
			'lang="%1$s" value="%2$s"%3$s',
			esc_attr( $element->locale ),
			esc_url( $element->url ),
			selected( $element->is_current, true, false )
		);

		if ( ! empty( $element->item_classes ) ) {
			$item_atts .= sprintf(
				' class="%s"',
				esc_attr( implode( ' ', $element->item_classes ) )
			);
		}

		$out = sprintf(
			'<option %s>%s</option>',
			$item_atts,
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
		return esc_html( $element->label );
	}
}
