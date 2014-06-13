<?php
/**
 * Plugin Name: Contact Details by WooThemes
 * Plugin URI: http://www.woothemes.com/
 * Description: Contact Details is the best plugin to get your users in touch with you!
 * Version: 1.0.0
 * Author: WooThemes
 * Author URI: http://www.woothemes.com
 * Requires at least: 3.8.1
 * Tested up to: 3.8.1
 *
 * Text Domain: contact-details-by-woothemes
 * Domain Path: /languages/
 *
 * @package Contact_Details_by_WooThemes
 * @category Core
 * @author Jeffikus
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Contact_Details_by_WooThemes to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Contact_Details_by_WooThemes
 */
function Contact_Details_by_WooThemes() {
	return Contact_Details_by_WooThemes::instance();
} // End Contact_Details_by_WooThemes()

Contact_Details_by_WooThemes();

/**
 * Main Output Template Tag
 * @access public
 * @param  $atts mixed
 * @since  1.0.0
 * @return void
 */
function contact_details( $atts = array() ) {
	$a = shortcode_atts( array(
        'display' => 'all'
    ), $atts );

	$action_output = Contact_Details_by_WooThemes()->contact_details_output( $atts );
	echo $action_output;
} // End contact_details()

/**
 * Main Contact_Details_by_WooThemes Class
 *
 * @class Contact_Details_by_WooThemes
 * @version	1.0.0
 * @since 1.0.0
 * @package	Contact_Details_by_WooThemes
 * @author Jeffikus
 */
final class Contact_Details_by_WooThemes {
	/**
	 * Contact_Details_by_WooThemes The single instance of Contact_Details_by_WooThemes.
	 * @var 	object
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
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'contact-details-by-woothemes';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		// Admin - Start
		require_once( 'classes/class-contact-details-by-woothemes-settings.php' );
			$this->settings = new Contact_Details_by_WooThemes_Settings();

		if ( is_admin() ) {
			require_once( 'classes/class-contact-details-by-woothemes-admin.php' );
			$this->admin = new Contact_Details_by_WooThemes_Admin();
		} // End If Statement

		require_once( 'classes/class-contact-details-by-woothemes-widget.php' );

		/* Register the widget. */
		add_action( 'widgets_init', create_function( '', 'return register_widget( "Contact_Details_by_WooThemes_Widget" );' ), 1 );

		// Admin - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Template Action
		add_action( 'contact_details', array( $this, 'contact_details_output_action' ), 1, 1 );

		// Load JavaScripts and Stylesheets
		add_action( 'wp_enqueue_scripts', array( $this, 'load_javascripts_stylesheets' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_javascripts_stylesheets' ) );
		} // End If Statement

		// Load Shortcode
		add_shortcode( 'contact_details', array( $this, 'contact_details_output' ) );

	} // End __construct()

	/**
	 * Main Contact_Details_by_WooThemes Instance
	 *
	 * Ensures only one instance of Contact_Details_by_WooThemes is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Contact_Details_by_WooThemes()
	 * @return Main Contact_Details_by_WooThemes instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'contact-details-by-woothemes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Load JS and CSS
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_javascripts_stylesheets() {
		$this->setup_data();
		// JS
		wp_register_script( 'contact-details-google-maps', 'http://maps.google.com/maps/api/js?sensor=false', false, false, true );
		wp_register_script( 'contact-details-google-maps-markers', $this->plugin_url . 'assets/js/markers.min.js', false, false, true );
		wp_enqueue_script( 'contact-details-google-maps' );
		wp_enqueue_script( 'contact-details-google-maps-markers' );
		wp_register_script( 'contact-details-google-maps-initialize', $this->plugin_url . 'assets/js/maps.min.js', array( 'jquery' ), false, true );
		$title = str_replace( array( '&#8220;','&#8221;' ), '"', $this->map_marker_title );
		$title = str_replace( '&#8211;', '-', $title );
		$title = str_replace( '&#8217;', "`", $title );
		$title = str_replace( '&#038;', '&', $title );
	  	$title = html_entity_decode( $title );
		$geocoords = explode( ',', $this->geocoords );
		$translation_array = array( 	'geocoords_x' => $geocoords[0],
										'geocoords_y' => $geocoords[1],
										'map_type' => $this->map_type,
										'zoom' => $this->map_zoom,
										'map_mouse_scroll' => $this->map_mouse_scroll,
										'plugin_url' => $this->plugin_url,
										'map_callout' => preg_replace( "/[\n\r]/","<br/>",$this->callout ),
										'map_link' => get_permalink( get_the_id() ),
										'marker_title' => $title,
										'marker_color' => $this->map_marker_color
									);
		wp_localize_script( 'contact-details-google-maps-initialize', 'contact_details', $translation_array );
		// The script can be enqueued now or later.
		wp_enqueue_script( 'contact-details-google-maps-initialize' );
		// CSS
		wp_enqueue_style( 'contact-details-styles', $this->plugin_url . 'assets/css/general.css' );
	} // End load_javascripts_stylesheets()

	/**
	 * Load Admin JS and CSS
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_admin_javascripts_stylesheets() {
		// CSS
		wp_enqueue_style( 'contact-details-admin-styles', $this->plugin_url . 'assets/css/admin.css' );
	} // End load_admin_javascripts_stylesheets()

	/**
	 * Main output function for contact details action
	 * @access public
	 * @param  $atts mixed
	 * @since  1.0.0
	 * @return void
	 */
	public function contact_details_output_action( $atts ) {
		$a = shortcode_atts( array(
	        'display' => 'all'
	    ), $atts );
		$action_output = $this->contact_details_output( $atts );
		echo $action_output;
	} // End contact_details_output_action()

