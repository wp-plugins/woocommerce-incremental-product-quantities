jQuery(document).ready( function($) {
		
	$(".qty ").change(function() {
	
		var new_qty = $(this).val();
		var step = $(this).attr( 'step' );
		var max = $(this).attr( 'max' );
		var min = $(this).attr( 'min' );
		var rem = ( new_qty - min ) % step;
		
		if ( +new_qty > +max ) {
			new_qty = max;

		} else if ( +new_qty < +min ) {
			new_qty = min;
			
		} else if ( rem != 0 ) {
			new_qty = +new_qty + (+step - +rem);
			$(this).val( new_qty );
		}
		
		$(this).val( new_qty );
		$(this).attr( "data-quantity", new_qty );
	});
	
	
});
