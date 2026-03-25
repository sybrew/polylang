<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Widgets;

use WP_Widget;
use WP_Syntex\Polylang\Language_Switcher\Admin;

/**
 * The advanced language switcher widget.
 *
 * @since 3.9
 *
 * @extends WP_Widget<T>
 * @phpstan-template T of array{
 *     title: string,
 *     layout: 'horizontal'|'vertical'|'dropdown',
 *     alignment: 'left'|'center'|'right'|'stretched',
 *     show_flags: bool,
 *     flag_aspect_ratio: '32'|'11',
 *     show_labels: ''|'names'|'codes',
 *     hide_if_no_translation: bool,
 *     hide_current: bool,
 *     force_home: bool
 * }
 */
class Languages extends WP_Widget {
	/**
	 * Constructor.
	 *
	 * @since 3.9
	 */
	public function __construct() {
		parent::__construct(
			'advanced_polylang',
			__( 'Advanced language switcher', 'polylang' ),
			array(
				'description'                 => __( 'Displays a language switcher', 'polylang' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Displays the widget.
	 *
	 * @since 3.9
	 *
	 * @param array $args     Arguments, including `before_title`, `after_title`, `before_widget`, and `after_widget`.
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 *
	 * @phpstan-param array{
	 *     name: string,
	 *     id: string,
	 *     description: string,
	 *     class: string,
	 *     before_widget: string,
	 *     after_widget: string,
	 *     before_title: string,
	 *     after_title: string,
	 *     before_sidebar: string,
	 *     after_sidebar: string,
	 *     show_in_rest: boolean,
	 *     widget_id: string,
	 *     widget_name: string
	 * } $args
	 * @phpstan-param T $instance
	 */
	public function widget( $args, $instance ): void {
		if ( 'dropdown' !== $instance['layout'] ) {
			if ( empty( PLL()->switcher ) ) {
				return;
			}
			$list = PLL()->switcher->get( $instance, PLL()->links );
		} else {
			// Sets a unique id for dropdown.
			$languages_args = array(
				'dropdown'               => $this->id,
				'show_names'             => 1,
				'show_flags'             => (int) $instance['show_flags'],
				'force_home'             => (int) $instance['force_home'],
				'hide_current'           => (int) $instance['hide_current'],
				'hide_if_no_translation' => (int) $instance['hide_if_no_translation'],
				'echo'                   => 0,
			);

			if ( empty( $instance['show_labels'] ) ) {
				$languages_args['show_names'] = 0;
			} elseif ( 'codes' === $instance['show_labels'] ) {
				$languages_args['display_names_as'] = 'slug';
			}

			$list = pll_the_languages( $languages_args );
		}

		if ( empty( $list ) ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'] ?? '', $instance, $this->id_base );

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		if ( 'dropdown' === $instance['layout'] ) {
			printf( '<div class="pll-switcher pll-layout-dropdown pll-alignment-%s">', esc_attr( $instance['alignment'] ) );

			// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
			$aria_label = trim( wp_strip_all_tags( $title ) );

			if ( ! $aria_label ) {
				$aria_label = __( 'Choose a language', 'polylang' );
			}

			printf(
				'<label class="screen-reader-text" for="lang_choice_%s">%s</label>',
				esc_attr( (string) $languages_args['dropdown'] ),
				esc_html( $aria_label )
			);

			echo "{$list}</div>"; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo $list; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Updates the widget options.
	 *
	 * @since 3.9
	 *
	 * @param array $new_instance New settings for this instance as input by the user via `form()`.
	 * @param array $old_instance Old settings for this instance.
	 * @return array|bool Settings to save or bool false to cancel saving.
	 *
	 * @phpstan-param T $new_instance
	 * @phpstan-param T $old_instance
	 */
	public function update( $new_instance, $old_instance ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$instance = array_merge(
			array( 'title' => '' ),
			Admin::get_options( 'default' )
		);

		if ( ! empty( $new_instance['title'] ) ) {
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
		}

		if ( isset( $new_instance['layout'] ) && in_array( $new_instance['layout'], array( 'dropdown', 'horizontal', 'vertical' ), true ) ) {
			$instance['layout'] = $new_instance['layout'];
		}

		if ( isset( $new_instance['alignment'] ) && in_array( $new_instance['alignment'], array( 'left', 'center', 'right', 'stretched' ), true ) ) {
			$instance['alignment'] = $new_instance['alignment'];
		}

		if ( isset( $new_instance['flag_aspect_ratio'] ) && in_array( $new_instance['flag_aspect_ratio'], array( '32', '11' ), true ) ) {
			$instance['flag_aspect_ratio'] = $new_instance['flag_aspect_ratio'];
		}

		if ( isset( $new_instance['show_labels'] ) && in_array( $new_instance['show_labels'], array( '', 'names', 'codes' ), true ) ) {
			$instance['show_labels'] = $new_instance['show_labels'];
		}

		foreach ( array( 'show_flags', 'force_home', 'hide_current', 'hide_if_no_translation' ) as $key ) {
			$instance[ $key ] = ! empty( $new_instance[ $key ] );
		}

		return $instance;
	}

	/**
	 * Displays the widget form.
	 *
	 * @since 3.9
	 *
	 * @param array $instance Current settings.
	 * @return void
	 *
	 * @phpstan-param T $instance
	 */
	public function form( $instance ): void {
		$labels_and_data = Admin::get_options();
		$instance        = wp_parse_args(
			(array) $instance,
			array_merge( array( 'title' => '' ), wp_list_pluck( $labels_and_data, 'default' ) )
		);

		// Title
		printf(
			'<p><label for="%1$s">%2$s</label><input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html__( 'Title:', 'polylang' ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $instance['title'] )
		);

		echo '<div class="advanced_polylang-widget-content">';

		// Layout.
		$this->print_select( 'layout', $labels_and_data['layout'], $instance );

		// Alignment.
		$this->print_select( 'alignment', $labels_and_data['alignment'], $instance );

		// Display flags.
		$this->print_checkbox( 'show_flags', $labels_and_data['show_flags'], $instance, array( 'layout' => 'dropdown' ) );

		// Flag aspect ratio.
		$this->print_select( 'flag_aspect_ratio', $labels_and_data['flag_aspect_ratio'], $instance, array( 'layout' => 'dropdown', 'show_flags' => false ) );

		// Display labels.
		$this->print_select( 'show_labels', $labels_and_data['show_labels'], $instance, array( 'layout' => 'dropdown' ) );

		// Force link to front page, hide languages.
		foreach ( array( 'force_home', 'hide_current', 'hide_if_no_translation' ) as $name ) {
			$this->print_checkbox( $name, $labels_and_data[ $name ], $instance, 'hide_current' === $name ? array( 'layout' => 'dropdown' ) : array() );
		}

		echo '</div>';
	}

	/**
	 * Prints a `<select>` setting.
	 *
	 * @since 3.9
	 *
	 * @param string          $key            Setting key.
	 * @param array           $label_and_data Setting label and choices.
	 * @param (string|bool)[] $values         Widget's settings.
	 * @param (string|bool)[] $hidden_if      Optional. Hides the input if the given conditions are met. Default is an empty array.
	 *                                        Ex: `array( 'layout' => 'dropdown' )` will hide the input if the layout is `dropdown`.
	 * @return void
	 */
	private function print_select( string $key, array $label_and_data, array $values, array $hidden_if = array() ): void {
		$this->print_wrapper_start( $values, $hidden_if );
		printf(
			'<label for="%s">%s</label> ',
			esc_attr( $this->get_field_id( $key ) ),
			esc_html( $label_and_data['label'] )
		);
		printf(
			'<select data-key="%1$s" id="%2$s" name="%3$s">',
			esc_attr( $key ),
			esc_attr( $this->get_field_id( $key ) ),
			esc_attr( $this->get_field_name( $key ) )
		);
		foreach ( $label_and_data['choices'] as $value => $label ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $value ),
				selected( $values[ $key ], $value, false ),
				esc_html( $label )
			);
		}
		echo '</select></div>';
	}

	/**
	 * Prints a checkbox setting.
	 *
	 * @since 3.9
	 *
	 * @param string          $key            Setting key.
	 * @param array           $label_and_data Setting label.
	 * @param (string|bool)[] $values         Widget's settings.
	 * @param (string|bool)[] $hidden_if      Optional. Hides the input if the given conditions are met. Default is an empty array.
	 *                                        Ex: `array( 'layout' => 'dropdown' )` will hide the input if the layout is `dropdown`.
	 * @return void
	 */
	private function print_checkbox( string $key, array $label_and_data, array $values, array $hidden_if = array() ): void {
		$this->print_wrapper_start( $values, $hidden_if );
		printf(
			'<input type="checkbox" data-key="%1$s" class="checkbox" id="%2$s" name="%3$s"%4$s/><label for="%2$s">%5$s</label>',
			esc_attr( $key ),
			esc_attr( $this->get_field_id( $key ) ),
			esc_attr( $this->get_field_name( $key ) ),
			checked( $values[ $key ], true, false ),
			esc_html( $label_and_data['label'] )
		);
		echo '</div>';
	}

	/**
	 * Prints the start of the outer wrapper.
	 *
	 * @since 3.9
	 *
	 * @param (string|bool)[] $values    Widget's settings.
	 * @param (string|bool)[] $hidden_if Hides the input if the given conditions are met.
	 *                                   Ex: `array( 'layout' => 'dropdown' )` will hide the input if the layout is `dropdown`.
	 * @return void
	 */
	private function print_wrapper_start( array $values, array $hidden_if ): void {
		if ( empty( $hidden_if ) ) {
			echo '<div>';
			return;
		}

		$classes = array();

		foreach ( $hidden_if as $key => $value ) {
			if ( $values[ $key ] === $value ) {
				$classes[] = "pll-hidden-by-{$key}";
			}
			$value     = is_bool( $value ) ? (int) $value : $value;
			$classes[] = "pll-hidden-if-{$key}-{$value}";
		}

		printf(
			'<div class="%s">',
			esc_attr( implode( ' ', $classes ) )
		);
	}
}
