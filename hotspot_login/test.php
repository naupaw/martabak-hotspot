<pre>
<?php
// Basic Testing...

require 'core.php';

$hotspot = new Ma_spot;

//get ALL Device status where connected to router
print_r($hotspot->get_all_status());

//Get Hotspot Status (Not Router Status)
print_r($hotspot->hotspot_status());

//Get My Status
print_r($hotspot->my_status());

/**
 * Execute Function
 **/

// Switch Hotspot Turn ON or OFF
// $hotspot->switch_hotspot();

// Add Host to hotspot by Ip Address
// $hotspot->register_host($ip);

// Remove Host from hotspot by Ip Address
// $hotspot->remove_host($ip);


/* For Now only ipv4 supported */
?>
</pre>