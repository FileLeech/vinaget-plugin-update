<?php
$account = "uid=ad422e52552cc9d45b14ded4";
if (stristr($account,':')) list($user, $pass) = explode(':',$account);
else $cookie = $account;
if(empty($cookie)==false || ($user && $pass)){
	for ($j=0; $j < 2; $j++){
		if(!$cookie) $cookie = $this->get_cookie("alldebrid.com");
		if(!$cookie){
			$data = $this->curl("http://www.alldebrid.com/register/?action=login&returnpage=&login_login=".urlencode($user)."&login_password=".urlencode($pass),"","login_login=".urlencode($user)."&login_password=".urlencode($pass));
			if(preg_match("%uid=(.*);%U", $data, $matches)) {
				$cookie = $matches[1];
				$this->save_cookies("alldebrid.com",$cookie);
			}
		}
		$cookie = preg_replace("/(UID=|uid=|Uid=)/","",$cookie);
		$this->cookie = "uid=".$cookie;
		if(strpos($url,"|")) {
			$linkpass = explode('|', $url); 
			$url = $linkpass[0]; $pass = $linkpass[1];
		}
		$data = $this->curl("http://www.alldebrid.com/service.php?link=".urlencode($url)."&nb=0&json=true&pw=$pass","uid=".$cookie,"",0);
		$page = json_decode($data, true);
		if (stristr($data,"disable for trial account")) $report = Tools_get::report($url,"disabletrial");
		elseif (stristr($data,"Ip not allowed")) die("<font color=red><b>Ip host have been banned by alldebird.com !</b></font>");
		elseif(isset($page['error']) && empty($page['error'])==false) {
			if ($page['error'] == "premium") {
				$cookie = "";
				$this->save_cookies("alldebrid.com","");
				continue;
			}
			die('<font color=red><b>'.$page['error'].'</b></font>');
		}
		elseif(isset($page['error']) && $page['error']==false) {
			$filename = $page['filename'];
			$link = $page['link'];
			$link = str_replace("https","http",$link) ;
			$size_name = Tools_get::size_name($link, $this->cookie);
			if($size_name[0] > 200 ) $filesize = $size_name[0];
			else $link='';
			break;
		}
		else {
			$cookie = "";
			$this->save_cookies("alldebrid.com","");
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