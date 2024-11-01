jQuery.noConflict();
function umbigo(id) {
	var compdv = jQuery('.cp-' + id);
	if (compdv.css('display') == 'block') {
		compdv.fadeOut(400);
		return;
	} else {
		compdv.fadeIn(400);
		return;
	};
}
