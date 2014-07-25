var HashViewer = HashViewer || {};
HashViewer.wp = HashViewer.wp || {};

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
        console.log('Got this from the server: \n' + response);
        el.find('.favorite-icon').css("color", "green");
    });
}