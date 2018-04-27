/* global jQuery */
jQuery(document).ready(function( $ ){
    "use strict";
    /* global MPPFeaturedContent  */
    var ajax_url = MPPFeaturedContent.ajaxUrl;

    $(document).on('click', 'a.mppftc-featured-btn',function() {
        var $this = $(this);

        $.post( ajax_url, {
            action: 'mppftc_featured_action',
            item_id: $this.data('item-id'),
            nonce:   $this.data('nonce')
        }, function(resp){
            if ( resp.success ) {
                $this.replaceWith( resp.data.button );
            }
        });

        return false;
    });

    // lightbox for media.
    $(document).on('click', '.mppftc-featured-media a', function () {
        var $this = $(this);
        var $container = $this.parents( '.mppftc-featured-media' );
        if( $container.data('mpp-lightbox-enabled') == '0' ) {
            return ;
        }

        if( ! mpp.lightbox.isLoaded() ) {
            return ;
        }
        mpp.lightbox.media(  $container.data('mpp-media-ids'), 0, $this.attr('href'), $this.data('mpp-media-id') );
        return false;
    });
});
