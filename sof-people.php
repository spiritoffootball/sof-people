<?php
/**
 * SOF People
 *
 * Plugin Name:       SOF People
 * Description:       Provides Custom Post Types for displaying people on the Spirit of Football website.
 * Plugin URI:        https://github.com/spiritoffootball/sof-people
 * GitHub Plugin URI: https://github.com/spiritoffootball/sof-people
 * Version:           1.0.1a
 * Author:            Christian Wach
 * Author URI:        https://haystack.co.uk
 * Text Domain:       sof-people
 * Domain Path:       /languages
 *
 * @package SOF_People
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_PEOPLE_VERSION', '1.0.1a' );

// Store reference to this file.
if ( ! defined( 'SOF_PEOPLE_FILE' ) ) {
	define( 'SOF_PEOPLE_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'SOF_PEOPLE_URL' ) ) {
	define( 'SOF_PEOPLE_URL', plugin_dir_url( SOF_PEOPLE_FILE ) );
}
// Store PATH to this plugin's directory.
if ( ! defined( 'SOF_PEOPLE_PATH' ) ) {
	define( 'SOF_PEOPLE_PATH', plugin_dir_path( SOF_PEOPLE_FILE ) );
}

/**
 * Main Plugin Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 1.0.0
 */
class SOF_People {

	/**
	 * Individual loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var SOF_People_Individual
	 */
	public $individual;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Initialise on "plugins_loaded".
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Do stuff on plugin init.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap plugin.
		$this->translation();
		$this->include_files();
		$this->setup_objects();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_people/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Enable translation.
	 *
	 * @since 1.0.0
	 */
	public function translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'sof-people', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_PEOPLE_FILE ) ) . '/languages/' // Relative path to files.
		);

	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {

		// Include class files.
		include SOF_PEOPLE_PATH . 'includes/class-individual.php';

	}

	/**
	 * Set up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Init objects.
		$this->individual = new SOF_People_Individual( $this );

	}

	/**
	 * Perform plugin activation tasks.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin activation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_people/activate' );

	}

	/**
	 * Perform plugin deactivation tasks.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_people/deactivate' );

	}

}

/**
 * Utility to get a reference to this plugin.
 *
 * @since 1.0.0
 *
 * @return SOF_People $plugin The plugin reference.
 */
function sof_people() {

	// Store instance in static variable.
	static $plugin = false;

	// Maybe return instance.
	if ( false === $plugin ) {
		$plugin = new SOF_People();
	}

	// --<
	return $plugin;

}

// Initialise plugin now.
sof_people();

// Activation.
register_activation_hook( __FILE__, [ sof_people(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ sof_people(), 'deactivate' ] );

/*
 * Uninstall uses the 'uninstall.php' method.
 *
 * @see https://codex.wordpress.org/Function_Reference/register_uninstall_hook
 */
