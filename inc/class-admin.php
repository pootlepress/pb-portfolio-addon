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
	 * Adds the admin hooks
	 * @since 1.0.0
	 */
	protected function admin_hooks() {
		//Enqueue admin JS and CSS
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this, 'admin_enqueue' ) );
		//Content block panel tab
		add_filter( 'pootlepb_content_block_tabs', array( $this, 'add_tab' ) );
		//Content block panel fields
		add_filter( 'pootlepb_content_block_fields', array( $this, 'content_block_fields' ) );
		//Row style panel tab
		add_filter( 'pootlepb_row_settings_tabs', array( $this, 'add_tab' ) );
		//Row style panel fields
		add_filter( 'pootlepb_row_settings_fields', array( $this, 'row_tab_fields' ) );
		//Row style panel js
		add_action( 'pootlepb_row_settings_portfolio_tab', array( $this, 'portfolio_row_js' ), 70 );
		//Add portfolio dialog
		add_action( 'pootlepb_metabox_end', array( $this, 'add_pofo_dialogs' ) );
		//Adding Button in add to panel pane
		add_filter( 'pootlepb_add_to_panel_buttons', array( $this, 'add_portfolio_button' ) );
		//Add bg color message
		add_action( 'pootlepb_content_block_portfolio_tab', array( $this, 'portfolio_style_message' ), 70 );
		//Custom pofo bg edit field
		add_action( 'pootlepb_row_settings_custom_field_pofo-bg', array( $this, 'portfolio_bg_edit_field_render' ), 7, 2 );
	} // End admin_hooks()

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
