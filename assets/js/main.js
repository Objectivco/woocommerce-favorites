function refreshList() {
    jQuery.get(window.location, function(data) {
        var products = jQuery(data).find('ul.products li');
        jQuery('ul.products').html(products);
    });
}

jQuery(document).ready(function() {
    jQuery(document).on('click', '.favorite-product > a', function(e) {
        e.preventDefault();
        var productId = jQuery(this).data('product-id');
        var parentElement = jQuery(this).parent();
        var parentProduct = parentElement.parent();
        parentElement.addClass('is-favoriting');
        jQuery.ajax({
            url: favorite_data.ajax_url,
            type: 'post',
            data: {
                action: 'favorite_callback',
                dataType: 'json',
                productId: productId
            },
            success: function(response) {
                var data = JSON.parse(response);
                if ( data.status == 1 ) {
                    parentElement.addClass('is-favorited');
                    parentElement.removeClass('is-favoriting');
                } else if ( data.status == 0 ) {
                    parentElement.removeClass('is-favorited');
                    parentElement.removeClass('is-favoriting');
                    if (jQuery('body').hasClass('woocommerce-favorites')) {
                        refreshList();
                    }
                }

                jQuery('li.favorites-number').html('<a class="favorites-ajax" href="<?php echo $favorites_url; ?>" itemprop="url"><span itemprop="name">What You Love (' + data.count + ')</span></a>');
            }
        });
    });
});
