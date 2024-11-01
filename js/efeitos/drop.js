jQuery.noConflict();
function umbigo(id) {
	var compdv = jQuery('.cp-' + id);
	if (compdv.css('display') == 'none') {
		compdv.show("drop", {direction:"up", distance:"20"}, 400);
		return;
	} else {
		compdv.hide("drop", {direction:"up", distance:"20"}, 400);
		return;
	};
}
