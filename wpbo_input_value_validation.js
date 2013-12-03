jQuery(document).ready( function($) {
		
	$(".qty ").change(function() {
	
		// Get values from input box
		var new_qty = $(this).val();
		var step = $(this).attr( 'step' );
		var max = $(this).attr( 'max' );
		var min = $(this).attr( 'min' );
		
		// Adjust default values if values are blank
		if ( min == '' ) 
			min = 1;
		
		if ( step == '' ) 
			step = 1;
		
		// Calculate remainder
		var rem = ( new_qty - min ) % step;

		// Max Value Validation
		if ( +new_qty > +max && max != '' ) {
			new_qty = max;
		
		// Min Value Validation
		} else if ( +new_qty < +min && min != '' ) {
			new_qty = min;
		
		// Step Value Value Validation
		} else if ( rem != 0 ) {
			new_qty = +new_qty + (+step - +rem);
		}
		
		// Set the new values
		$(this).val( new_qty );
		$(this).attr( "data-quantity", new_qty );
	});
	
});
