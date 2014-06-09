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

		require_once( 'classes/class-contact-details-by-woothemes-widget.php' );

		/* Register the widget. */
		add_action( 'widgets_init', create_function( '', 'return register_widget( "Contact_Details_by_WooThemes_Widget" );' ), 1 );

		// Admin - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Template Action
		add_action( 'contact_details', array( $this, 'contact_details_output' ), 1, 1 );
		add_action( 'contact_form_title', array( $this, 'contact_form_title' ) );

		// Load JavaScripts and Stylesheets
		add_action( 'wp_enqueue_scripts', array( $this, 'load_javascripts_stylesheets' ) );

		// Load Ajax
		add_action( 'wp_ajax_contact_form_callback', array( $this, 'form_callback' ) );
 		add_action(' wp_ajax_nopriv_contact_form_callback', array( $this, 'form_callback' ) );

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
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Load JS and CSS
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_javascripts_stylesheets() {
		$suffix = '.min';
		// JS
		wp_register_script( 'contact-details-google-maps', 'http://maps.google.com/maps/api/js?sensor=false' );
		wp_register_script( 'contact-details-google-maps-markers', $this->plugin_url . 'assets/js/markers ' . $suffix . ' .js' );
		wp_enqueue_script( 'contact-details-google-maps' );
		wp_enqueue_script( 'contact-details-google-maps-markers' );
		// CSS
		wp_enqueue_style( 'contact-details-styles', $this->plugin_url . 'assets/css/general.css' );
	} // End load_javascripts_stylesheets()

	/**
	 * Main output function for contact details
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function contact_details_output( $atts ) {
		$a = shortcode_atts( array(
	        'display' => 'all'
	    ), $atts );
	    // Setup data
	    $this->setup_data();
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
	    if ( isset( $a['display'] ) && ( $a['display'] == 'all' || $a['display'] == 'form' ) ) {
	    	// Output map
			$this->form_output();
	    } // End If Statement
	} // End contact_details_output()

	/**
	 * Sets object data and settings
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function setup_data() {
		$this->phone_number = $this->settings->get_value( 'phone_number', '' );
	    $this->fax_number = $this->settings->get_value( 'fax_number', '' );
	    $this->address = $this->settings->get_value( 'address', '' );
	    $this->email_address = $this->settings->get_value( 'email_address', '' );
	    $this->twitter = $this->settings->get_value( 'twitter', '' );
	    $this->facebook = $this->settings->get_value( 'facebook', '' );
	    $this->geocoords = $this->settings->get_value( 'map_coords', '' );
	    $this->callout = $this->settings->get_value( 'callout', '' );
	    $this->map_marker_title = $this->settings->get_value( 'location_name', '' );
	    // Next version -> create options for these
	    $this->map_height = 250;
	    $this->map_zoom = 9;
	    $this->map_type = 'ROADMAP'; // SATELLITE, HYBRID, TERRAIN
	    $this->map_marker_color = 'red';
	    $this->disable_map_mouse_scroll = false;
	} // End setup_data()

	/**
	 * Location details output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function location_output() {
		?>
		<!-- LOCATION -->
		<section id="location-details">
			<?php if (isset($this->map_marker_title) && $this->map_marker_title != '' ) { ?><h2><?php _e( $this->map_marker_title, 'woothemes' ); ?></h2><?php } ?>
    	    <?php if (isset($this->address) && $this->address != '' ) { ?><p><?php echo nl2br( esc_html( $this->address ) ); ?></p><?php } ?>
	    	<p>
	    	    <?php if (isset($this->phone_number) && $this->phone_number != '' ) { ?><?php _e('Tel:','woothemes'); ?> <?php echo esc_html( $this->phone_number ); ?><br /><?php } ?>
	    	    <?php if (isset($this->fax_number) && $this->fax_number != '' ) { ?><?php _e('Fax:','woothemes'); ?> <?php echo esc_html( $this->fax_number ); ?><br /><?php } ?>
	    	    <?php if (isset($this->email_address) && $this->email_address != '' ) { ?><?php _e('Email:','woothemes'); ?> <a href="mailto:<?php echo esc_attr( $this->email_address ); ?>"><?php echo esc_html( $this->email_address ); ?></a><br /><?php } ?>
	    	</p>
		</section><!-- /.location -->
		<?php
	} // End location_output()

	/**
	 * Social details output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function social_output() {
		?>
		<!-- SOCIAL MEDIA -->
		<section id="location-social-media">
			<a href="<?php echo esc_url( $this->twitter ); ?>"><?php _e( 'Twitter', 'woothemes' ); ?></a>
			<a href="<?php echo esc_url( $this->facebook ); ?>"><?php _e( 'Facebook', 'woothemes' ); ?></a>
		</section>
		<?php
	} // End social_output()

	/**
	 * Google Map output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function map_output() {
		?>
		<!-- MAP -->
		<section id="location-map">
    		<?php if ($this->geocoords != '') { ?>
				<section id="map">
			    	<?php $this->maps_contact_output("geocoords=$this->geocoords"); ?>
				</section>
			<?php } ?>
    	</section><!-- /#location-map -->
    	<?php
	} // End map_output()

	/**
	 * Contact form output
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function form_output() {
		if ( isset($_POST['submitted']) && !isset($_POST['form'] ) ) {
			if ( !wp_verify_nonce( $_POST['contact_form_nonce'], 'contact_form' ) ) {
				die( 'Failed security check' );
			} // End If Statement
			$non_js_data = $this->form_callback( $_POST );
			$emailSent = false;
			$nameError = '';
			$emailError = '';
			$commentError = '';
			$mathCheck = '';
			extract( $non_js_data );
		} // End If Statement

		if( isset( $emailSent ) && true == $emailSent ) { ?>

            <p class="info"><strong><?php _e( 'Thanks!', 'woothemes' ); ?></strong> <?php _e( 'Your email was successfully sent.', 'woothemes' ); ?></p>

        <?php } else { ?>

		<!-- CONTACT FORM -->
		<section id="location-contact">

			<?php if( isset( $hasError ) || isset( $captchaError ) ) { ?>
   		 	    <p class="alert"><?php _e( 'There was an error submitting the form.', 'woothemes' ); ?></p>
   		 	<?php } ?>
   		 	<?php if ( get_option( 'woo_contactform_email' ) == '' ) { ?>
   		 	    <?php echo do_shortcode( '[box type="alert"]' . __( 'E-mail has not been setup properly. Please add your contact e-mail!', 'woothemes' ) . '[/box]' );  ?>
   		 	<?php } ?>

			<form action="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" id="location-contact-form" method="post">

				<?php do_action( 'contact_form_title' ); ?>

   		 	   	<p>
   		 	        <label for="contactName"><?php _e( 'Name', 'woothemes' ); ?></label>
	 	            <input type="text" name="contactName" id="contactName" value="<?php if( isset( $_POST['contactName'] ) ) { echo esc_attr( $_POST['contactName'] ); } ?>" class="txt requiredField" />
	 	            <?php if($nameError != '') { ?>
	 	                <span class="error"><?php echo $nameError;?></span>
	 	            <?php } ?>
	 	        </p>
   		 	    <p>
   		 	        <label for="email"><?php _e( 'Email', 'woothemes' ); ?></label>
	 	            <input type="text" name="email" id="email" value="<?php if( isset( $_POST['email'] ) ) { echo esc_attr( $_POST['email'] ); } ?>" class="txt requiredField email" />
	 	            <?php if($emailError != '') { ?>
	 	                <span class="error"><?php echo $emailError;?></span>
	 	            <?php } ?>
	 	        </p>
   		 	    <p>
   		 	        <label for="commentsText"><?php _e( 'Message', 'woothemes' ); ?></label>
	 	            <textarea name="comments" id="commentsText" rows="5" cols="30" class="requiredField"><?php if( isset( $_POST['comments'] ) ) { echo esc_textarea( $_POST['comments'] ); } ?></textarea>
	 	            <?php if( $commentError != '' ) { ?>
	 	                <span class="error"><?php echo $commentError; ?></span>
	 	            <?php } ?>
	 	        </p>
   		 	    <p>
   		 	        <label for="mathCheck"><?php _e( 'Solve:', 'woothemes' ); ?> 3 + 6 =</label>
                    <input type="text" name="mathCheck" id="mathCheck" value="<?php if( isset( $_POST['mathCheck'] ) ) { echo esc_attr( $_POST['mathCheck'] ); } ?>" class="txt requiredField math" />
                    <?php if($mathCheck != '') { ?>
                        <span class="error"><?php echo $mathCheck;?></span>
                    <?php } ?>
	 	        </p>
                <p>
                	<?php wp_nonce_field('contact_form', 'contact_form_nonce' ); ?>
   		 	        <input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if( isset( $_POST['sendCopy'] ) && $_POST['sendCopy'] == true ) { echo ' checked="checked"'; } ?> /><label for="sendCopy"><?php _e( 'Send a copy of this email to yourself', 'woothemes' ); ?></label>
   		 	    </p>
   		 	    <p>
   		 	        <label for="checking" class="screenReader"><?php _e( 'If you want to submit this form, do not enter anything in this field', 'woothemes' ); ?></label><input type="text" name="checking" id="checking" class="screenReader" value="<?php if( isset( $_POST['checking'] ) ) { echo esc_attr( $_POST['checking'] ); } ?>" />
   		 	    </p>
   		 	    <p>
   		 	        <input type="hidden" name="submitted" id="submitted" value="true" /><input class="submit button animated fadeInUp" type="submit" value="<?php esc_attr_e( 'Send Message', 'woothemes' ); ?>" />
   		 	    </p>
   		 	</form>

   		 	<script type="text/javascript">
			<!--//--><![CDATA[//><!--
			jQuery(document).ready(function() {
				jQuery( 'form#location-contact-form').submit(function() {
					jQuery( 'form#location-contact-form .error').remove();
					var hasError = false;
					jQuery( '.requiredField').each(function() {
						if(jQuery(this).hasClass('math')) {
							if( jQuery.trim(jQuery(this).val()) != 9 && jQuery.trim(jQuery(this).val()).toLowerCase() != 'nine' ) {
								jQuery(this).parent().append( '<span class="error"><?php _e( 'You got the maths wrong', 'woothemes' ); ?>.</span>' );
								jQuery(this).addClass( 'inputError' );
								hasError = true;
							} // End If Statement
						} else {
							if(jQuery.trim(jQuery(this).val()) == '') {
								var labelText = jQuery(this).prev( 'label').text();
								jQuery(this).parent().append( '<span class="error"><?php _e( 'You forgot to enter your', 'woothemes' ); ?> '+labelText+'.</span>' );
								jQuery(this).addClass( 'inputError' );
								hasError = true;
							} else if(jQuery(this).hasClass( 'email')) {
								var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
								if(!emailReg.test(jQuery.trim(jQuery(this).val()))) {
									var labelText = jQuery(this).prev( 'label').text();
									jQuery(this).parent().append( '<span class="error"><?php _e( 'You entered an invalid', 'woothemes' ); ?> '+labelText+'.</span>' );
									jQuery(this).addClass( 'inputError' );
									hasError = true;
								} // End If Statement
							} // End If Statement
						} // End If Statement
					});
					if(!hasError) {
						var formInput = jQuery(this).serialize();
						var name = jQuery("#name").val();
					    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
					    console.log(formInput);
					    jQuery.ajax({
					         data: {action: 'contact_form_callback', form: formInput},
					         type: 'post',
					         url: ajaxurl,
					         success: function(data) {
					              if ( data == 1 ) {
					              	// Notify the user with success message
									jQuery( 'form#location-contact-form').slideUp( "fast", function() {
										jQuery(this).before( '<p class="tick"><?php _e( '<strong>Thanks!</strong> Your email was successfully sent.', 'woothemes' ); ?></p>' );
									});
					              } else {
					              	// Has errors
					              	console.log(data);
					              } // End If Statement
					        }
					    });
					    return false;
					} // End If Statement
					return false;
				});
			});
			//-->!]]>
			</script>
    	</section><!-- /#location-contact -->
    	<?php
    	} // End If Statement
	} // End form_output()

	/**
	 * Contact form title
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function contact_form_title() {
		echo '<h2>' . __( 'Contact Form', 'woothemes' ) . '</h2>';
	} // End contact_form_title()

	/**
	 * Ajax callback function for form submission
	 * @access public
	 * @param  $args array
	 * @since  1.0.0
	 * @return array or string
	 */
	public function form_callback( $args ) {
		$emailSent = false;
		$nameError = '';
		$emailError = '';
		$commentError = '';
		$mathCheck = '';

		// Nonce test
		if ( !is_array($args) ) {
			parse_str( $_POST['form'], $args );
		} // End If Statement

		if ( !wp_verify_nonce( $args['contact_form_nonce'], 'contact_form' ) ) {
			die( 'Failed security check' );
		} // End If Statement

		do_action( 'pre_contact_form_submission' );

		//If the form is submitted
		if( isset( $args['submitted'] ) ) {

			//Check to see if the honeypot captcha field was filled in
			if( trim( $args['checking'] ) !== '' ) {
				$captchaError = true;
			} else {

				// Check math field
				if( $args['mathCheck'] != 9 && strcasecmp( trim( $args['mathCheck'] ), 'nine' ) != 0  ) {
					$mathCheck = __( 'You got the maths wrong.', 'woothemes' );
					$hasError = true;
				} else {
					$math = trim( $args['mathCheck'] );
				} // End If Statement

				//Check to make sure that the name field is not empty
				if( trim( $args['contactName'] ) === '' ) {
					$nameError =  __( 'You forgot to enter your name.', 'woothemes' );
					$hasError = true;
				} else {
					$name = trim( $args['contactName'] );
				} // End If Statement

				//Check to make sure sure that a valid email address is submitted
				if( trim( $args['email'] ) === '' )  {
					$emailError = __( 'You forgot to enter your email address.', 'woothemes' );
					$hasError = true;
				} else if ( ! eregi( "^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($args['email'] ) ) ) {
					$emailError = __( 'You entered an invalid email address.', 'woothemes' );
					$hasError = true;
				} else {
					$email = trim( $args['email'] );
				} // End If Statement

				//Check to make sure comments were entered
				if( trim( $args['comments'] ) === '' ) {
					$commentError = __( 'You forgot to enter your comments.', 'woothemes' );
					$hasError = true;
				} else {
					$comments = stripslashes( trim( $args['comments'] ) );
				} // End If Statement

				//If there is no error, send the email
				if( ! isset( $hasError ) ) {
					$emailTo = $this->settings->get_value( 'email_address', '' );
					$subject = sprintf( __( 'Contact Form Submission from %s', 'woothemes' ), esc_html( $name ) );
					$sendCopy = trim( $args['sendCopy'] );
					$body = __( "Name: $name \n\nEmail: $email \n\nComments: $comments", 'woothemes' );
					$headers = __( 'From: ', 'woothemes') . "$name <$email>" . "\r\n" . __( 'Reply-To: ', 'woothemes' ) . $email;
					// Send the mail
					do_action( 'pre_contact_form_process' );
					$emailSent = wp_mail( $emailTo, $subject, $body, $headers );
					do_action( 'post_contact_form_process' );
					if( $sendCopy == 'true' ) {
						$subject = __( 'You emailed ', 'woothemes' ) . stripslashes( get_bloginfo( 'title' ) );
						$headers = __( 'From: ', 'woothemes' ) . "$name <$emailTo>";
						wp_mail( $email, $subject, $body, $headers );
					} // End If Statement
				} else {
					$emailSent = false;
				} // End If Statement

			} // End If Statement
		} // End If Statement

		do_action( 'post_contact_form_submission' );

		if ( !isset($_POST['form']) ) {

			return array(	'emailSent' => $emailSent,
							'hasError' => $hasError,
							'captchaError' => $captchaError,
							'mathCheck' => $mathCheck,
							'nameError' => $nameError,
							'emailError' => $emailError,
							'commentError' => $commentError,
							'nameError' => $nameError,
							);
		} else {
			die( $emailSent );
		} // End If Statement
	} // End form_callback()

	/**
	 * Google Maps html and JS
	 * @access public
	 * @param  $args array
	 * @since  1.0.0
	 * @return void
	 */
	public function maps_contact_output($args){
		if ( !is_array($args) ) {
			parse_str( $args, $args );
		} // End If Statement
		extract($args);

		$map_height = $this->map_height;
		$zoom = $this->map_zoom;
		$type = $this->map_type;
		$marker_title = $this->map_marker_title;
		$marker_color = $this->map_marker_color;

		if(empty($map_height)) { $map_height = 250; } ?>

		<div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>

	    <script type="text/javascript">
			jQuery(document).ready(function(){
				function initialize() {
				  	var myLatlng = new google.maps.LatLng(<?php echo $this->geocoords; ?>);
					var myOptions = {
					  zoom: <?php echo $zoom; ?>,
					  center: myLatlng,
					  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
					};
					<?php if( $this->disable_map_mouse_scroll === true ){ ?>
				  		myOptions.scrollwheel = false;
				  	<?php } ?>
				  	var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
			  		var point = new google.maps.LatLng(<?php echo $this->geocoords; ?>);
	  				var root = "<?php echo esc_url( $this->plugin_url ); ?>";
	  				var callout = '<?php echo preg_replace("/[\n\r]/","<br/>",$this->callout); ?>';
	  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
	  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"', $marker_title); ?>
	  				<?php $title = str_replace('&#8211;','-',$title); ?>
	  				<?php $title = str_replace('&#8217;',"`",$title); ?>
	  				<?php $title = str_replace('&#038;','&',$title); ?>
	  				var the_title = '<?php echo html_entity_decode($title) ?>';
				 	var color = '<?php echo $this->map_marker_color; ?>';
		  			createMarker(map,point,root,the_link,the_title,color,callout);
				} // End initialize()

				function handleNoFlash(errorCode) {
					if (errorCode == FLASH_UNAVAILABLE) {
						alert("Error: Flash doesn't appear to be supported by your browser");
						return;
					}
				} // End handleNoFlash()
			initialize();
			});
		</script>
	<?php
	} // End maps_contact_output()

} // End Class
?>
