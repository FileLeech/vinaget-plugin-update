<?php
$account = "akatsuki1412:nothingme";
//$account = trim($this->get_account('tb7.pl'));
if (stristr($account,':')) list($user, $pass) = explode(':',$account);
else $cookie = $account;
if(empty($cookie)==false || ($user && $pass)){
	for ($j=0; $j < 2; $j++){
		if(!$cookie) $cookie = $this->get_cookie("tb7.pl");
		if(!$cookie){
			$data = $this->curl("http://tb7.pl/login","","login={$user}&password={$pass}");
			if(strpos=="Hasło jest nieprawidłowe") die("Account banned");
			$cookie = $this->GetCookies($data);
			$this->save_cookies("tb7.pl",$cookie);
		}
		$this->cookie = $cookie;
		$this->curl("http://tb7.pl/mojekonto/sciagaj",$cookie,"step=1&content=".urlencode($url));
		$file = $this->curl("http://tb7.pl/mojekonto/sciagaj",$cookie,"step=2&0=on");
		preg_match('/<a href="(.*)" target="_blank">Pobierz<\/a>/', $file, $matches);
		$fileurl = trim($matches[1]);
		//print_r($fileurl);
		$origanl=$this->curl($fileurl,"","");
		if(preg_match('/ocation: *(.*)/i', $origanl, $redir)) 
		$url1 = trim($redir[1]);
		$a=$this->curl($url1,"","");
		if(preg_match('/ocation: *(.*)/i', $origanl, $redir)){
			//print_r($redir);
			$link = trim($redir[1]);
			$size_name = Tools_get::size_name($fileurl, $this->cookie);
			preg_match('/(([0-9]\,?\.?)+\s(K|M|G)B)/i', $file, $matches);
			$filesize = Tools_get::convert($matches[1]);
			$filename = $size_name[1];
			$this->curl("http://tb7.pl/mojekonto/pliki",$cookie,"action=delete&files%5B%5D=".urlencode($fileurl));
			echo json_encode(array("url" => urlencode($link), "filename" => urlencode($filename), "filesize" => $filesize, "msg" => "success"));
			exit;
			//break;
		}
		else {
			$cookie = "";
			$this->save_cookies("tb7.pl","");
		}
	}
}


/*
* Script Name: Vinaget 
* Version: 2.6.3
* Created: Amanat
*/
?>