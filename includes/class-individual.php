<?php
/**
 * Individual loader class.
 *
 * Handles Individual functionality.
 *
 * @package SOF_People
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Individual class.
 *
 * A class that encapsulates Individual functionality.
 *
 * @since 1.0.0
 */
class SOF_People_Individual {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var SOF_People
	 */
	public $plugin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var SOF_People_Individual_CPT
	 */
	public $cpt;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param SOF_People $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_people/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this class.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap object.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_people/individual/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes required files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {

		// Include class files.
		require SOF_PEOPLE_PATH . 'includes/class-individual-cpt.php';

	}

	/**
	 * Sets up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	private function setup_objects() {

		// Init objects.
		$this->cpt = new SOF_People_Individual_CPT( $this );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {

	}

}
