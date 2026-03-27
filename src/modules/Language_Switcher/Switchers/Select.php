<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use WP_Syntex\Polylang\Language_Switcher\Element;

defined( 'ABSPATH' ) || exit;

/**
 * Class that displays a language switcher as a selector.
 *
 * @since 3.9
 */
class Select extends Abstract_Switcher {
	/**
	 * Returns the markup of the switcher.
	 *
	 * @since 3.9.0
	 *
	 * @param \PLL_Language[] $languages A list of language instances.
	 * @return string
	 */
	public function get( array $languages ): string {
		$out = "\n";

		foreach ( $this->get_elements( $languages )->get() as $element ) {
			$out .= $this->get_item( $element );
		}

		if ( ! $this->settings->show_wrapper || trim( $out ) === '' ) {
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

		$out = sprintf(
			'<div class="%1$s"><label class="screen-reader-text" for="%2$s">%3$s</label><select class="pll-switcher-select" id="%2$s">%4$s</select></div>',
			esc_attr( implode( ' ', $outer_wrapper_classes ) ),
			"lang_choice_polylang-{$this->settings->unique_id}",
			esc_html( __( 'Choose a language', 'polylang' ) ),
			$out
		);

		return "\n{$out}\n";
	}

	/**
	 * Returns the markup of an item.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	protected function get_item( Element $element ): string {
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
			esc_html( $element->label )
		);

		return "\t{$out}\n";
	}
}
