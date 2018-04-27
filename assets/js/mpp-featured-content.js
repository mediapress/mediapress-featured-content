/* global jQuery */
jQuery(document).ready(function( $ ){
    "use strict";
    /* global MPPFeaturedContent  */
    var ajax_url = MPPFeaturedContent.ajaxUrl;

    $(document).on('click', 'a.mppftc-featured-btn',function() {
        var $this = $(this);

        $.post( ajax_url, {
            action: 'mppftc_featured_action',
            item_id: $this.data('itemId'),
            nonce:   $this.data('nonce')
        }, function(resp){
            if ( resp.success ) {
                $this.html( resp.data.label );
            }
        });

        return false;
    });
});
