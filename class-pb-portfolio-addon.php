<?php

class PB_Portfolio_Add_on{

	/**
	 * PB_Portfolio_Add_on Instance of main plugin class.
	 *
	 * @var 	object PB_Portfolio_Add_on
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public static $token;
	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public static $version;

	/**
	 * PB - Portfolio Add-on plugin directory URL.
	 *
	 * @var 	string Plugin directory
	 * @access  private
	 * @since 	1.0.0
	 */
	public static $url;

	/**
	 * PB - Portfolio Add-on plugin directory Path.
	 *
	 * @var 	string Plugin directory
	 * @access  private
	 * @since 	1.0.0
	 */
	public static $path;

	/**
	 * Main PB - Portfolio Add-on Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return PB_Portfolio_Add_on instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 *
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {

		self::$token =     'pb-portfolio-addon';
		self::$url =       plugin_dir_url( __FILE__ );
		self::$path =      plugin_dir_path( __FILE__ );
		self::$version =   '1.0.0';

		add_action( 'init', array( $this, 'init' ) );
	} // End __construct()

	public function init() {

		if ( class_exists( 'Pootle_Page_Builder' ) ) {

			$this->add_actions();
			$this->add_filters();
		}
	} // End init()

	private function add_actions() {
		//Adding front end JS and CSS in /assets folder
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	} // End add_actions()

	private function add_filters() {

		//Content block attributes apply
		add_filter( 'pootlepb_content_block_attributes', array( $this, 'content_block' ), 10, 2 );
		//Content block panel tab
		add_filter( 'pootlepb_content_block_tabs', array( $this, 'add_block_tab' ) );
		add_filter( 'pootlepb_content_block_fields', array( $this, 'content_block_fields' ) );
		//Row style panel tab
		add_filter( 'pootlepb_row_settings_tabs', array( $this, 'add_row_tab' ) );
		add_filter( 'pootlepb_row_settings_fields', array( $this, 'row_tab_fields' ) );

	} // End add_filters()

	/**
	 * Sets content block attributes
	 * @param array $attr Content block attributes
	 * @param array $set Content block settings
	 */
	public function content_block( $attr, $set ) {
		if ( !empty( $set['portfolio-bg'] ) ) {
			$attr['style'] .= 'background: url(' . $set['portfolio-bg'] . ') center/cover;';
		}
		return $attr;
	}

	/**
	 * Adds portfolio tab to content block panel
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 */
	public function add_block_tab( $tabs ) {
		$tabs['portfolio'] = array(
			'label' => 'Portfolio',
			'priority' => 7,
		);
		return $tabs;
	}

	/**
	 * Adds portfolio content block fields
	 * @param array $f Fields
	 * @return array Tabs
	 */
	public function content_block_fields( $f ) {
		$f['make-portfolio-item'] = array(
			'name' => 'Make this a porfolio item',
			'type' => 'checkbox',
			'priority' => 1,
			'tab' => 'Portfolio',
		);
		$f['portfolio-bg'] = array(
			'name' => 'Background image',
			'type' => 'upload',
			'priority' => 2,
			'tab' => 'Portfolio',
		);
		return $f;
	}

	/**
	 * Adds portfolio tab to row settings panel
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 */
	public function add_row_tab( $tabs ) {
		$tabs['portfolio'] = array(
			'label' => 'Portfolio',
			'priority' => 7,
		);
		return $tabs;
	}

	/**
	 * Adds portfolio row fields
	 * @param array $f Fields
	 * @return array Tabs
	 */
	public function row_tab_fields( $f ) {
		$f['portfolio-layout'] = array(
			'name' => __( 'Portfolio layout', 'vantage' ),
			'tab' => 'Portfolio',
			'type' => 'select',
			'priority' => 1,
			'options' => array(
				'' => 'Please choose...',
				'square' => 'Square',
				'masonry' => 'Masonry',
			),
			'default' => '',
		);
		return $f;
	}

	/**
	 * Enqueue the css and js to front end
	 */
	public function enqueue() {
		$token = self::$token;
		$url = self::$url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	} // End enqueue()

}