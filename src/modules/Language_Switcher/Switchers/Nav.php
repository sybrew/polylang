<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher\Switchers;

use WP_Syntex\Polylang\Language_Switcher\Element;

defined( 'ABSPATH' ) || exit;

/**
 * Class that displays a language switcher as a list.
 *
 * @since 3.9
 */
class Nav extends Abstract_Switcher {
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

		if ( $this->settings->show_flags ) {
			$outer_wrapper_classes[] = "pll-aspect-ratio-{$this->settings->flag_aspect_ratio}";
		}

		$tag = $this->supports_html5() ? 'nav' : 'div';
		$out = sprintf(
			'<%1$s class="%2$s" aria-label="%3$s"><ul>%4$s</ul></%1$s>',
			$tag,
			esc_attr( implode( ' ', $outer_wrapper_classes ) ),
			esc_attr( __( 'Choose a language', 'polylang' ) ),
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
			$this->get_item_label( $element )
		);

		return "\t{$out}\n";
	}

	/**
	 * Returns the markup of the label of an item.
	 *
	 * @since 3.9
	 *
	 * @param Element $element An element.
	 * @return string
	 */
	protected function get_item_label( Element $element ): string {
		if ( ! empty( $element->flag ) && ! empty( $element->label ) ) {
			return sprintf(
				'<span>%s</span><span>%s</span>',
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
