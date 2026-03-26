/**
 * Handles the options in the language switcher nav menu metabox.
 *
 * @package Polylang
 */

const pllNavMenu = {
	/**
	 * The element wrapping the menu elements.
	 *
	 * @member {HTMLElement|null}
	 */
	buttons: null,

	/**
	 * Init.
	 */
	init: () => {
		if ( document.readyState !== 'loading' ) {
			pllNavMenu.ready();
		} else {
			document.addEventListener( 'DOMContentLoaded', pllNavMenu.ready );
		}
	},

	/**
	 * Called when the DOM is ready. Attaches the events to the wrapper.
	 */
	ready: () => {
		pllNavMenu.buttons = document.getElementsByClassName( 'pll-submenu-toggle' );

		for ( var i = 0; i < pllNavMenu.buttons.length; i++ ) {
			pllNavMenu.buttons[ i ].addEventListener( 'click', pllNavMenu.openCloseSubmenu );
		}
	},

	openCloseSubmenu: {
		/**
		 * Event callback that opens or closes a submenu.
		 *
		 * @param {Event} event The event.
		 */
		handleEvent: ( event ) => {
			const expanded = event.currentTarget.getAttribute( 'aria-expanded' );
			event.currentTarget.setAttribute( 'aria-expanded', 'true' === expanded ? 'false' : 'true' );
		}
	}
};

pllNavMenu.init();
