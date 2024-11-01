jQuery.noConflict();
function umbigo(id) {
	var compdv = jQuery('.cp-' + id);
	if (compdv.css('display') == 'none') {
		compdv.show("fold", {size:150}, 400);
		return;
	} else {
		compdv.hide("fold", {size:150}, 400);
		return;
	};
}
