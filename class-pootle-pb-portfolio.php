<?php

/**
 * @property string hover_color
 * @property string hover_color_opacity
 * @property string row_animation
 */
class Pootle_PB_Portfolios{

	/**
	 * Pootle_PB_Portfolios Instance of main plugin class.
	 *
	 * @var 	object Pootle_PB_Portfolios
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
	 * pootle page builder portfolios plugin directory URL.
	 *
	 * @var 	string Plugin directory
	 * @access  private
	 * @since 	1.0.0
	 */
	public static $url;

	/**
	 * pootle page builder portfolios plugin directory Path.
	 *
	 * @var 	string Plugin directory
	 * @access  private
	 * @since 	1.0.0
	 */
	public static $path;

	/**
	 * Main pootle page builder portfolios Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Pootle_PB_Portfolios instance
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

		self::$token =     'pootle-pb-portfolio';
		self::$url =       plugin_dir_url( __FILE__ );
		self::$path =      plugin_dir_path( __FILE__ );
		self::$version =   '1.0.0';

		add_action( 'init', array( $this, 'init' ) );
	} // End __construct()

	public function init() {

		if ( class_exists( 'Pootle_Page_Builder' ) ) {

			$this->add_actions();
			$this->add_filters();

			// Pootlepress API Manager
			/** Including PootlePress_API_Manager class */
			require_once( plugin_dir_path( __FILE__ ) . 'pp-api/class-pp-api-manager.php' );
			/** Instantiating PootlePress_API_Manager */
			new PootlePress_API_Manager( self::$token, 'pootle page builder portfolios', self::$version, __FILE__, self::$token );
		}
	} // End init()

	private function add_actions() {
		//Adding front end JS and CSS in /assets folder
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this, 'admin_enqueue' ) );
		//Content Portfolio container
		add_action( 'pootlepb_render_content_block', array( $this, 'portfolio_container' ), 25 );
		add_action( 'pootlepb_render_content_block', array( $this, 'portfolio_container_close' ), 70 );
		add_action( 'pootlepb_row_settings_portfolio_tab', array( $this, 'portfolio_row_js' ), 70 );
	} // End add_actions()

	private function add_filters() {

		//Content block attributes apply
		add_filter( 'pootlepb_content_block_attributes', array( $this, 'content_block' ), 10, 2 );
		//Row attributes
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_attr' ), 10, 2 );
		//Content block panel tab
		add_filter( 'pootlepb_content_block_tabs', array( $this, 'add_tab' ) );
		add_filter( 'pootlepb_content_block_fields', array( $this, 'content_block_fields' ) );
		//Row style panel tab
		add_filter( 'pootlepb_row_settings_tabs', array( $this, 'add_tab' ) );
		add_filter( 'pootlepb_row_settings_fields', array( $this, 'row_tab_fields' ) );

	} // End add_filters()

	/**
	 * Sets content block attributes
	 *
	 * @param array $attr Content block attributes
	 * @param array $set Content block settings
	 * @return array
	 */
	public function content_block( $attr, $set ) {
		if ( !empty( $set['portfolio-bg'] ) ) {
			$attr['style'] .= 'background: url(' . $set['portfolio-bg'] . ') center/cover;';
		}
		if ( !empty( $set['portfolio-item'] ) ) {
			$attr['class'][] = 'ppb-portfolio-block';
		}
		return $attr;
	}

	/**
	 * Add portfolio js to row styling panel
	 * @since 0.1.0
	 */
	public function portfolio_row_js() {
		?>
		<script>
			jQuery(function($){
				$('[dialog-field="portfolio-link"]').change(function(){
					if ( 'link' == $(this).val() ) {
						$('.field-portfolio-link-url').show();
					} else {
						$('.field-portfolio-link-url').val('').hide();
					}
				});
			})
		</script>
	<?php
	}


	/**
	 * Render the Content Panel.
	 *
	 * @param string $widget_info The widget class name.
	 *
	 * @since 0.1.0
	 */
	public function portfolio_container( $info ) {
		$set = json_decode( $info['info']['style'], true );

		if ( !empty( $set['portfolio-item'] ) ) {

			$attr = array();
			$attr['class'] = 'ppb-portfolio-item';
			$attr['style'] = '';

			$this->hover_color( $attr, $set );
			$this->hover_animation( $attr, $set );
			$this->add_link( $attr, $set );

			echo '<div ' . pootlepb_stringify_attributes( $attr ) . '>';
		}
	}

	private function hover_color( &$attr, $set ) {

		if ( ! empty( $set['portfolio-bg-color'] ) ) {
			$attr['style'] .= 'background:rgba( ' . pootlepb_hex2rgb( $set['portfolio-bg-color'] ) . ', ' . $this->hover_color_opacity . ' );';
		} else if ( ! empty( $this->hover_color ) ) {
			$attr['style'] .= ' background:' . $this->hover_color . ';';
		}
	}

	private function hover_animation( &$attr, $set ) {

		if ( ! empty( $this->row_animation ) ) {
			$attr['data-portfolio-animate'] = 'animated ' . $this->row_animation;
		}
	}

	private function add_link( &$attr, $set ) {

		if ( ! empty( $set['portfolio-link'] ) ) {
			$attr['style'] .= 'cursor:pointer;';
			if ( 'link' == $set['portfolio-link'] ) {
				echo '<a href="' . $set['portfolio-link-url'] . '">';
			} else {
				add_thickbox();
				echo '<a class="thickbox" href="' . $set['portfolio-bg'] . '?keepThis=true&TB_iframe=true&height=250&width=400">';
			}
		}
	}

	/**
	 * Render the Content Panel.
	 *
	 * @param string $widget_info The widget class name.
	 *
	 * @since 0.1.0
	 */
	public function portfolio_container_close( $info ) {
		$set = json_decode( $info['info']['style'], true );

		if ( ! empty( $set['portfolio-item'] ) ) {
			echo '</div>';
		}
	}

	/**
	 * Sets row attributes
	 *
	 * @param array $attr Content block attributes
	 * @param array $set Content block settings
	 * @return array
	 */
	public function row_attr( $attr, $set ) {

		if ( !empty( $set['portfolio-layout'] ) ) {
			$attr['class'][] = 'portfolio-layout-' . $set['portfolio-layout'];
		}

		$this->row_animation = false;
		if ( !empty( $set['portfolio-animation'] ) ) {
			$this->row_animation = $set['portfolio-animation'];
		}

		$this->hover_color = false;
		$this->hover_color_opacity = 1;
		if ( !empty( $set['portfolio-hover-color'] ) ) {
			$this->hover_color = $set['portfolio-hover-color'];
			if ( ! empty( $set['portfolio-hover-color-opacity'] ) ) {
				$this->hover_color_opacity = 1 - $set['portfolio-hover-color-opacity'];
				$this->hover_color = 'rgba( ' . pootlepb_hex2rgb( $this->hover_color ) . ', ' . ( 1 - $set['portfolio-hover-color-opacity'] ) . ' )';
			}
		}

		return $attr;
	}

	/**
	 * Adds portfolio tab to row settings and content block panel
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 */
	public function add_tab( $tabs ) {
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
		$f['portfolio-item'] = array(
			'name' => 'Make this a porfolio item',
			'type' => 'checkbox',
			'priority' => 1,
			'tab' => 'portfolio',
		);
		$f['portfolio-bg'] = array(
			'name' => 'Background image',
			'type' => 'upload',
			'priority' => 2,
			'tab' => 'portfolio',
		);
		$f['portfolio-bg-color'] = array(
			'name' => 'Background color',
			'type' => 'color',
			'priority' => 3,
			'tab' => 'portfolio',
		);
		$f['portfolio-link'] = array(
			'name' => __( 'Link to', 'vantage' ),
			'tab' => 'portfolio',
			'type' => 'select',
			'priority' => 4,
			'options' => array(
				'' => 'None',
				'link' => 'Webpage',
				'libox' => 'Lightbox',
			),
			'default' => '',
		);
		$f['portfolio-link-url'] = array(
			'name' => __( 'Webpage URL', 'vantage' ),
			'tab' => 'portfolio',
			'type' => 'text',
			'priority' => 5,
			'default' => '',
		);
		return $f;
	}

	/**
	 * Adds portfolio row fields
	 * @param array $f Fields
	 * @return array Tabs
	 */
	public function row_tab_fields( $f ) {
		$f['portfolio-layout'] = array(
			'name' => __( 'Portfolio layout', 'vantage' ),
			'tab' => 'portfolio',
			'type' => 'select',
			'priority' => 1,
			'options' => array(
				'' => 'Please choose...',
				'square' => 'Square',
				'masonry' => 'Masonry',
				'circle' => 'Circle',
			),
			'default' => '',
		);
		$f['portfolio-animation'] = array(
			'name' => __( 'Animation', 'vantage' ),
			'tab' => 'portfolio',
			'type' => 'select',
			'priority' => 2,
			'options' => array(
				'' => 'Please choose...',
				'pulse' => 'Pulse',
				'flipInX' => 'FlipInX',
				'tada' => 'Tada',
				'bounceOut' => 'Bounce Out',
				'bounceOutLeft' => 'Bounce Out Left',
				'bounceOutDown' => 'Bounce Out Down',
				'fadeInDownBig' => 'Fade In Down Big',
				'fadeInLeft' => 'Fade In Left',
				'flip' => 'Flip',
				'slideOutUp' => 'Slide Out Up',
				'rotate' => 'Rotate',
			),
			'default' => '',
		);
		$f['portfolio-hover-color'] = array(
			'name' => 'Hover color',
			'type' => 'color',
			'priority' => 3,
			'tab' => 'portfolio',
		);
		$f['portfolio-hover-color-opacity'] = array(
			'name' => 'Hover color Transparency',
			'type' => 'slider',
			'priority' => 4,
			'tab' => 'portfolio',
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
		wp_enqueue_style( $token . '-animate', $url . '/assets/animate.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	} // End enqueue()

	/**
	 * Enqueue the css and js to front end
	 */
	public function admin_enqueue() {
		$token = self::$token;
		$url = self::$url;

		wp_enqueue_script( $token . '-admin-js', $url . '/assets/admin.js', array( 'jquery' ) );
	}
}