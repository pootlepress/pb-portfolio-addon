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
		$token = Pootle_PB_Portfolios::$token;
		$url = Pootle_PB_Portfolios::$url;

		wp_enqueue_script( $token . '-admin-js', $url . '/assets/admin.js', array( 'jquery' ) );
		wp_enqueue_style( $token . '-admin-css', $url . '/assets/admin.css' );
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
			'name' => 'Hover color',
			'type' => 'color',
			'priority' => 4,
			'tab' => 'portfolio',
		);
		$f['portfolio-link'] = array(
			'name' => __( 'Link to', 'vantage' ),
			'tab' => 'portfolio',
			'type' => 'select',
			'priority' => 5,
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
			'priority' => 6,
			'default' => '',
		);
		$f['portfolio-link-new-page'] = array(
			'name' => __( 'Open in a new page', 'vantage' ),
			'tab' => 'portfolio',
			'type' => 'checkbox',
			'priority' => 7,
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
			'default' => '0.5',
			'type' => 'slider',
			'priority' => 4,
			'tab' => 'portfolio',
		);
		$f['portfolio-edit-background'] = array(
			'name' => 'Edit Background',
			'type' => 'pofo-bg',
			'priority' => 3,
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
	 * Adds Portfolio dialogs
	 * @action pootlepb_metabox_end
	 */
	public function add_pofo_dialogs() {
		?>
		<div id="pofo-add-dialog" data-title="<?php esc_attr_e( 'Add Portfolio', 'ppb-panels' ) ?>"
		     class="panels-admin-dialog" style="text-align: center">
			<p>
				<label>
					<strong>
						<?php _e( 'Type in the number of rows and columns', 'ppb-panels' ) ?>
					</strong>
				</label>
			</p>
			<p>
				<input id="pofo-add-dialog-num-cols" type="number" class="small-text" value="4"> x
				<input id="pofo-add-dialog-num-rows" type="number" class="small-text" value="4">
			</p>
		</div>
		<div id="pofo-edit-bgs-dialog" data-title="<?php esc_attr_e( 'Sort Images', 'ppb-panels' ) ?>"
		     class="panels-admin-dialog" style="text-align: center">
			<p>Drag the images below to reorder them for your portfolio</p>
			<div class="images"></div>
		</div>
	<?php
	}

	/**
	 * Adds Add Portfolio dialog
	 * @action pootlepb_add_to_panel_buttons
	 */
	public function add_portfolio_button( $buttons ) {
		$preb_name = $buttons['prebuilt-set'];
		unset( $buttons['prebuilt-set'] );
		$buttons['add-pofo'] = 'Add Portfolio';
		$buttons['prebuilt-set'] = $preb_name;
		return $buttons;
	}

	/**
	 * Add portfolio js to row styling panel
	 * @since 0.1.0
	 */
	public function portfolio_style_message() {
		?>
		You can set a background color for this portfolio item in <a id="ppb-pofo-switch-to-style-tab" href="#">Styles</a>
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
		<button class="button pofo-sort-image">Sort Images</button>
		<?php
	}
}
