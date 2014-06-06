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
		}
		// Admin - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load JavaScripts
		add_action( 'wp_enqueue_scripts', array( $this, 'loadJavaScripts' ) );

		// Load Shortcode
		add_shortcode( 'contact_details', array( $this, 'locationOutput' ) );

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
	public static function instance () {
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
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->_token . '-version', $this->version );
	} // End _log_version_number()


	public function loadJavaScripts() {
		wp_register_script( 'contact-details-google-maps', 'http://maps.google.com/maps/api/js?sensor=false' );
		wp_register_script( 'contact-details-google-maps-markers', $this->plugin_url . 'assets/js/markers.js' );
		wp_enqueue_script( 'contact-details-google-maps' );
		wp_enqueue_script( 'contact-details-google-maps-markers' );
	}


	public function locationOutput( $atts ) {
		global $woo_options;
		$a = shortcode_atts( array(
	        'foo' => 'something',
	        'bar' => 'something else',
	    ), $atts );
	    $test = $a['foo'];
		?>
		<?php if ( isset($woo_options['woo_contactform_map_coords']) && $woo_options['woo_contactform_map_coords'] != '' ) { $geocoords = $woo_options['woo_contactform_map_coords']; }  else { $geocoords = ''; } ?>

		<section id="location-map">

    		<h2><?php _e( 'Location', 'woothemes' ); ?></h2>

    		<!-- LOCATION -->
			<?php if ( isset( $woo_options['woo_contact_panel'] ) && $woo_options['woo_contact_panel'] == 'true' ) { ?>
				<section id="location">
			    	<ul>
			    	    <?php if (isset($woo_options['woo_contact_title'])) { ?><li><strong><?php echo esc_html( $woo_options['woo_contact_title'] ); ?></strong></li><?php } ?>
			    	    <?php if (isset($woo_options['woo_contact_title']) && $woo_options['woo_contact_title'] != '' ) { ?><li><?php echo nl2br( esc_html( $woo_options['woo_contact_address'] ) ); ?></li><?php } ?>
			    	    <?php if (isset($woo_options['woo_contact_number']) && $woo_options['woo_contact_number'] != '' ) { ?><li><?php _e('Tel:','woothemes'); ?> <?php echo esc_html( $woo_options['woo_contact_number'] ); ?></li><?php } ?>
			    	    <?php if (isset($woo_options['woo_contact_fax']) && $woo_options['woo_contact_fax'] != '' ) { ?><li><?php _e('Fax:','woothemes'); ?> <?php echo esc_html( $woo_options['woo_contact_fax'] ); ?></li><?php } ?>
			    	    <?php if (isset($woo_options['woo_contactform_email']) && $woo_options['woo_contactform_email'] != '' ) { ?><li><?php _e('Email:','woothemes'); ?> <a href="mailto:<?php echo esc_attr( $woo_options['woo_contactform_email'] ); ?>"><?php echo esc_html( $woo_options['woo_contactform_email'] ); ?></a></li><?php } ?>
			    	</ul>
				</section><!-- /.location -->
			<?php } ?>

			<!-- MAP -->

			<?php if ($geocoords != '') { ?>
				<section id="map" <?php if ( isset( $woo_options['woo_contact_panel'] ) && $woo_options['woo_contact_panel'] == 'true' ) { ?>class="float"<?php } ?>>
			    	<?php $this->woo_maps_contact_output("geocoords=$geocoords"); ?>
				</section>
			<?php } ?>

    	</section><!-- /#location-map -->
    	<?php
	}

	/*-----------------------------------------------------------------------------------*/
	/* Google Maps */
	/*-----------------------------------------------------------------------------------*/

	public function woo_maps_contact_output($args){

		$key = get_option('woo_maps_apikey');

		// No More API Key needed

		if ( !is_array($args) )
			parse_str( $args, $args );

		extract($args);
		$mode = '';
		$streetview = 'off';
		$map_height = get_option('woo_maps_single_height');
		$featured_w = get_option('woo_home_featured_w');
		$featured_h = get_option('woo_home_featured_h');
		$zoom = get_option('woo_maps_default_mapzoom');
		$type = get_option('woo_maps_default_maptype');
		$marker_title = get_option('woo_contact_title');
		if ( $zoom == '' ) { $zoom = 6; }
		$lang = get_option('woo_maps_directions_locale');
		$locale = '';
		if(!empty($lang)){
			$locale = ',locale :"'.$lang.'"';
		}
		$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';

		if(empty($map_height)) { $map_height = 250;}

		if(is_home() && !empty($featured_h) && !empty($featured_w)){
		?>
	    <div id="single_map_canvas" style="width:<?php echo $featured_w; ?>px; height: <?php echo $featured_h; ?>px"></div>
	    <?php } else { ?>
	    <div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>
	    <?php } ?>
	    <script type="text/javascript">
			jQuery(document).ready(function(){
				function initialize() {


				<?php if($streetview == 'on'){ ?>


				<?php } else { ?>

				  	<?php switch ($type) {
				  			case 'G_NORMAL_MAP':
				  				$type = 'ROADMAP';
				  				break;
				  			case 'G_SATELLITE_MAP':
				  				$type = 'SATELLITE';
				  				break;
				  			case 'G_HYBRID_MAP':
				  				$type = 'HYBRID';
				  				break;
				  			case 'G_PHYSICAL_MAP':
				  				$type = 'TERRAIN';
				  				break;
				  			default:
				  				$type = 'ROADMAP';
				  				break;
				  	} ?>

				  	var myLatlng = new google.maps.LatLng(<?php echo $geocoords; ?>);
					var myOptions = {
					  zoom: <?php echo $zoom; ?>,
					  center: myLatlng,
					  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
					};
					<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
				  	myOptions.scrollwheel = false;
				  	<?php } ?>
				  	var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);

					<?php if($mode == 'directions'){ ?>
				  	directionsPanel = document.getElementById("featured-route");
	 				directions = new GDirections(map, directionsPanel);
	  				directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
				  	<?php
				 	} else { ?>

				  		var point = new google.maps.LatLng(<?php echo $geocoords; ?>);
		  				var root = "<?php echo esc_url( $this->plugin_url ); ?>";
		  				var callout = '<?php echo preg_replace("/[\n\r]/","<br/>",get_option('woo_maps_callout_text')); ?>';
		  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
		  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"', $marker_title); ?>
		  				<?php $title = str_replace('&#8211;','-',$title); ?>
		  				<?php $title = str_replace('&#8217;',"`",$title); ?>
		  				<?php $title = str_replace('&#038;','&',$title); ?>
		  				var the_title = '<?php echo html_entity_decode($title) ?>';

		  			<?php
				 	if(is_page()){
				 		$custom = get_option('woo_cat_custom_marker_pages');
						if(!empty($custom)){
							$color = $custom;
						}
						else {
							$color = get_option('woo_cat_colors_pages');
							if (empty($color)) {
								$color = 'red';
							}
						}
				 	?>
				 		var color = '<?php echo $color; ?>';
				 		createMarker(map,point,root,the_link,the_title,color,callout);
				 	<?php } else { ?>
				 		var color = '<?php echo get_option('woo_cat_colors_pages'); ?>';
		  				createMarker(map,point,root,the_link,the_title,color,callout);
					<?php
					}
						if(isset($_POST['woo_maps_directions_search'])){ ?>

						directionsPanel = document.getElementById("featured-route");
	 					directions = new GDirections(map, directionsPanel);
	  					directions.load("from: <?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);



						directionsDisplay = new google.maps.DirectionsRenderer();
						directionsDisplay.setMap(map);
	    				directionsDisplay.setPanel(document.getElementById("featured-route"));

						<?php if($walking == 'on'){ ?>
						var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
						<?php } else { ?>
						var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
						<?php } ?>
						var start = '<?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?>';
						var end = '<?php echo $address; ?>';
						var request = {
	       					origin:start,
	        				destination:end,
	        				travelMode: travelmodesetting
	    				};
	    				directionsService.route(request, function(response, status) {
	      					if (status == google.maps.DirectionsStatus.OK) {
	        					directionsDisplay.setDirections(response);
	      					}
	      				});

	  					<?php } ?>
					<?php } ?>
				<?php } ?>


				  }
				  function handleNoFlash(errorCode) {
					  if (errorCode == FLASH_UNAVAILABLE) {
						alert("Error: Flash doesn't appear to be supported by your browser");
						return;
					  }
					 }



			initialize();

			});
		jQuery(window).load(function(){

			var newHeight = jQuery('#featured-content').height();
			newHeight = newHeight - 5;
			if(newHeight > 300){
				jQuery('#single_map_canvas').height(newHeight);
			}

		});

		</script>

	<?php
	}



} // End Class
?>
