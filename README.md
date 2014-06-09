contact-details-by-woothemes
============================

Todo

Add styling for theme fixes for map common issues:

Move JS to separate file
Account for multiple maps, forms, and html ids
Additional Styling

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
- pre_contact_form_submission
- post_contact_form_submission

Add Filters for Contact Title and Messages