<?php
date_default_timezone_set('Asia/Jakarta');
require 'core.php';

$action = new Ma_spot;

foreach ($action->cron_action() as $data) {
	
	if($data['status'] == 1)
	{
		#no changes...
	}
	else
	{
		exec("iptables -t nat -D PREROUTING -s ".$data['ip']." -p tcp --dport 1:1000 -j ACCEPT");
		var_dump("[".date("Y-m-d h:i:s")."] Removed ".$data['ip']." From hotspot login");
	}

}

?>