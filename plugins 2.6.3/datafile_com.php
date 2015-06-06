<?php
if (preg_match('#^https?://([a-z0-9]+)\.datafile\.com/#', $url)){
	$maxacc = count($this->acc['datafile.com']['accounts']);
	if($maxacc > 0){
		for ($k=0; $k < $maxacc; $k++){
			$account = trim($this->acc['datafile.com']['accounts'][$k]);
			if (stristr($account,':')) list($user, $pass) = explode(':',$account);
			else $cookie = $account;
			$url = preg_replace("/^https:\/\/www.datafile.com/i","http://www.datafile.com",$url);
			if(empty($cookie)==false || ($user && $pass)){
				for ($j=0; $j < 2; $j++){
					if(!$cookie) $cookie = $this->get_cookie("datafile.com");
					if(!$cookie){
						$data = $this->curl("https://www.datafile.com/login.html","lang=en", "login={$user}&password={$pass}&remember_me=1");
						$cookie = $this->GetCookies($data);
						$this->save_cookies("datafile.com",$cookie);
					}
					$this->cookie = $cookie;
					$data = $this->curl($url, $cookie, "");
					if(stristr($data, 'ErrorCode 0: Invalid Link')) die(Tools_get::report($Original,"dead"));
					elseif(preg_match('/ocation: *(.*)/i', $data, $redir)) {
						$link2 = "http://www.datafile.com".trim($redir[1]);
						$data = $this->curl($link2, $this->cookie, "");
						if(preg_match('/ocation: *(.*)/i', $data, $match)) $link = trim($match[1]);	
						elseif (stristr($data, "ErrorCode 6: Download limit in")) {
							if($k <= $maxacc-1) {
								$cookie = '';
								$this->save_cookies("datafile.com","");
								break;
							}
							else die("<font color=red>All accounts were out of bandwidth.</font>");
						}
					}
					if($link) {
						$size_name = Tools_get::size_name($link, $this->cookie);
						if ($size_name[0] <> -1 && empty($size_name[0]) == false) {
							$filesize = $size_name[0];
							if ($array = explode(";", $size_name[1])) $filename = $array[0];
							else $filename = $size_name[1];
							break;
						}
						else {
							$link = "";
							$cookie = "";
							$this->save_cookies("datafile.com","");
						}
					}
					else {
						$link = "";
						$cookie = "";
						$this->save_cookies("datafile.com","");
					}
				}
				if($link) break;
			}
		}
	}
}

/*
* Home page: http://vinaget.us
* Blog:	http://blog.vinaget.us
* Script Name: Vinaget 
* Version: 2.6.3
* Created: ..:: [H] ::..
*/
?>