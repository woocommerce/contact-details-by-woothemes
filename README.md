contact-details-by-woothemes
============================

Todo

- Account for multiple maps, forms, and html ids
- Additional Styling for twitter/facebook links

How to output:

- Use the template action -> do_action( 'contact_details' );
- Use the shortcode -> [contact_details]
- Use the widget -> Contact Details by WooThemes

All output functions take the following arguments:
'display' with possible values of:
- 'all'
- 'details'
- 'social'
- 'map'

Contact Details Actions:
- pre_contact_details_output
- pre_contact_details_location_output
- pre_contact_details_social_output
- pre_contact_details_map_output
- post_contact_details_output
- post_contact_details_location_output
- post_contact_details_social_output
- post_contact_details_map_output