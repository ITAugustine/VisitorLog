/**
 * Visitor Log header js - 1.0
 *
 */
var checked_all = '';
var checked_arr = [];
var json_checked_arr = [];
checked_arr.length = 0;

jQuery(function($){

    $( 'body input:checkbox' ).click(function() {
        checked_arr.length = 0;
        if ( $('body input:checkbox').is(':checked') ) {

            $('input:checkbox:checked').each(function() {
                checked_arr.push( $(this).val() );
            });
        }
    }); 
    $( '#visitorlog_checkbox_select' ).click(function() {
        if ( $('#visitorlog_checkbox_select').is(':checked') ) {
            checked_arr.length = 0;
            $('body input:checkbox').prop('checked', true);

            $('input:checkbox:checked').each(function() {
                checked_arr.push( $(this).val() );
            });
            checked_all = 'on';
        } else {
            checked_arr.length = 0;
            $('body input:checkbox').prop('checked', false);
            checked_all = 'off';
        }
    });

}); // END jQuery(function($)
