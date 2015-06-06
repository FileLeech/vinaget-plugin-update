<?php
if (preg_match('#^http://(www\.)?ryushare\.com/#', $url)){
	$account = trim($this->get_account('ryushare.com'));
	if (stristr($account,':')) list($user, $pass) = explode(':',$account);
	else $cookie = $account;
	if(empty($cookie)==false || ($user && $pass)){
		for ($j=0; $j < 2; $j++){
			if(!$cookie) $cookie = $this->get_cookie("ryushare.com");
			if(!$cookie){
				$data = $this->curl("http://ryushare.com/","lang=english","op=login&redirect=http%3A%2F%2Fryushare.com%2F&login=$user&password=$pass&loginFormSubmit=Login");
				$cookie = $this->GetCookies($data);
				$this->save_cookies("ryushare.com",$cookie);
			}
			$data =  $this->curl($url,$cookie.';lang=english;',"");

			if (stristr($data,'<b>File Not Found</b>') || stristr($data, "The file was removed by administrator")) die(Tools_get::report($Original,"dead"));
			elseif(preg_match('/ocation: *(.*)/i', $data, $redir)){
				$link = str_replace(" ","%20",trim($redir[1]));
				$size_name = Tools_get::size_name($link, $this->cookie);
				$filesize = $size_name[0];
				$filename = $size_name[1];
				break;
			}
			
			$data1 = trim ($this->cut_str ($data, '<form', "/form>"));
			
			if(preg_match_all('/input type="hidden" name="(.*?)" value="(.*?)"/i', $data1, $value)) {
				$this->cookie = $cookie.';'.$this->GetCookies($data);
				$max =count($value[1]);
				$post = "";
				for ($k=0; $k < $max; $k++){
					$post .= $value[1][$k].'='.$value[2][$k].'&';
				}
				$data =  $this->curl($url,$cookie.';lang=english;',$post);
				if(preg_match('%<a href="(.*?)">Click here to download</a>%U', $data, $matches)) {
					$link = str_replace(" ","%20",trim($matches[1]));
					$size_name = Tools_get::size_name($link, $this->cookie);
					$filesize = $size_name[0];
					$filename = $size_name[1];
					break;
				}
			}
			else {
				$cookie = "";
				$this->save_cookies("ryushare.com","");
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