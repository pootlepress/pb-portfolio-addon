<?php
/**
 * Created by shramee
 * At: 5:37 PM 6/8/15
 */

/**
 * Class Pootle_PB_Portfolios_Tabs_And_Fields
 * Contains tabs and fields hooks to Portfolio tabs in ppb
 */
class Pootle_PB_Portfolios_Admin {

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
	 * Shows portfolio for pootle pb on-boarding message
	 * @param string $message
	 * @param object $current_user
	 * @param string|int $visit_count
	 * @return string Message to output
	 */
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
	 * Enqueue the css and js to front end
	 */
	public function admin_enqueue() {
		$token = $this->token;

		wp_enqueue_script( $token . '-admin-js', $this->url . '/assets/admin.js', array( 'jquery', 'jquery-ui-sortable' ) );
		wp_enqueue_style( $token . '-admin-css', $this->url . '/assets/admin.css' );
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
				'' => 'Fade In Down',
				'fadeInUp' => 'Fade In Up',
				'flipInX' => 'FlipInX',
				'slideInUp' => 'Slide In Up',
				'slideInDown' => 'Slide In Down',
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
			'default' => '0.5',
			'type' => 'slider',
			'priority' => 4,
			'tab' => 'portfolio',
		);
		$f['portfolio-grid'] = array(
			'name' => 'Display',
			'type' => 'pofo-display',
			'priority' => 5,
			'tab' => 'portfolio',
		);
		$f['portfolio-edit-background'] = array(
			'name' => 'Edit Background',
			'type' => 'pofo-bg',
			'priority' => 6,
			'tab' => 'portfolio',
		);
		$f['portfolio-grid-preview'] = array(
			'name' => '<div class="pofo-grid-preview"></div>',
			'type' => 'html',
			'priority' => 7,
			'tab' => 'portfolio',
		);
		$f['portfolio-grid-options'] = array(
			'name' => '<div class="pofo-grid-options"></div>',
			'type' => 'html',
			'priority' => 8,
			'tab' => 'portfolio',
		);
		return $f;
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
						$('.field-portfolio-link-new-page').show();
					} else {
						$('.field-portfolio-link-url').val('').hide();
						$('.field-portfolio-link-new-page').prop( "checked", false ).hide();
					}
				});
			})
		</script>
	<?php
	}

	/**
	 * Add portfolio js to row styling panel
	 * @since 0.1.0
	 */
	public function portfolio_style_message() {
		?>
		<script>
			jQuery(function($){
				$('#ppb-pofo-switch-to-style-tab').click(function(e){
					e.preventDefault();
					var i = $('.widget-dialog-pootle_pb_content_block .ppb-tabs-anchors').index($('[href$="pootle-style-tab"]'))
					$('.ppb-add-content-panel').ppbTabs( "option", "active", i );
				});
			})
		</script>
	<?php
	}

	public function portfolio_bg_edit_field_render( $key, $field ) {
		?>
		<input type="hidden" name="panelsStyle[<?php echo esc_attr( $key ) ?>]"
		         data-style-field="<?php echo esc_attr( $key ) ?>"
		         data-style-field-type="<?php echo esc_attr( $field['type'] ) ?>" />
		<button class="button pofo-select-image">Select Images</button>
		<?php
	}

	public function pofo_display_field( $key, $field ) {
		$field['type'] = 'number';
		$field['min'] = '0';
		$field['max'] = '10';
		pootlepb_render_content_field( $key . '-across', $field );
		echo ' across by ';
		unset( $field['max'] );
		pootlepb_render_content_field( $key . '-down', $field );
		echo ' down';
	}
}
