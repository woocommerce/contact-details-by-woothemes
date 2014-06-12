<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Contact Details Widget
 *
 * A WooThemes standardized Contact Details widget.
 *
 * @package WordPress
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * protected $contact_details_by_woothemes_widget_cssclass
 * protected $contact_details_by_woothemes_widget_description
 * protected $contact_details_by_woothemes_widget_idbase
 * protected $contact_details_by_woothemes_widget_title
 *
 * - __construct()
 * - widget()
 * - form()
 */
class Contact_Details_by_WooThemes_Widget extends WP_Widget {
	protected $contact_details_by_woothemes_widget_cssclass;
	protected $contact_details_by_woothemes_widget_description;
	protected $contact_details_by_woothemes_widget_idbase;
	protected $contact_details_by_woothemes_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->contact_details_by_woothemes_widget_cssclass 	= 'widget_contact_details_by_woothemes_items';
		$this->contact_details_by_woothemes_widget_description 	= __( 'Contact Details by WooThemes.', 'contact-details-by-woothemes' );
		$this->contact_details_by_woothemes_widget_idbase 		= 'contact-details-by-woothemes';
		$this->contact_details_by_woothemes_widget_title 		= __( 'Contact Details by WooThemes', 'contact-details-by-woothemes' );

		// Cache
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> $this->contact_details_by_woothemes_widget_cssclass,
			'description' 	=> $this->contact_details_by_woothemes_widget_description
			);

		/* Widget control settings. */
		$control_ops = array(
			'id_base' 	=> $this->contact_details_by_woothemes_widget_idbase
			);

		/* Create the widget. */
		$this->WP_Widget( $this->contact_details_by_woothemes_widget_idbase, $this->contact_details_by_woothemes_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$cache = wp_cache_get( 'widget_contact_details_by_woothemes_items', 'widget' );

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		if ( isset( $instance['output'] ) ) {
			$args['output'] = $instance['output'];
		} else {
			$args['output'] = $args['output'];
		}

		ob_start();

		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title 		= apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$output 	= $args['output'];

		$args = array();

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { $args['title'] = $title; }

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->contact_details_by_woothemes_widget_cssclass . '_top' );

		// Display S&C.
		echo $before_widget;

		if ( $title )
				echo $before_title . $title . $after_title;

		echo '<div class="contact-details-by-woothemes-connect">';
			$atts = array(
						'display' => $output
						);
			echo Contact_Details_by_WooThemes()->contact_details_output( $atts );
		echo '</div><!--/.contact-details-by-woothemes-connect-->';

		echo $after_widget;

		// Add actions for plugins/themes to hook onto.
		do_action( $this->contact_details_by_woothemes_widget_cssclass . '_bottom' );

		$cache[ $widget_id ] = ob_get_flush();

		wp_cache_set( 'widget_contact_details_by_woothemes_items', $cache, 'widget' );

	} // End widget()

	/**
	 * Method to update the settings from the form() method.
	 * @since  1.0.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] 		= strip_tags( $new_instance['title'] );
		$instance['output'] 	= esc_html( $new_instance['output'] );

		/* Flush cache. */
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset($alloptions['widget_woothemes_wc_items']) ) {
			delete_option( 'widget_woothemes_wc_items' );
		}

		return $instance;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
    public function form( $instance ) {

		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
			'title' 		=> __( 'Contact Details by WooThemes', 'contact-details-by-woothemes' ),
			'output'		=> 'all'
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'contact-details-by-woothemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>

		<?php
		$select_data = array(	'all' 		=> __( 'All Contact Details', 'contact-details-by-woothemes' ),
								'details' 	=> __( 'Location Details', 'contact-details-by-woothemes' ),
								'social' 	=> __( 'Social Media Links', 'contact-details-by-woothemes' ),
								'map' 		=> __( 'Google Map', 'contact-details-by-woothemes' )
								 );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'output' ); ?>"><?php _e( 'Output:', 'contact-details-by-woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'output' ); ?>" class="widefat">
				<?php
				foreach ( $select_data as $key => $value ) { ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['output'], $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } // End For Loop
				?>
			</select>
		</p>
	<?php
	} // End form()

	/**
	 * Flush widget cache
	 * @since  1.0.0
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( 'widget_contact_details_by_woothemes_items', 'widget' );
	}

} // End Class