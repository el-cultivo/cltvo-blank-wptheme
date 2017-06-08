const $ = jQuery
var hexColorSampleChanger = function() {
	//TODO validate that the input value is a hex number
	var input = $('.hex-color-text-input_JS');
	if (input.lenght < 0) { return;}
	 input.on('change', function() {
	 	var $this = $(this),
	 		sample = $this.siblings('.hex-color-text-sample__container').find('.hex-color-text-sample_JS'),
	 		color = $this.val() === '' ? 'transparent' : '#'+$this.val();
	 	sample.css('backgroundColor', color);
	 });
};

$(window).load(function() {
	hexColorSampleChanger();
});

