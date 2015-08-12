<?php

/**
 * Pootle Page Builder Addon Boilerplate main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Pootle_PB_Portfolios {

	/**
	 * @var 	Pootle_PB_Portfolios Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var     string Token
	 * @access  public
	 * @since   1.0.0
	 */
	public static $token;

	/**
	 * @var     string Version
	 * @access  public
	 * @since   1.0.0
	 */
	public static $version;

	/**
	 * @var 	string Plugin main __FILE__
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $file;

	/**
	 * @var 	string Plugin directory url
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $url;

	/**
	 * @var 	string Plugin directory path
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $path;

	/**
	 * @var 	Pootle_PB_Portfolios_Admin Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $admin;

	/**
	 * @var 	Pootle_PB_Portfolios_Public Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $public;

	/**
	 * Main Pootle Page Builder Addon Boilerplate Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Pootle_Page_Builder_Addon_Boilerplate instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {
		self::$token =     'pootle-pb-portfolio';
		self::$file    =   $file;
		self::$url     =   plugin_dir_url( $file );
		self::$path    =   plugin_dir_path( $file );
		self::$version =   '1.0.0';

		add_action( 'init', array( $this, 'init' ) );
	} // End __construct()

	public function init() {
		if ( class_exists( 'Pootle_Page_Builder' ) ) {

			//Initiate admin
			$this->_admin();

			//Initiate public
			$this->_public();

			//Mark this add on as active
			add_filter( 'pootlepb_installed_add_ons', array( $this, 'add_on_active' ) );

			/** Including PootlePress_API_Manager class */
			require_once( plugin_dir_path( __FILE__ ) . 'pp-api/class-pp-api-manager.php' );
			/** Instantiating PootlePress_API_Manager */
			new PootlePress_API_Manager( self::$token, 'pootle page builder portfolios', self::$version, __FILE__, self::$token );
		}
	} // End init()

	/**
	 * Adds the admin hooks
	 * @since 1.0.0
	 */
	protected function _admin() {
		//Instantiating admin class
		$this->admin = Pootle_PB_Portfolios_Admin::instance();

		//Content block attributes apply
		add_filter( 'pootlepb_welcome_message', array( $this->admin, 'welcome_message' ), 10, 3 );
		//Enqueue admin JS and CSS
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this->admin, 'admin_enqueue' ) );
		//Content block panel tab
		add_filter( 'pootlepb_content_block_tabs', array( $this->admin, 'add_tab' ) );
		//Content block panel fields
		add_filter( 'pootlepb_content_block_fields', array( $this->admin, 'content_block_fields' ) );
		//Row style panel tab
		add_filter( 'pootlepb_row_settings_tabs', array( $this->admin, 'add_tab' ) );
		//Row style panel fields
		add_filter( 'pootlepb_row_settings_fields', array( $this->admin, 'row_tab_fields' ) );
		//Row style panel js
		add_action( 'pootlepb_row_settings_portfolio_tab', array( $this->admin, 'portfolio_row_js' ), 70 );
		//Add portfolio dialog
		add_action( 'pootlepb_metabox_end', array( $this->admin, 'add_pofo_dialogs' ) );
		//Adding Button in add to panel pane
		add_filter( 'pootlepb_add_to_panel_buttons', array( $this->admin, 'add_portfolio_button' ) );
		//Add bg color message
		add_action( 'pootlepb_content_block_portfolio_tab', array( $this->admin, 'portfolio_style_message' ), 70 );
		//Custom pofo bg edit field
		add_action( 'pootlepb_row_settings_custom_field_pofo-bg', array( $this->admin, 'portfolio_bg_edit_field_render' ), 7, 2 );
	} // End admin_hooks()

	private function _public() {
		//Instantiating admin class
		$this->public = Pootle_PB_Portfolios_Public::instance();

		//Row attributes
		add_filter( 'pootlepb_row_style_attributes', array( $this->public, 'row_attr' ), 10, 2 );
		//Content block attributes apply
		add_filter( 'pootlepb_content_block_attributes', array( $this->public, 'content_block_attr' ), 10, 2 );
		//Enqueue public JS and CSS
		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue' ) );
		//Content Portfolio container
		add_action( 'pootlepb_render_content_block', array( $this->public, 'portfolio_container' ), 25 );
		//Content Portfolio container close
		add_action( 'pootlepb_render_content_block', array( $this->public, 'portfolio_container_close' ), 70 );

	} // End public_hooks()

	/**
	 * Marks this add on as active on
	 * @param array $active Active add ons
	 * @return array Active add ons
	 * @since 1.0.0
	 */
	public function add_on_active( $active ) {

		// To allows ppb add ons page to fetch name, description etc.
		$active[ self::$token ] = self::$file;

		return $active;
	}
}