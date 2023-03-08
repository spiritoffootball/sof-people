<?php
/**
 * Individual Custom Post Type Class.
 *
 * Handles providing an "Individual" Custom Post Type.
 *
 * @package SOF_People
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Individual Custom Post Type Class.
 *
 * A class that encapsulates an "Individual" Custom Post Type.
 *
 * @since 1.0.0
 */
class SOF_People_Individual_CPT {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object
	 */
	public $plugin;

	/**
	 * Individual loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object
	 */
	public $individual;

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_name = 'individual';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_rest_base = 'individuals';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_name = 'individual-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_rest_base = 'individual-types';

	/**
	 * Free Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_free_name = 'individual-tag';

	/**
	 * Free Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_free_rest_base = 'individual-tags';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->individual = $parent;
		$this->plugin = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_people/individual/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Activation and deactivation.
		add_action( 'sof_people/activate', [ $this, 'activate' ] );
		add_action( 'sof_people/deactivate', [ $this, 'deactivate' ] );

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Create primary taxonomy.
		add_action( 'init', [ $this, 'taxonomy_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );

		/*
		// Create alternative taxonomy.
		add_action( 'init', [ $this, 'taxonomy_alt_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_alt_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_alt_filter_post_type' ] );
		*/

		// Create free tagging taxonomy.
		add_action( 'init', [ $this, 'taxonomy_free_create' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();
		$this->taxonomy_free_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Create Post Type args.
		$args = [

			// Labels.
			'labels' => [
				'name'               => __( 'Individuals', 'sof-people' ),
				'singular_name'      => __( 'Individual', 'sof-people' ),
				'add_new'            => __( 'Add New', 'sof-people' ),
				'add_new_item'       => __( 'Add New Individual', 'sof-people' ),
				'edit_item'          => __( 'Edit Individual', 'sof-people' ),
				'new_item'           => __( 'New Individual', 'sof-people' ),
				'all_items'          => __( 'All Individuals', 'sof-people' ),
				'view_item'          => __( 'View Individual', 'sof-people' ),
				'search_items'       => __( 'Search Individuals', 'sof-people' ),
				'not_found'          => __( 'No matching Individual found', 'sof-people' ),
				'not_found_in_trash' => __( 'No Individuals found in Trash', 'sof-people' ),
				'menu_name'          => __( 'Individuals', 'sof-people' ),
			],

			// Defaults.
			'menu_icon' => 'dashicons-groups',
			'description' => __( 'Individuals on the Spirit of Football website.', 'sof-people' ),
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'has_archive' => true,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 20,
			'map_meta_cap' => true,

			// Rewrite.
			'rewrite' => [
				'slug' => 'people',
				'with_front' => false,
			],

			// Supports.
			'supports' => [
				'title',
				'editor',
				'excerpt',
				'thumbnail',
			],

			// REST setup.
			'show_in_rest' => true,
			'rest_base' => $this->post_type_rest_base,

		];

		// Set up the post type called "Press".
		register_post_type( $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Override messages for a Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our Custom Post Type.
		$messages[ $this->post_type_name ] = [

			// Unused - messages start at index 1.
			0 => '',

			// Item updated.
			1 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Individual updated. <a href="%s">View Individual</a>', 'sof-people' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2 => __( 'Custom field updated.', 'sof-people' ),
			3 => __( 'Custom field deleted.', 'sof-people' ),
			4 => __( 'Individual updated.', 'sof-people' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5 => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Individual restored to revision from %s', 'sof-people' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Individual published. <a href="%s">View Individual</a>', 'sof-people' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7 => __( 'Individual saved.', 'sof-people' ),

			// Item submitted.
			8 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Individual submitted. <a target="_blank" href="%s">Preview Individual</a>', 'sof-people' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9 => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Individual scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Individual</a>', 'sof-people' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-people' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Individual draft updated. <a target="_blank" href="%s">Preview Individual</a>', 'sof-people' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

		];

		// --<
		return $messages;

	}

	/**
	 * Override the "Add title" label.
	 *
	 * @since 1.0.0
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $title ) {

		// Bail if not our post type.
		if ( $this->post_type_name !== get_post_type() ) {
			return $title;
		}

		// Overwrite with our string.
		$title = __( 'Add the first name and last name of the Individual', 'sof-people' );

		// --<
		return $title;

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Arguments.
		$args = [

			// Same as "category".
			'hierarchical' => true,

			// Labels.
			'labels' => [
				'name'              => _x( 'Individual Types', 'taxonomy general name', 'sof-people' ),
				'singular_name'     => _x( 'Individual Type', 'taxonomy singular name', 'sof-people' ),
				'search_items'      => __( 'Search Individual Types', 'sof-people' ),
				'all_items'         => __( 'All Individual Types', 'sof-people' ),
				'parent_item'       => __( 'Parent Individual Type', 'sof-people' ),
				'parent_item_colon' => __( 'Parent Individual Type:', 'sof-people' ),
				'edit_item'         => __( 'Edit Individual Type', 'sof-people' ),
				'update_item'       => __( 'Update Individual Type', 'sof-people' ),
				'add_new_item'      => __( 'Add New Individual Type', 'sof-people' ),
				'new_item_name'     => __( 'New Individual Type Name', 'sof-people' ),
				'menu_name'         => __( 'Individual Types', 'sof-people' ),
				'not_found'         => __( 'No Individual Types found', 'sof-people' ),
			],

			// Rewrite rules.
			'rewrite' => [
				'slug' => 'people/types',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui' => true,

			// REST setup.
			'show_in_rest' => true,
			'rest_base' => $this->taxonomy_rest_base,

		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Fix the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int $post_id The WordPress post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Add a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow != $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Show a dropdown.
		wp_dropdown_categories( [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'sof-people' ), $taxonomy->label ),
			'taxonomy' => $this->taxonomy_name,
			'name' => $this->taxonomy_name,
			'orderby' => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected' => isset( $_GET[ $this->taxonomy_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_name ] ) : '',
			'show_count' => true,
			'hide_empty' => true,
			'value_field' => 'slug',
			'hierarchical' => 1,
		] );

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our alternative Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_free_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Define Taxonomy arguments.
		$args = [

			// General.
			'public' => true,
			'hierarchical' => false,

			// Labels.
			'labels' => [
				'name'                       => _x( 'Individual Tags', 'taxonomy general name', 'sof-people' ),
				'singular_name'              => _x( 'Individual Tag', 'taxonomy singular name', 'sof-people' ),
				'menu_name'                  => __( 'Individual Tags', 'sof-people' ),
				'search_items'               => __( 'Search Individual Tags', 'sof-people' ),
				'all_items'                  => __( 'All Individual Tags', 'sof-people' ),
				'edit_item'                  => __( 'Edit Individual Tag', 'sof-people' ),
				'update_item'                => __( 'Update Individual Tag', 'sof-people' ),
				'add_new_item'               => __( 'Add New Individual Tag', 'sof-people' ),
				'new_item_name'              => __( 'New Individual Tag Name', 'sof-people' ),
				'not_found'                  => __( 'No Individual Tags found', 'sof-people' ),
				'popular_items'              => __( 'Popular Individual Tags', 'sof-people' ),
				'separate_items_with_commas' => __( 'Separate Individual Tags with commas', 'sof-people' ),
				'add_or_remove_items'        => __( 'Add or remove Individual Tag', 'sof-people' ),
				'choose_from_most_used'      => __( 'Choose from the most popular Individual Tags', 'sof-people' ),
			],

			// Rewrite rules.
			'rewrite' => [
				'slug' => 'people/tags',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui' => true,

			// REST setup.
			'show_in_rest' => true,
			'rest_base' => $this->taxonomy_free_rest_base,

		];

		// Go ahead and register the Taxonomy now.
		register_taxonomy( $this->taxonomy_free_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

}
