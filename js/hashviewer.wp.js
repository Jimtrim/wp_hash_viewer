var HashViewer = HashViewer || {};
HashViewer.wp = HashViewer.wp || {};

HashViewer.wp.saveImage = function(id, username, imageUrl, createdTime) {
    var data = {
        'action': 'save_image',
        'compId': parseInt(jQuery('#compIdField').text(), 10),
        'instagramMediaId': id,
        'instagramUsername': username,
        'instagramImage': imageUrl,
        'createdAt': createdTime
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        console.log('Got this from the server: ' + response);
    });
}