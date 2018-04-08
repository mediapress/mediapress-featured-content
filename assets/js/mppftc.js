jQuery(document).ready(function( $ ){

    var ajax_url = MPPFTC.ajax_url;

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
