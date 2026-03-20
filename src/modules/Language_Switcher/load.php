<?php
/**
 * @package Polylang
 */

namespace WP_Syntex\Polylang\Language_Switcher;

defined( 'ABSPATH' ) || exit;

add_action(
	'pll_init',
	function ( $polylang ) {
		if ( $polylang->model->has_languages() ) {
			$polylang->switcher = ( new Switcher() )->init();
		}
	}
);