	/**
	 * Main output function for contact details shortcode
	 * @access public
	 * @param  $atts mixed
	 * @since  1.0.0
	 * @return void
	 */
	public function contact_details_output( $atts ) {
		$a = shortcode_atts( array(
	        'display' => 'all'
	    ), $atts );

		ob_start();

	    do_action( 'pre_contact_details_output' );

	    if ( isset( $a['display'] ) && ( $a['display'] == 'all' || $a['display'] == 'details' ) ) {
	    	// Output location
			$this->location_output();
	    } // End If Statement

	    if ( isset( $a['display'] ) && ( $a['display'] == 'all' || $a['display'] == 'social' ) ) {
	    	// Output Social Media
			$this->social_output();
	    } // End If Statement

	    if ( isset( $a['display'] ) && ( $a['display'] == 'all' || $a['display'] == 'map' ) ) {
	    	// Output map
			$this->map_output();
	    } // End If Statement

	    do_action( 'post_contact_details_output' );

	    $output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	} // End contact_details_output()

	/**
	 * Sets object data and settings
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function setup_data() {
		$this->phone_number 		= $this->settings->get_value( 'phone_number', '', 'contact-fields' );
	    $this->fax_number 			= $this->settings->get_value( 'fax_number', '', 'contact-fields' );
	    $this->address 				= $this->settings->get_value( 'address', '', 'contact-fields' );
	    $this->email_address 		= $this->settings->get_value( 'email_address', '', 'contact-fields' );
	    $this->twitter 				= $this->settings->get_value( 'twitter', '', 'contact-fields' );
	    $this->facebook 			= $this->settings->get_value( 'facebook', '', 'contact-fields' );
	    $this->geocoords 			= $this->settings->get_value( 'map_coords', '', 'map-fields' );
	    $this->callout 				= $this->settings->get_value( 'callout', '', 'map-fields' );
	    $this->map_marker_title 	= $this->settings->get_value( 'location_name', '', 'contact-fields' );

	    // Next version -> create options for these
	    $this->map_height 					= 250;
	    $this->map_zoom 					= 9;
	    $this->map_type 					= 'SATELLITE'; // SATELLITE, HYBRID, TERRAIN
	    $this->map_marker_color 			= 'red';
	    $this->map_mouse_scroll 			= 'false';
	} // End setup_data()

	/**
	 * Location details output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function location_output() {
		do_action( 'pre_contact_details_location_output' );
		?>
		<!-- LOCATION -->
		<section id="location-details" itemscope itemtype="http://schema.org/Place">

			<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" class="contact-address">
				<?php if ( isset( $this->map_marker_title ) && $this->map_marker_title != '' ) { ?><h2 itemprop="location"><?php _e( $this->map_marker_title, 'contact-details-by-woothemes' ); ?></h2><?php } ?>
    	    	<?php if ( isset( $this->address ) && $this->address != '' ) { ?><p itemprop="streetAddress"><?php echo nl2br( esc_html( $this->address ) ); ?></p><?php } ?>
    		</div><!-- /.contact-address -->

	    	<div class="contact-numbers">
	    	    <?php if ( isset( $this->phone_number ) && $this->phone_number != '' ) { ?><span itemprop="telephone"><?php _e( 'Tel:','contact-details-by-woothemes' ); ?> <?php echo esc_html( $this->phone_number ); ?></span><br /><?php } ?>
	    	    <?php if ( isset( $this->fax_number ) && $this->fax_number != '' ) { ?><span itemprop="faxNumber"><?php _e( 'Fax:','contact-details-by-woothemes' ); ?> <?php echo esc_html( $this->fax_number ); ?></span><br /><?php } ?>
	    	    <?php if ( isset( $this->email_address ) && $this->email_address != '' ) { ?><?php _e('Email:', 'contact-details-by-woothemes' ); ?> <a href="mailto:<?php echo esc_attr( $this->email_address ); ?>" itemprop="email"><?php echo esc_html( $this->email_address ); ?></a><br /><?php } ?>
	    	</div><!-- /.contact-numbers -->

		</section><!-- /#location-details -->
		<?php
		do_action( 'post_contact_details_location_output' );
	} // End location_output()

	/**
	 * Social details output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function social_output() {
		do_action( 'pre_contact_details_social_output' ); ?>

		<!-- SOCIAL MEDIA -->
		<section id="location-social-media" itemscope itemtype="http://schema.org/Place">
			<ul>
			<?php if ( isset( $this->twitter ) && '' != $this->twitter ) { ?>
				<li>
					<a itemprop="url" href="<?php echo esc_url( $this->twitter ); ?>"><?php _e( 'Twitter', 'contact-details-by-woothemes' ); ?></a>
				</li>
			<?php } ?>

			<?php if ( isset( $this->facebook ) && '' != $this->facebook ) { ?>
				<li>
					<a itemprop="url" href="<?php echo esc_url( $this->facebook ); ?>"><?php _e( 'Facebook', 'contact-details-by-woothemes' ); ?></a>
				</li>
			<?php } ?>
			</ul>
		</section><!-- /#location-social-media -->

		<?php do_action( 'post_contact_details_social_output' );
	} // End social_output()

	/**
	 * Google Map output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function map_output() {
		do_action( 'pre_contact_details_map_output' ); ?>

		<!-- MAP -->
		<section id="location-map" itemscope itemtype="http://schema.org/Place">
    		<?php if ( $this->geocoords != '' ) {
    			$map_height 	= $this->map_height;
				if ( empty( $map_height ) ) {
					$map_height = 250;
				} // End If Statement ?>
				<div itemprop="map" id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div><!-- /#single_map_canvas -->
			<?php } ?>
    	</section><!-- /#location-map -->

    	<?php do_action( 'post_contact_details_map_output' );
	} // End map_output()

} // End Class