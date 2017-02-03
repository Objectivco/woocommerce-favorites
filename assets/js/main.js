jQuery(document).ready(function() {
    jQuery('.favorite-product > a').on('click', function(e) {
        e.preventDefault();
        var postId = jQuery(this).data('product-id');
        console.log(postId);
        jQuery.ajax({
            url: favorite_data.ajax_url,
            type: 'post',
            data: {
                action: 'favorite_callback',
                postId: postId
            },
            success: function(response) {
                console.log(response);
            }
        });
    });
});
