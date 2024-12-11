jQuery(document).ready(function($) {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('cloned_order_id')) {
        var clonedOrderId = urlParams.get('cloned_order_id');
        var clonedOrderRow = $('#post-' + clonedOrderId);

        if (clonedOrderRow.length) {
            clonedOrderRow.addClass('cloned-order');
        }
    }
});