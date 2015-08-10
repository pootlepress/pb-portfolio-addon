<?php

/**
 * Pootle Page Builder Addon Boilerplate public class
 * @property string $token Plugin token
 * @property string $url Plugin root dir url
 * @property string $path Plugin root dir path
 * @property string $version Plugin version
 * @property string hover_color
 * @property array bg_images
 * @property int block_now
 * @property string hover_color_opacity
 * @property string row_animation
 * @property bool pofo_enabled
 */
class Pootle_PB_Portfolios_Public{

	/**
	 * @var 	Pootle_PB_Portfolios_Public Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Main Pootle Page Builder Addon Boilerplate Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
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
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   =   Pootle_PB_Portfolios::$token;
		$this->url     =   Pootle_PB_Portfolios::$url;
		$this->path    =   Pootle_PB_Portfolios::$path;
		$this->version =   Pootle_PB_Portfolios::$version;
	} // End __construct()

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
		} else {
			$this->row_animation = 'fadeInDown';
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
			$this->add_link( $attr, $set );

			echo '<div ' . pootlepb_stringify_attributes( $attr ) . '>';
			echo "<div class='portfolio-content' data-portfolio-animate='animated {$this->row_animation}'>";
		}
	}

	private function hover_color( &$attr, $set ) {

		if ( ! empty( $set['portfolio-bg-color'] ) ) {
			$attr['style'] .= 'background:rgba( ' . pootlepb_hex2rgb( $set['portfolio-bg-color'] ) . ', ' . $this->hover_color_opacity . ' );';
		} else if ( ! empty( $this->hover_color ) ) {
			$attr['style'] .= ' background:' . $this->hover_color . ';';
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
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_style( $token . '-animate', $url . '/assets/animate.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	} // End enqueue()
}