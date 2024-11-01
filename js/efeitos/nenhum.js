jQuery.noConflict();
function umbigo(id) {
	var compdv = jQuery('.cp-' + id);
	if (compdv.css('display') == 'none') {
		compdv.show();
		return;
	} else {
		compdv.hide();
		return;
	};
}
