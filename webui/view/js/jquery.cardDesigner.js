$(document).ready(function() {
	
	$('#trap').hide();
	
	$('#type select').change(function() {
		
		var selected = $(this).children(':selected').html();
		
		$('.link').hide();
		$('#' + selected).show();
		
		//console.log(selected);
		
	});
	
});