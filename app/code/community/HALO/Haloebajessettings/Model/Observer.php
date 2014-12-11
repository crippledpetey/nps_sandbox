<?php

	class HALO_Haloebajessettings_Model_Observer{

		public function afterConfigSave(){
			/* RUNS AFTER ADMIN CONFIGURATION IS SAVED */

			//$observer contains data passed from when the event was triggered.
	        //You can use this data to manipulate the order data before it's saved.
	        //Uncomment the line below to log what is contained here:
	        Mage::log($_REQUEST);

	        //Mage::log('After Admin Config Save:\n');
		}
	}
?>