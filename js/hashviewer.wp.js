var HashViewer = HashViewer || {}; // extend existing
HashViewer.wp = HashViewer.wp || {};

HashViewer.wp.savedImages = [];

HashViewer.wp.saveImage = function(id) {
	var el = jQuery("div[data-mediaid='"+id+"']")


    var data = {
        'action': 'save_image',
        'compId': parseInt(jQuery('#compIdField').text(), 10),
        'mediaId': el.data('mediaid'),
        'instagramUsername': el.data('username'),
        'instagramImage': el.data('imageurl'),
        'createdAt': el.data('createdat')
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
    	console.log(data);
    	console.log("---")
        console.log('saveImage, response: \n' + response);
        el.find('.favorite-icon')
            .removeClass("unsaved")
            .addClass("saved");

    });
}

HashViewer.wp.getSavedImages = function() {

    var data = {
        'action': 'get_saved_images',
        'compId': parseInt(jQuery('#compIdField').text(), 10)
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.get(ajaxurl, data, function(response) {
        console.log(data);
        console.log("---")
        console.log('getSavedImage, response: \n' + response);

        HashViewer.wp.savedImages = JSON.parse(response);
        console.log(HashViewer.wp.savedImages);
    });
}