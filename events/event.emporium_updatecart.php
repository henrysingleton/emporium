<?php

	if(!defined('__IN_SYMPHONY__')) die('<h2>Error</h2><p>You cannot directly access this file</p>');

	Final Class eventEmporium_updatecart extends Event{

		public static function about(){
					
			return array(
						 'name' => 'Emporium: Update Cart',
						'author' => array(
							'name' => 'Henry Singleton',
							'website' => 'http://henry.sites.randb.com.au/cfd',
							'email' => 'henry@randb.com.au'),
						 'version' => '0.1',
						 'release-date' => '2012-03-06',						 
					);						 
		}
				
		public static function documentation(){
			return '
				<p>This event takes a product identifier and a qty, and updates the session-based cart accordingly. Values can be either GET or POST data.</p>
				
				<h3>Add to cart</h3>
				<p>To add a product to the cart, pass through a unique product identifier (this could be an internal Symphony ID, a product SKU, etc) and the quantity you want added to the cart.</p>
								
				<pre class="XML"><code>
				&lt;form method="post"&gt;
					&lt;input type="hidden" name="fields[product-id]" value="3/"/&gt;
					&lt;label&gt;Qty: &lt;input name="fields[qty]" type="text" value="1" /&gt;&lt;/label&gt;
					&lt;input type="submit" name="action[update-cart]" value="Add to Cart"/&gt;
				&lt;/form&gt;
				</code></pre>

				<p><code>&lt;a href="{$root}/cart/?update-cart&amp;product-id=3&amp;qty=1"&gt;Add to Cart&lt;/a&gt;</code></p>
				
				<h4>Response XML</h4>
				<pre class="XML"><code>
				&lt;emporium&gt;
					&lt;item product-id=&quot;3&quot; qty=&quot;1&quot; action=&quot;added&quot; /&gt;
				&lt;/emporium&gt;
				</code></pre>
				
				<h3>Update item in cart</h3>
				<p>If an item already exists in the cart, and the quantity is not 0, the quantity of the product will be updated to match the passed quantity. </p>
				
				<h4>Response XML</h4>
				<pre class="XML"><code>
				&lt;emporium&gt;
					&lt;item product-id=&quot;3&quot; qty=&quot;7&quot; action=&quot;updated&quot; /&gt;
				&lt;/emporium&gt;
				</code></pre>
				
				<h3>Remove from cart</h3>
				<p>To remove an item from the cart, simply pass through the product identifier with a qty of 0. 

				<pre class="XML"><code>
				&lt;form method="post"&gt;
					&lt;input type="hidden" name="fields[product-id]" value="3/"/&gt;
					&lt;label&gt;Qty: &lt;input name="fields[qty]" type="text" value="0" /&gt;&lt;/label&gt;
					&lt;input type="submit" name="action[update-cart]" value="Update Cart"/&gt;
				&lt;/form&gt;
				</code></pre>
				
				<p><code>&lt;a href="{$root}/cart/?update-cart&amp;product-id=3&amp;qty=0"&gt;Remove from Cart&lt;/a&gt;</code></p>
				
				<h4>Response XML</h4>
				<pre class="XML"><code>
				&lt;emporium&gt;
					&lt;item product-id=&quot;3&quot; action=&quot;removed&quot; /&gt;
				&lt;/emporium&gt;
				</code></pre>
			';
		}
		
		public function load(){
			if ( 
				is_array($_REQUEST) && 
				array_key_exists('update-cart',$_REQUEST) && 
				array_key_exists('product-id',$_REQUEST) && 
				array_key_exists('qty',$_REQUEST) && 
				is_numeric($_REQUEST['qty'])
			) return $this->__trigger();
		}

		protected function __trigger(){
			$xml = new XMLElement('emporium');
			$cart = new Cookie(__SYM_COOKIE_PREFIX__ . '-cart', TWO_WEEKS, __SYM_COOKIE_PATH__);
			$product_id = $_REQUEST['product-id'];
			$qty = (int) $_REQUEST['qty'];
			
			//check if product already exists
			$exists = (bool) $_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart'][$product_id];
			
			//if we're setting a qty greater than 0, add it to the cart. If less than 0, remove it. 
			if ($qty > 0) {
				$cart->set($product_id, $qty);
				$xml->appendChild(new XMLElement('item', NULL, array('product-id' => $product_id, 'qty' => $qty, 'action' => ($exists ? 'updated' : 'added'))));
			} elseif ($exists) {
				unset($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart'][$product_id]);
				$xml->appendChild(new XMLElement('item', NULL, array('product-id' => $product_id, 'action' => 'removed')));
			}

			//add all the product ids to the page params pool, so we can easily filter datasources
			Frontend::Page()->_param['emporium-cart-items'] = implode(',',array_keys($_COOKIE[__SYM_COOKIE_PREFIX__ . '-cart']));
			
			return $xml;
		}
	}

