<?php
/*
*	Add to Cart Validation to ensure quantity ordered follows
*	the user's rules.
*/
add_action( 'woocommerce_add_to_cart_validation', 'wpbo_add_to_cart_validation', 5, 4 );

function wpbo_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id, $variations ) {

	return wpbo_validate_single_product( $passed, $product_id, $quantity, $variation_id, $variations, false );
	
}

/*
*	Cart Update Validation to ensure quantity ordered follows
*	the user's rules. Items are being passed thro
*/
add_action( 'woocommerce_update_cart_validation', 'wpbo_update_cart_validation', 5, 4 );

function wpbo_update_cart_validation( $passed, $cart_item_key, $values, $quantity ) {

	return wpbo_validate_single_product( $passed, $values['product_id'], $quantity, $values['variation_id'], $values['variations'], true );
	
}

/*
*	Validates a single product based on the quantity rules applied to it.
*	It will also validate based on the quantity in the cart.
*/
function wpbo_validate_single_product( $passed, $product_id, $quantity, $variation_id, $variations, $from_cart ) {
	global $woocommerce, $product;
	$product = get_product( $product_id );
	$title = $product->get_title();

	// Return Defaults if it isn't a simple product
	if( $product->product_type != 'simple' ) {
		return true;
	}

	// Get the applied rule and values - if they exist
	$rule = wpbo_get_applied_rule( $product );
	$values = wpbo_get_value_from_rule( 'all', $product, $rule );
	extract( $values ); // $min_value, $max_value, $step, $priority
			
	// Inactive Products can be ignored
	if ( $values == null )
		return true;

	// Min Validation
	if ( $min_value != null && $quantity < $min_value ) {
		$woocommerce->add_error( sprintf( __( "You must add a minimum of %s %s's to your cart.", 'woocommerce' ), $min_value, $title ) );
		return false;
	}
	
	// Max Validation
	if ( $max_value != null && $quantity > $max_value ) {
		$woocommerce->add_error( sprintf( __( "You may only add a maximum of %s %s's to your cart.", 'woocommerce' ), $max_value, $title ) );
		return false;
	}
	
	// Subtract the min value from quantity to calc remainder if min value exists
	if ( $min_value != 0 ) {
		$rem_qty = $quantity - $min_value;
	} else {
		$rem_qty = $quantity;
	}
	
	// Step Validation	
	if ( $step != null && $rem_qty % $step != 0 ) {
		$woocommerce->add_error( sprintf( __( "You may only add a %s in multiples of %s to your cart.", 'woocommerce' ), $title, $step ) );
		return false;
	}
	
	// Don't run Cart Validations if user is updating the cart
	if ( $from_cart != true ) {
	
		// Get Cart Quantity for the product
		foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			if( $product_id == $_product->id ) {
				$cart_qty = $values['quantity'];
			}
		}
		
		//  If there aren't any items in the cart already, ignore these validations
		if ( $cart_qty != null ) {
		
			// Total Cart Quantity Min Validation
			if ( $min_value != null && ( $quantity + $cart_qty ) < $min_value ) {
				$woocommerce->add_error( sprintf( __( "Your cart must have a minimum of %s %s's to proceed.", 'woocommerce' ), $min_value, $title ) );
				return false;
			}
		
			// Total Cart Quantity Max Validation
			if ( $max_value != null && ( $quantity + $cart_qty ) > $max_value ) {
				$woocommerce->add_error( sprintf( __( "You can only purchase a maximum of %s %s's at once and your cart already has %s %s's in it already.", 'woocommerce' ), $max_value, $title, $cart_qty, $title ) );
				return false;
			}
			
			// Subtract the min value from cart quantity to calc remainder if min value exists
			if ( $min_value != 0 ) {
				$cart_qty_rem = $quantity + $cart_qty - $min_value;
			} else {
				$cart_qty_rem = $quantity + $cart_qty;
			}
			
			// Total Cart Quantity Step Validation
			if ( $step != null && $cart_qty_rem % step != 0 ) {
				$woocommerce->add_error( sprintf( __("You may only purchase %s in multiples of %s.", 'woocommerce' ), $title, $step  ) );
				return false;
			}
		}
	}
	
	return true;
}