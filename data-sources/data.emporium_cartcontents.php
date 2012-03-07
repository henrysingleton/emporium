<?php	
		
	Final Class datasourceEmporium_cartcontents Extends DataSource{			

		function about(){
			return array(
					 'name' => 'Emporium: Cart Contents',
					 'author' => array(
							'name' => 'Henry Singleton',
							'email' => 'henry@henrysingleton.com'),
					 'version' => '0.1',
					 'release-date' => '2012-03-06');	
		}

		public function grab(){
			
			$xml = new XMLElement('emporium-cart');
		
			$cart = new Cookie(__SYM_COOKIE_PREFIX__ . '-cart', TWO_WEEKS, __SYM_COOKIE_PATH__);			
			
			Frontend::Page()->_param['emporium-cart-items'] = implode(', ',array_keys($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart']));


			if(!is_array($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart']) || empty($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart'])) return NULL;
				
			foreach($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart'] as $product_id => $val){
				if(strlen($val) <= 0) continue;
				$xml->appendChild(new XMLElement('item', NULL, array('product-id' => $product_id, 'qty' => $cart->get($product_id))));
				$count++;
	        }
	    	
			//get the total of all the items in the cart
			$xml->setAttribute('total',array_sum($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart']));
			
			return $xml;
				
		}		
		
	}