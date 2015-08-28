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

		$this->hover_color = false;
		$this->hover_color_opacity = 0.5;

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
		if ( $this->pofo_used( $set ) ) {

			$this->animation = 'fadeInDown';

			if ( empty( $set['portfolio-layout'] ) ) {
				$set['portfolio-layout'] = 'square';
			}

			//Layout Class
			$attr['class'][] = 'ppb-portfolio portfolio-layout-' . $set['portfolio-layout'];

			if ( ! empty( $set['portfolio-animation'] ) ) {
				$this->animation = $set['portfolio-animation'];
			}
		}

		return $attr;
	}

	/**
	 * Render the Portfolio
	 * @param array $info content block info
	 * @action pootlepb_render_content_block
	 * @since 0.1.0
	 */
	public function portfolio( $info ) {
		$set = json_decode( $info['info']['style'], true );

		if ( $this->pofo_used( $set ) ) {
			$across = $set['portfolio-grid-across'];
			$down   = $set['portfolio-grid-down'];
			$this->hover_color_opacity = 0.5;
			$this->hover_color = 'rgba(200, 200, 200, 0.5)';

			if ( !empty( $set['portfolio-hover-color'] ) ) {
				$this->hover_color = $set['portfolio-hover-color'];
				if ( ! empty( $set['portfolio-hover-color-opacity'] ) ) {
					$this->hover_color_opacity = 1 - $set['portfolio-hover-color-opacity'];
					$this->hover_color = 'rgba( ' . pootlepb_hex2rgb( $this->hover_color ) . ', ' . ( 1 - $set['portfolio-hover-color-opacity'] ) . ' )';
				}
			}

			$i = 0;
			for ( $ro = 0; $ro < $down; $ro ++ ) {
				?>
				<div class="pofo-row">
					<?php
					for ( $c = 0; $c < $across; $c ++ ) {
						$attr = array();
						$attr['class'] = 'pofo-contents ppb-portfolio-item';
						$attr['style'] = '';
						$this->hover_color( $attr, $set, $i );
						$this->hover_animation( $attr, $set );
						$this->add_link( $set, $i );

						?>
						<div
							class="pofo-item ppb-portfolio-block"
							style="<?php
							?>width: <?php echo ( 101 - $across ) / $across ?>%;<?php
							?>padding-top: <?php echo ( 101 - $across ) / $across ?>%;<?php
							?>background-color: <?php echo $set[ 'portfolio-item-' . $i . '-color' ] ?>;<?php
							?>background-image: url(<?php echo $set[ 'portfolio-item-' . $i . '-image' ] ?>);<?php
							?>">
							<?php

							echo '<div ' . pootlepb_stringify_attributes( $attr ) . '>';
							?>
								<div class="hv-center">
									<?php echo $set[ 'portfolio-item-' . $i . '-content' ] ?>
								</div>
							</div>
						</div>
					<?php
						if ( ! empty( $set[ 'portfolio-item-' . $i . '-link' ] ) ) {
							echo '</a>';
						}
						$i++;
					}
					?>
				</div>
			<?php
			}
		}

		if ( !empty( $set['portfolio-item'] ) ) {

		}
	}

	private function pofo_used( $set ) {
		if ( ! empty( $set['portfolio-grid-across'] ) && ! empty( $set['portfolio-grid-down'] ) ) {
			return true;
		}
		return false;
	}

	private function hover_color( &$attr, $set ) {

		if ( ! empty( $set['portfolio-bg-color'] ) ) {
			$attr['style'] .= 'background:rgba( ' . pootlepb_hex2rgb( $set['portfolio-bg-color'] ) . ', ' . $this->hover_color_opacity . ' );';
		} else if ( ! empty( $this->hover_color ) ) {
			$attr['style'] .= ' background:' . $this->hover_color . ';';
		}
	}

	private function hover_animation( &$attr, $set ) {

		if ( ! empty( $this->animation ) ) {
			$attr['data-portfolio-animate'] = 'animated ' . $this->animation;
		}
	}

	private function add_link( $set, $i ) {

		if ( ! empty( $set[ 'portfolio-item-' . $i . '-link' ] ) ) {
			if ( 'link' == $set['portfolio-item-' . $i . '-link'] ) {
				echo '<a ';
				if ( ! empty( $set['portfolio-item-' . $i . '-new-page'] ) ) {
					echo 'target="_blank" ';
				}
				echo 'href="' . $set['portfolio-item-' . $i . '-url'] . '">';
			} else {
				add_thickbox();
				echo '<a class="thickbox" href="' . $set['portfolio-item-' . $i . '-image'] . '?keepThis=true&TB_iframe=true&height=250&width=400">';
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
		$token = $this->token;
		$url = $this->url;

		wp_enqueue_style( $token . '-css', $url . '/assets/front-end.css' );
		wp_enqueue_style( $token . '-animate', $url . '/assets/animate.css' );
		wp_enqueue_script( $token . '-js', $url . '/assets/front-end.js', array( 'jquery' ) );
	} // End enqueue()
}