// Enable Deliver to a different address as default

setTimeout(function() {
    var checkbox = jQuery('#ship-to-different-address-checkbox');
    checkbox.prop('checked', true);
    checkbox.trigger('change');
}, 1000);

