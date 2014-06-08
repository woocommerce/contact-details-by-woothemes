contact-details-by-woothemes
============================

Todo

Add styling for theme fixes for map common issues:

#single_map_canvas label { width: auto; display:inline; }
#single_map_canvas img { max-width: none; background: none; }

Move JS to separate file
Account for multiple maps, forms, and html ids
Widget Cleanup
Additional Styling
Form including Nonce, ajax and non javascript callback

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

Contact Form Actions:
- pre_contact_form_process
- post_contact_form_process