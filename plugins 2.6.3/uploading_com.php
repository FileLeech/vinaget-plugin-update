<?php
if (preg_match('#^http://([a-z0-9]+)\.uploading\.com/#', $url) || preg_match('#^http://uploading\.com/#', $url)){
	$maxacc = count($this->acc['uploading.com']['accounts']);
	if($maxacc > 0){
		$password = "";
		if(strpos($url,"|")) {
			$linkpass = explode('|', $url); 
			$url = $linkpass[0]; $password = $linkpass[1];
		}
		if (isset($_POST['password'])) $password = $_POST['password'];
		for ($k=0; $k < $maxacc; $k++){
			$account = $this->acc['uploading.com']['accounts'][$k];
			if (stristr($account,':')) list($user, $pass) = explode(':',$account);
			else $cookie = $account;
			if(empty($cookie)==false || ($user && $pass)){
				for ($j=0; $j < 2; $j++){
					if(!$cookie) $cookie = $this->get_cookie("uploading.com");
					if(!$cookie){
						$page=$this->curl("http://uploading.com/general/login_form/?ajax","","email={$user}&password={$pass}");
						$cookie =  $this->GetCookies($page);
						$this->save_cookies("uploading.com", $cookie);
					}
					$this->cookie = $cookie;
					$page=$this->curl($url,$cookie,"");
					if (stristr($page,'file not found')) die(Tools_get::report($Original,"dead"));
					if(preg_match('#ocation: (.+)?\r\n#U', $page, $match)) $link = trim($match[1]);
					elseif (strstr($page,"Your account premium traffic has been limited")) {
						if($k <= $maxacc-1) {
							$cookie = '';
							$this->save_cookies("uploading.com","");
							continue;
						}
						else die("<font color=red>All accounts were out of bandwidth</font>");
					}
					else {
						$array = array();
						if (preg_match('/code: "(.*)",/i', $page, $match)) $code = trim($match[1]);
						$array["action"] = "get_link";
						$array["code"] = $code;
						if (empty($password) == true) $array["pass"] = false;
						else $array["pass"] = $password;
						$array["force_exe"] = 1;
						$ch = @curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://uploading.com/files/get/?ajax");
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));
						curl_setopt($ch, CURLOPT_HEADER, FALSE);
						curl_setopt($ch, CURLOPT_COOKIE, $cookie);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
						$page = curl_exec($ch);
						curl_close($ch);
						$content = @json_decode($page, true);
						$link = $content["answer"]["link"];
					}
					
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
							$this->save_cookies("uploading.com","");
						}
					}
					else {
						$link = "";
						$cookie = "";
						$this->save_cookies("uploading.com","");
					}
				}
				if($link) break;
			}
		}
	}
}

?>