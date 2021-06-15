( function( $) {

    // Initiating Select2
    $(document).ready(function() {
        // Test email
        $( '.button.test-email' ).on( 'click', function( e ) {
            e.preventDefault();
            let email = $( 'input[name=' + $(this).data('target') + ']' ).val();
            let trigger = $(this).data('trigger');

            let data = {
                'email': email,
                'trigger': trigger
            };

            // TODO: show loader here

            if( email !== '' ) {
                $.ajax({
                    url: MightyRFD.ajaxUrl,
                    type: 'post',
                    data: {
                        action: MightyRFD.mailAction,
                        security: MightyRFD.nonce,
                        fields: data
                    },
                    success: function( data ) {
                        // TODO: end loader and show success message
                    },
                    error: function() {
                        console.log('#212 Something went wrong!');
                    }
                });
            }
            
        });

        // Delete expired coupons
        $( '.delete-expired-coupons' ) .on( 'click', function( e ) {
            e.preventDefault();

            // Loader
            $( '#mighty-basic-configuration .delete-expired-coupons' ).after('<p class="mrfd-loader"><span class="loader dashicons dashicons-image-filter"></span></p>');

            $.ajax({
                url: MightyRFD.ajaxUrl,
                type: 'post',
                data: {
                    action: MightyRFD.couponDeletionAction
                },
                success: function( data ) {
                    console.log('Deletion successful');

                    $('#mighty-basic-configuration .loader').remove();
                    $('#mighty-basic-configuration .delete-expired-coupons').after('<p class="info-message">✅ Expired coupons deleted successfully.</p>');
                },
                error: function() {
                    console.log('#213 Coupons deletion failed!');
                }
            });
        });
    });
    
}) ( jQuery );