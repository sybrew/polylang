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
		$out = '';

		foreach ( $this->get_elements( $languages )->get() as $element ) {
			$out .= $this->get_item( $element );
		}

		if ( ! $this->settings->show_wrapper || empty( $out ) ) {
			return $out;
		}

		$out = sprintf(
			'<%1$s id="pll-switcher-%2$s" class="%3$s" aria-label="%4$s"><ul>%5$s</ul></%1$s>',
			$this->supports_html5() ? 'nav' : 'div',
			esc_attr( $this->settings->unique_id ),
			esc_attr( implode( ' ', $this->get_wrapper_classes() ) ),
			esc_attr( __( 'Choose a language', 'polylang' ) ),
			"\n{$out}"
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
			static::get_item_label( $element )
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
	public static function get_item_label( Element $element ): string {
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
