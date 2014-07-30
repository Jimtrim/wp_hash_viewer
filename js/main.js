/**
* Define Array.indexOf for IE 6, 7 and 8
* Source: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/indexOf 
*/

if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function (searchElement, fromIndex) {
	var k;
	if (this == null) {
	  throw new TypeError('"this" is null or not defined');
	}
	var O = Object(this);
	var len = O.length >>> 0;
	if (len === 0) {
	  return -1;
	}
	var n = +fromIndex || 0;
	if (Math.abs(n) === Infinity) {
	  n = 0;
	}
	if (n >= len) {
	  return -1;
	}
	k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
	while (k < len) {
	  var kValue;
	  if (k in O && O[k] === searchElement) {
		return k;
	  }
	  k++;
	}
	return -1;
  };
}


var HashViewer = HashViewer || {};
HashViewer.next_url = undefined;
HashViewer.next_max_tag_id = undefined;
HashViewer.last_tag = '';
HashViewer.no_of_pictures = 0;
HashViewer.CLIENT_ID = "d81afea83c3f40b5a5485418e2a53aa7";

HashViewer.reset = function() {
	console.log("reset called");
	HashViewer.next_url = undefined;
	HashViewer.next_max_tag_id = undefined;
	HashViewer.last_tag = '';
	HashViewer.no_of_pictures = 0;
	jQuery("#gallery").html('');
	jQuery("#more-btn").addClass('hidden');
};

HashViewer.getInputField = function() {
	return jQuery("input[id='tag-text']");
};
HashViewer.getInputValue = function() {
	return HashViewer.getInputField().val();
};
HashViewer.setInputValue = function(val) {
	HashViewer.getInputField().val(val);
};


HashViewer.splitHashtags = function(text) {
	var result = text[0];
	for (var i = 1; i < text.length; i++) {
		if (text[i] == '#' && text[i - 1] != ' ') {
			result += ' ';
		}
		result += text[i];
	}
	return result;
};


HashViewer.createGalleryBlock = function(post) {
	var image = post.images.low_resolution;
	var user = post.user;

	var param = post.id +',' + post.id +','

	var fav_class = (HashViewer.wp.savedImages.indexOf(post.id) >= 0 ? "saved" : "unsaved");

	// define data-attributes, making storing of favorited images easier
	var out = '<div class="gallery-block text-center"' 
					+' data-mediaid="'+post.id+'"'
					+' data-username="'+user.username+'"'
					+' data-imageurl="'+image.url+'"'
					+' data-createdat="'+post.created_time+'"'
			+'>';
	out += '<a href="' + post.link + '"><img class="gallery-image col-sm-12" src ="' + image.url + '" /></a>';
	out += '<em>User: <a href="http://instagram.com/' + user.username + '">' + user.username + '</a></em>';
	if (jQuery('#compIdField').text() != "") { // Image selection should only be used when in competition mode
		out += '<a href="javascript:HashViewer.wp.saveImage(\''+post.id+'\');">'
		out += '<span class="favorite-icon ' + fav_class + ' glyphicon glyphicon-heart"></span>';
		out += '</a>';
	}
	out += '</div>'; //width-fix END

	return out;
};

HashViewer.displayError = function(message) {
	jQuery('#error-container').html('<span>' + message + '</span>').removeClass('hidden');
	console.log(message);
};

HashViewer.updateGallery = function(in_tag) {
	console.log("updateGallery called");
	jQuery('#error-container').addClass('hidden'); // Hide old errors
	

	// get tag, and store as last tag for future comparison
	var tag = in_tag || HashViewer.getInputValue() || HashViewer.last_tag;
	tag = HashViewer.util.removeLeadingHash(tag);
	if (tag === "") return;
	if (HashViewer.last_tag != tag)
		HashViewer.reset();
	HashViewer.last_tag = tag;

	// Set search field input tag if none present
	if (HashViewer.getInputValue() === "")
		HashViewer.setInputValue(tag); 

	// save next url for use in Load More button
	HashViewer.next_url = 'https://api.instagram.com/v1/tags/' + tag + '/media/recent?client_id=' + HashViewer.CLIENT_ID;
	console.log(HashViewer.next_url);
	if (HashViewer.next_max_tag_id)
		HashViewer.next_url += "&max_tag_id=" + HashViewer.next_max_tag_id;

	// make sure that a list Hashviewer.wp.savedImages exists
	HashViewer.wp = HashViewer.wp || {};
	HashViewer.wp.savedImages = HashViewer.wp.savedImages || [];

	// request next block of images
	jQuery.ajax({
		url: HashViewer.next_url,
		type: 'get',
		dataType: 'jsonp'
	})
		.done(function(res) {
			if (res.meta.code >= 400) { // if requests responds with HTTP error codes
				HashViewer.displayError("ERROR: " + res.meta.error_message);
			} else {
				jQuery.each(res.data, function(i, post) {
					jQuery("#gallery").append(HashViewer.createGalleryBlock(post));
					HashViewer.no_of_pictures += 1;
				});

				if (res.pagination.next_max_tag_id) {
					jQuery("#more-btn").removeClass('hidden');
					HashViewer.next_max_tag_id = res.pagination.next_max_tag_id;
				} else {
					jQuery("#more-btn").addClass('hidden');
				}
			}

		})
		.fail(function(err) {
			console.log("fail: " + HashViewer.next_url);
			HashViewer.displayError("FAILURE:" + err);
		})
		.error(function(XHR, status, err) {
			console.log("error: " + HashViewer.next_url);
			HashViewer.displayError("ERROR:" + err);
		});

	return this;
};


HashViewer.util = HashViewer.Util || {};
HashViewer.util.removeLeadingHash = function(str) {
	if (typeof str == "string" && str.charAt(0) == '#') {
		return str.substring(1);
	}
	return str;
};


jQuery(document).ready(function($) {
	// $("button[id='tag-btn']").bind('click', HashViewer.updateWindowHash()); 
	HashViewer.getInputField().keypress(function(e) { // enter-fix for search
		if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
			$("button[id='tag-btn']").click();
			$(this).blur();
			return false;
		} else {
			return true;
		}
	});
	HashViewer.wp.getSavedImages();

});