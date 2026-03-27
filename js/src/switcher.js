/**
 * Handles the options in the language switcher nav menu metabox.
 *
 * @package Polylang
 */

const pllSwitcher = {
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
			pllSwitcher.ready();
		} else {
			document.addEventListener( 'DOMContentLoaded', pllSwitcher.ready );
		}
	},

	/**
	 * Called when the DOM is ready. Attaches the events to the wrapper.
	 */
	ready: () => {
		pllSwitcher.buttons = document.getElementsByClassName( 'pll-submenu-toggle' );

		for ( var i = 0; i < pllSwitcher.buttons.length; i++ ) {
			pllSwitcher.buttons[ i ].addEventListener( 'click', pllSwitcher.openCloseSubmenu );
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
			event.currentTarget.setAttribute( 'aria-label', 'true' === expanded ? pllSwitcherI18n.openDropdown : pllSwitcherI18n.closeDropdown );
		}
	}
};

pllSwitcher.init();
