<?php
/**
 * Little Martabak Hotspot System
 * @author Martabak angus.
 **/

Class Ma_spot
{
	private $name = "Martabak Hotspot"; 			//name your hotspot
	private $router_ip = "192.168.1"; 				//My Router IP (also make a hotspot login)
	private $redirect_port = "2082";				//Redirect Port Default is 80
	private $login_url = "/hotspot_login/";			//Locate your Hotspot URL

	public function __construct()
	{
		#do...
	}

	public function base_url()
	{
		return 'http://'.$this->router_ip.$this->login_url;
	}

	public function hotspot_name()
	{
		return $this->name;
	}

	private function get_arp()
	{
		return shell_exec("cat /proc/net/arp");
	}

	/**
	 * Get list
	 * 'ip' or 'mac'
	 * @return array
	 **/

	private function get_list($src, $type = "ip")
	{
		switch ($type) {
			case 'ip':
				preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $src ,$hasil);
				break;
			
			case 'mac':
				preg_match_all('/..:..:..:..:..:../', $src ,$hasil);
				break;
			default:
				$hasil[0] = false;
				var_dump("not found");
				break;
		}
		return $hasil[0];
	}

	/**
	 * Get Reserved IP by iptables rule
	 **/

	private function reserved_ip()
	{
		//Call Iptables command

		$iptables = shell_exec("iptables -t nat -L PREROUTING -vn --line-number | grep tcp");

		foreach (explode("\n", $iptables) as $key => $data) {
			if(explode("ACCEPT", $data)[1])
			{
				preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $data ,$iptabe);

				$ft = explode(".0", $iptabe[0][0]);

				if(!isset($ft[1]))
				{
					$iptb[] = $iptabe[0][0];
				}
			}
		}

		return $iptb;
	}

	/**
	 * Get All Status of client connected to router
	 **/

	public function get_all_status()
	{

		$client['my_ip'] = $_SERVER['REMOTE_ADDR'];
		foreach ($this->get_list($this->get_arp(), 'ip') as $key => $data) {

			if(@in_array($data, $this->reserved_ip()))
			{
				$got = true;
			}
			else
			{
				$got = false;
			}

			$client['all'][] = array(
							'ip_addr' => $data,
							'mac_addr' => $this->get_list($this->get_arp(), 'mac')[$key],
							'access' => $got,
							'my_ip' => ($data == $_SERVER['REMOTE_ADDR'] ? true : false)
							);
		}
		return $client;
	}

	public function hotspot_status()
	{
		$iptables = shell_exec("iptables -t nat -L PREROUTING -vn --line-number | grep DNAT");

		if(explode("tcp dpts", $iptables)[1] && explode("to:".$this->router_ip.":".$this->redirect_port, $iptables)[1])
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public function switch_hotspot()
	{
		if($this->hotspot_status())
		{
			exec("iptables -t nat -D PREROUTING -s 10.25.5.0/255.255.255.0 -p tcp --dport 1:1000 -j DNAT --to-destination ".$this->router_ip.":".$this->redirect_port);
		}
		else
		{
			exec("iptables -t nat -A PREROUTING -s 10.25.5.0/255.255.255.0 -p tcp --dport 1:1000 -j DNAT --to-destination ".$this->router_ip.":".$this->redirect_port);
		}
		return true;
	}

	public function register_host($ip)
	{
		exec("iptables -t nat -I PREROUTING -s ".$ip." -p tcp --dport 1:1000 -j ACCEPT");
	}

	public function remove_host($ip)
	{
		exec("iptables -t nat -D PREROUTING -s ".$ip." -p tcp --dport 1:1000 -j ACCEPT");
	}

	public function my_status()
	{
		$my_ip = $_SERVER['REMOTE_ADDR'];

		if(@in_array($my_ip, $this->reserved_ip()))
		{
			$stat = "Grant";
			$tp = true;
		}
		else
		{
			$stat = "Denied";
			$tp = false;
		}

		foreach ($this->get_all_status()['all'] as $key => $data) {
			if($data['my_ip'] == true)
			{
				return $data;
			}
		}

	}

	public function make_redirect()
	{
		#if($_SERVER['SERVER_NAME'] !== $this->router_ip)
		if($_GET['redir'] == 1)
		{
			return "<script type=\"text/javascript\">document.write('<meta http-equiv=\"refresh\" content=\"1; url=".$this->base_url()."?next='+encodeURIComponent(window.location.href)+'\">');</script>";
		}
	}

	public function redirect_main($param = null)
	{

		if($param != null)
		{
			$param = htmlspecialchars($param);
		}

		return '<meta http-equiv="refresh" content="2; url='.$this->base_url().'">';
	}

	function cron_action()
	{
		foreach ($this->reserved_ip() as $data) {

			if(in_array($data, $this->get_list($this->get_arp(), 'ip')))
			{
				$ret[] = array('status' => 1, 'ip' => $data);
			}
			else
			{
				$ret[] = array('status' => 0, 'ip' => $data);
			}

		}

		return $ret;
	}
}