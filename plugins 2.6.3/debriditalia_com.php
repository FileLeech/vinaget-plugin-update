<?php
$account = "akatsuki1412:nothingme";
//$account = trim($this->get_account('debriditalia.com'));
if (stristr($account,':')) 
	list($user, $pass) = explode(':',$account);
else 
	$cookie = $account;

if(empty($cookie)==false || ($user && $pass)){
	for ($j=0; $j < 2; $j++){
		if(!$cookie) 
			$cookie = $this->get_cookie("debriditalia.com");

		if (!$cookie) {
			//Login
			$login = 'http://debriditalia.com/login.php?u='.$user.'&p='.$pass.'&sid='.mt_rand();
			
			$data = $this->curl($login, null, "fplang=en;");

			if (strpos($data, 'Premium valid till')!==false) {
				preg_match('/user=(.*?);(.*)auth=(.*?);/s', $data, $matches);
				$cookie = ' user='.$matches[1].'; auth='.$matches[3];
				if (!empty($cookie)) {
					$cookie = 'fplang=en;' . $cookie;
					$this->save_cookies('debriditalia.com', $cookie);
				}
			}
		}

		$this->cookie = $cookie;
		if(strpos($url,"|")) {
			$linkpass = explode('|', $url); 
			$url = $linkpass[0];
			$pass = $linkpass[1];
		}

		
		$post = 'http://www.debriditalia.com/api.php?generate=&link=' . $url;
		
		$data = trim($this->curl($post,$this->cookie,null,0));
	
		if (strpos($data, 'http://www.debriditalia.com/dl/') !== false) {
			$page = $this->curl($data, null, $this->cookie, 1);
			
			if (preg_match('/ocation: *(.*)/i', $page, $redir))
				$data = trim($redir[1]);
		}
	
		if (strpos($data, 'http://') !== false && strpos($data, 'debriditalia') !== false) {
			$link = trim($data);
			$size_name = Tools_get::size_name($link, $this->cookie);
			if($size_name[0] > 200 ){
				$filesize = $size_name[0];
				$filename = $size_name[1];
				break;
			}
			else $link='';
		}
		else {
			die("<font color=red><b>Debriditalia.com > " . $data . "</b></font>");
		}
	}
}

?>