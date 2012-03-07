<?php

	Class extension_emporium extends Extension{
		public function about(){
			return array('name' => 'Emporium',
						 'version' => '0.1',
						 'release-date' => '2012-03-06',
						 'author' => array('name' => 'Henry Singleton',
										   'website' => 'http://henrysingleton.com',
										   'email' => 'henry@henrysingleton.com')
				 		);
		}
	}