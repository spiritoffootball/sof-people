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
	 * @param object $parent The parent object.
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

		// Bootstrap object.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Broadcast that this class is now loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_people/individual/loaded' );

	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	public function include_files() {

		// Include class files.
		include SOF_PEOPLE_PATH . 'includes/class-individual-cpt.php';

	}

	/**
	 * Set up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	public function setup_objects() {

		// Init objects.
		$this->cpt = new SOF_People_Individual_CPT( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

	}

}
