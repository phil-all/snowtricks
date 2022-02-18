var limit = 15;
var per_page = 5;
jQuery('#results > div.data:gt(' + (limit - 1) + ')').hide();
if (jQuery('#results > div.data').length <= limit) {
    jQuery('#results-show-more').hide();
}

jQuery('#results-show-more').bind('click', function (event) {
    event.preventDefault();
    limit += per_page;
    jQuery('#results > div.data:lt(' + (limit) + ')').show();
    if (jQuery('#results > div.data').length <= limit) {
        jQuery(this).hide();
    }
});