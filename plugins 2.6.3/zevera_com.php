<?php
$account = trim($this->get_account('zevera.com'));
if (stristr($account,':')) list($user, $pass) = explode(':',$account);
else $cookie = $account;
$maxacc = count($this->acc['zevera.com']['accounts']);
if($maxacc > 0){
	for ($k=0; $k < $maxacc; $k++){
		$account = trim($this->acc['zevera.com']['accounts'][$k]);
		if (stristr($account,':')) list($user, $pass) = explode(':',$account);
		for ($j=0; $j < 2; $j++){
			if(!$cookie) $cookie = $this->get_cookie("zevera.com");
			if(!$cookie){
				$data = $this->curl("http://www.zevera.com/offerlogin/?login={$user}&pass={$pass}", "otmData=languagePref=US", "");
				$cookie = $this->GetCookies($data);
				if (preg_match('/.ASPNETAUTH=(.*)/', $cookie, $match)) $cookie1 = ".ASPNETAUTH=".trim($match[1])."; ";
				$this->save_cookies("zevera.com",$cookie);
			}
			$this->cookie = $cookie;
			$data = $this->curl("http://www.zevera.com/Members/download.ashx?ourl=".str_replace(array("http://","https://"),"",$url), $cookie, "");

			if (preg_match('/ocation: (.*)/i', $data, $match)) {
				if (!stristr($match[1], "/members/systemmessage.aspx")) {
					$link = trim($match[1]);
					$size_name = Tools_get::size_name($link, "");
					if ($size_name[0] == -1) {
						$link = "";
						$cookie = "";
						$this->save_cookies("zevera.com","");
					}
					else {
						$filesize =  $size_name[0];
						$filename = $size_name[1];
					}
				}
				else {
					$link = "";
					$cookie = "";
					$this->save_cookies("zevera.com","");
				}
			}
			else {
				$link = "";
				$cookie = "";
				$this->save_cookies("zevera.com","");
			}
		}
		
		if($link) break;
	}
}

/*
* PLugin Name: Zevera.com
* Script Name: Vinaget 
* Version: 2.6.3
* Created: ..:: LTT ::..
* Year: 2015
*/
?>