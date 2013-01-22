function moveDown(element, target, step, delay) {
	var currentTop = $(element).position().top;

	if (currentTop < target) {
	  	$(element).css("top", currentTop + step);
	    setTimeout(function() { moveDown(element, target, delay); }, delay);
	}
}