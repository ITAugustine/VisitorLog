/**
 * Visitor Log actions js - 1.0
 *
 */
jQuery(function($) {

    $( '#visitorlog_delete_all' ).click(function() {
        let redirect = `admin.php?page=`+`${visitorlog_obj.action1}`+`${visitorlog_obj.action2}`+`${visitorlog_obj.action7}`;
        swal({
            title: visitorlog_obj.msg1,
            text: "",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: visitorlog_obj.msg3,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: visitorlog_obj.msg2
        },
        function() {
            location.href=`${redirect}`;
        });
    }); 

    $( '#visitorlog_delete_select' ).click(function() {
    	let redirect = `admin.php?page=`+`${visitorlog_obj.action1}`;
        swal({
            title: visitorlog_obj.msg1,
            text: "",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: visitorlog_obj.msg3,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: visitorlog_obj.msg2
        },
        function(){
            if ( checked_all === 'on' ) {
                checked_all = '';
                location.href=`${redirect}`+`${visitorlog_obj.action2}`;
            }
            if ( checked_all === 'off' ) {
                checked_all = '';
                return;
            }
            json_checked_arr = JSON.stringify( checked_arr );
            location.href=`${redirect}`+`${visitorlog_obj.action3}`+`${visitorlog_obj.action4}`+json_checked_arr+`${visitorlog_obj.action7}`;
        });
    }); 
    $( '#visitorlog_ban_select' ).click(function() {
        let redirect = `admin.php?page=`+`${visitorlog_obj.action1}`;
        swal({
            title: visitorlog_obj.msg1,
            text: "",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: visitorlog_obj.msg3,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: visitorlog_obj.msg2
        },
        function(){
            if ( checked_all === 'on' ) {
                checked_all = '';
                json_checked_arr = JSON.stringify( checked_arr );
            location.href=`${redirect}`+`${visitorlog_obj.action5}`+`${visitorlog_obj.action7}`+`${visitorlog_obj.action4}`+json_checked_arr;
            }
            if ( checked_all === 'off' ) {
                checked_all = '';
                return;
            }
            json_checked_arr = JSON.stringify( checked_arr );
            location.href=`${redirect}`+`${visitorlog_obj.action5}`+`${visitorlog_obj.action7}`+`${visitorlog_obj.action4}`+json_checked_arr;
        });
    });
    $( '#visitorlog_unban_select' ).click(function() {
        let redirect = `admin.php?page=`+`${visitorlog_obj.action1}`;
        swal({
            title: visitorlog_obj.msg1,
            text: "",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: visitorlog_obj.msg3,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: visitorlog_obj.msg2
        },
        function(){
            if ( checked_all === 'on' ) {
                checked_all = '';
                json_checked_arr = JSON.stringify( checked_arr );
            location.href=`${redirect}`+`${visitorlog_obj.action6}`+`${visitorlog_obj.action7}`+`${visitorlog_obj.action4}`+json_checked_arr;
            }
            if ( checked_all === 'off' ) {
                checked_all = '';
                return;
            }
            json_checked_arr = JSON.stringify( checked_arr );
            location.href=`${redirect}`+`${visitorlog_obj.action6}`+`${visitorlog_obj.action7}`+`${visitorlog_obj.action4}`+json_checked_arr;
        });
    });


}); // END jQuery(function($)