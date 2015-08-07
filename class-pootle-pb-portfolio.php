<?php

/** Tabs and fields class */
require 'inc/class-admin.php';

/**
 * Allows adding cool portfolio to ppb
 *
 * @property string hover_color
 * @property array bg_images
 * @property int block_now
 * @property string hover_color_opacity
 * @property string row_animation
 * @property bool pofo_enabled
 */
class Pootle_PB_Portfolios extends Pootle_PB_Portfolios_Admin {

	/**
	 * Pootle_PB_Portfolios Instance of main plugin class.
	 *
	 * @var    object Pootle_PB_Portfolios
	 * @access  protected
	 * @since    1.0.0
	 */
	protected static $_instance = null;

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
	protected function __construct() {
		self::$token =     'pootle-pb-portfolio';
		self::$url =       plugin_dir_url( __FILE__ );
		self::$path =      plugin_dir_path( __FILE__ );
		self::$version =   '1.0.0';

		add_action( 'init', array( $this, 'init' ) );
	} // End __construct()

	public function init() {
		if ( class_exists( 'Pootle_Page_Builder' ) ) {
			/** In Pootle_PB_Portfolios_Admin */
			$this->admin_hooks();
			$this->public_hooks();

			// Pootlepress API Manager
			/** Including PootlePress_API_Manager class */
			require_once( plugin_dir_path( __FILE__ ) . 'pp-api/class-pp-api-manager.php' );
			/** Instantiating PootlePress_API_Manager */
			new PootlePress_API_Manager( self::$token, 'pootle page builder portfolios', self::$version, __FILE__, self::$token );
		}
	} // End init()

	private function public_hooks() {
		//Row attributes
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_attr' ), 10, 2 );
		//Enqueue public JS and CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		//Content Portfolio container
		add_action( 'pootlepb_render_content_block', array( $this, 'portfolio_container' ), 25 );
		//Content Portfolio container close
		add_action( 'pootlepb_render_content_block', array( $this, 'portfolio_container_close' ), 70 );
		//Content block attributes apply
		add_filter( 'pootlepb_welcome_message', array( $this, 'welcome_message' ), 10, 3 );
		//Content block attributes apply
		add_filter( 'pootlepb_content_block_attributes', array( $this, 'content_block_attr' ), 10, 2 );

	} // End public_hooks()

	/**
	 * Sets row attributes
	 *
	 * @param array $attr Content block attributes
	 * @param array $set Content block settings
	 * @return array
	 */
	public function row_attr( $attr, $set ) {

		$this->block_now = 0;
		$this->pofo_enabled = false;
		$this->bg_images = false;
		$this->row_animation = false;
		$this->hover_color = false;
		$this->hover_color_opacity = 0.5;

		if ( !empty( $set['portfolio-layout'] ) ) {
			$attr['class'][] = 'ppb-portfolio portfolio-layout-' . $set['portfolio-layout'];
			$this->pofo_enabled = true;
		} else {
			return $attr;
		}

		if ( !empty( $set['portfolio-edit-background'] ) ) {
			$this->bg_images = json_decode( $set['portfolio-edit-background'], true );
		}

		if ( ! empty( $set['portfolio-animation'] ) ) {
			$this->row_animation = $set['portfolio-animation'];
		}

		if ( !empty( $set['portfolio-hover-color'] ) ) {
			$this->hover_color = $set['portfolio-hover-color'];
			if ( ! empty( $set['portfolio-hover-color-opacity'] ) ) {
				$this->hover_color_opacity = 1 - $set['portfolio-hover-color-opacity'];
				$this->hover_color = 'rgba( ' . pootlepb_hex2rgb( $this->hover_color ) . ', ' . ( 1 - $set['portfolio-hover-color-opacity'] ) . ' )';
			}
		}

		return $attr;
	}

	public function welcome_message( $message, $current_user, $visit_count ) {
		global $pagenow;
		if ( 0 < $visit_count && 'post-new.php' == $pagenow && ! get_user_meta( $current_user->ID, 'pootlepb_pofo_welcome' ) ) {
			update_user_meta( $current_user->ID, 'pootlepb_pofo_welcome', $visit_count );
			return "
<script>
	jQuery(function($){
		$('.add-button').removeClass('pootle');
		$('.add-pofo.add-button').addClass('pootle');
	})
</script>
<div id='ppb-hello-user' class='visit-count-1'>Click the 'Add Portfolio' button above to create your portfolio</div>
			";
		}
		return $message;
	}

	/**
	 * Sets content block attributes
	 *
	 * @param array $attr Content block attributes
	 * @param array $set Content block settings
	 * @return array
	 */
	public function content_block_attr( $attr, $set ) {
		if ( !empty( $set['portfolio-bg'] ) ) {
			$attr['style'] .= 'background: url(' . $set['portfolio-bg'] . ') center/cover;';
		}
		if ( !empty( $set['portfolio-item'] ) ) {
			$attr['class'][] = 'ppb-portfolio-block';
		}

		$this->block_now++;

		return $attr;
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
				echo '<a ';
				if ( ! empty( $set['portfolio-link-new-page'] ) ) {
					echo 'target="_blank" ';
				}
				echo 'href="' . $set['portfolio-link-url'] . '">';
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
			if ( ! empty( $set['portfolio-link'] ) ) {
				echo '</a>';
			}
		}
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
}