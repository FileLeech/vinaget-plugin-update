<?php
if (preg_match('#^(http|https)\:\/\/(www\.)?depositfiles\.com/#', $url)){
	$maxacc = count($this->acc['depositfiles.com']['accounts']);
	if($maxacc > 0){
		$password = "";
		if(strpos($url,"|")) {
			$linkpass = explode('|', $url); 
			$url = $linkpass[0]; $password = $linkpass[1];
		}
		if (isset($_POST['password'])) $password = $_POST['password'];
		if($password) $post = "file_password=".$password;
		else $post = "";
		for ($k=0; $k < $maxacc; $k++){
			$account = $this->acc['depositfiles.com']['accounts'][$k];
			if (stristr($account,':')) list($user, $pass) = explode(':',$account);
			else $cookie = $account;
			if(empty($cookie)==false || ($user && $pass)){
				$page = $this->curl($url,"","");
				if (stristr($page, "HTTP/1.1 302 Moved Temporarily")) {
					if (preg_match('/ocation: (.*)/', $page, $match)) $url = trim($match[1]);
				}
				for ($j=0; $j < 2; $j++){
					if(!$cookie) $cookie = $this->get_cookie("depositfiles.com");
					if(!$cookie){
						$page = $this->curl("http://depositfiles.com/login.php?return=%2F","lang_current=en","go=1&login=$user&password=$pass");
						$cookie = $this->GetCookies($page)."; lang_current=en;";
						$this->save_cookies("depositfiles.com",$cookie);
					}
					
					$this->cookie = $cookie;
					
					$page = $this->curl($url,$cookie,$post);
					if(stristr($page, "You have exceeded the")){
						if($k <= $maxacc-1) {
							$cookie = '';
							$this->save_cookies("depositfiles.com","");
							break;
						}
						else die("<font color='red'><b>All accounts was out of bandwidth</b></font>");
					}
					elseif(strpos($page,'Please, enter the password for this file')) die(Tools_get::report($Original,"password_required"));
					elseif (preg_match('/ocation: (.*)/i', $page, $redir))$link = trim($redir[1]);
					elseif (preg_match('/<a href="(.*?)" onClick=/', $page, $redir2)) $link = trim($redir2[1]);
					elseif(stristr($page, "his file does not exist")) die(Tools_get::report($Original,"dead"));
					
					if($link){
						$size_name = Tools_get::size_name($link, $this->cookie);
						if ($size_name[0] <> -1 && empty($size_name[0]) == false) {
							$filesize = $size_name[0];
							$filename = $size_name[1];
							break;
						}
						else {
							$link = "";
							$cookie = ""; 
							$this->save_cookies("depositfiles.com","");
						}
					}
					else {
						$link = "";
						$cookie = ""; 
						$this->save_cookies("depositfiles.com","");
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