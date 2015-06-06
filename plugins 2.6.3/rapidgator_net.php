<?php
if (preg_match('#^http:\/\/(www.)?rapidgator\.net/#', $url)) {
	$account = trim($this->get_account('rapidgator.net'));
	if (stristr($account,':')) list($user, $pass) = explode(':',$account);
	else $cookie = $account;
	$maxacc = count($this->acc['rapidgator.net']['accounts']);
	if($maxacc > 0){
		for ($k=0; $k < $maxacc; $k++){
			$account = trim($this->acc['rapidgator.net']['accounts'][$k]);
			if (stristr($account,':')) list($user, $pass) = explode(':',$account);
			for ($j=0; $j < 2; $j++){
				if(!$cookie) $cookie = $this->get_cookie("rapidgator.net");
				if(!$cookie){
					$data = $this->curl("https://rapidgator.net/auth/login","","LoginForm[email]=$user&LoginForm[password]=$pass&LoginForm[rememberMe]=1");
					$cookie = $this->GetCookies($data);
					$this->save_cookies("rapidgator.net",$cookie);
				}
				$this->cookie = $cookie;
				$data = $this->curl($url,$cookie.';lang=en',"");
				
				if (stristr($data,'Account:&nbsp;<a href="/article/premium">Free</a>')) {
					$cookie = '';
					$this->save_cookies("rapidgator.net","");
					continue;
				}
				
				if (stristr($data, "You Are Almost Out of Bandwidth!") || stristr($data, "You have reached quota of downloaded information")) {
					if($k < $maxacc-1) {
						$cookie = '';
						$this->save_cookies("rapidgator.net","");
						break;
					}
					else die("<font color='red'><b>All accounts was out of bandwidth</b></font>");
				}
				
				if (stristr($data,'File not found')) die(Tools_get::report($Original,"dead"));
				elseif (preg_match("%var premium_download_link = '(.*)';%U", $data, $matches)) $link = trim($matches[1]);
				elseif (preg_match ( '/ocation: (.*)/', $data, $linkpre)) $link = trim ($linkpre[1]);
				
				if($link) {
					$size_name = Tools_get::size_name($link, $this->cookie);
					if ($size_name[0] <> -1 && empty($size_name[0]) == false) {
						$filesize = $size_name[0];
						$filename = $size_name[1];
						break;
					}
					else {
						$link = "";
						$cookie = "";
						$this->save_cookies("rapidgator.net","");
					}
				}
				else {
					$link = "";
					$cookie = "";
					$this->save_cookies("rapidgator.net","");
				}
			}
			
			if($link) break;
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