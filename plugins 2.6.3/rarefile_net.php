<?php
if (preg_match('#^http://([a-z0-9]+)\.rarefile\.net/#', $url) || preg_match('#^http://rarefile\.net/#', $url)){
	$maxacc = count($this->acc['rarefile.net']['accounts']);
	if($maxacc > 0){
		for ($k=0; $k < $maxacc; $k++){
			$account = trim($this->acc['rarefile.net']['accounts'][$k]);
			if (stristr($account,':')) list($user, $pass) = explode(':',$account);
			else $cookie = $account;
			if(empty($cookie)==false || ($user && $pass)){
				for ($j=0; $j < 2; $j++){
					if(!$cookie) $cookie = $this->get_cookie("rarefile.net");
					if(!$cookie){
						$data = $this->curl("http://www.rarefile.net/", "lang=english", "login={$user}&password={$pass}&op=login&redirect=http://www.rarefile.net/");
						$cookie = "lang=english; ".$this->GetCookies($data);
						$this->save_cookies("rarefile.net",$cookie);
					}
					$this->cookie = $cookie;
					$data = $this->curl($url,$cookie,"");
					
					if(preg_match('/ocation: *(.*)/i', $data, $redir)) $link = trim($redir[1]);
					elseif (stristr($data,"<h2>File Not Found</h2>")) die(Tools_get::report($Original,"dead"));
					elseif(stristr($data,'The file was deleted by its owner')) die(Tools_get::report($Original,"dead"));
					else {
						$post = array();
						if (preg_match('/<input type="hidden" name="op" value="(.*)">/i', $data, $redir2)) $post["op"] = $redir2[1];
						if (preg_match('/<input type="hidden" name="id" value="(.*)">/i', $data, $redir2)) $post["id"] = $redir2[1];
						if (preg_match('/<input type="hidden" name="rand" value="(.*)">/i', $data, $redir2)) $post["rand"] = $redir2[1];
						$post["referer"] = "";
						if (preg_match('/<input type="hidden" name="method_free" value="(.*)">/i', $data, $redir2)) $post["method_free"] = $redir2[1];
						if (preg_match('/<input type="hidden" name="method_premium" value="(.*)">/i', $data, $redir2)) $post["method_premium"] = $redir2[1];
						if (preg_match('/<input type="hidden" name="down_direct" value="(.*)">/i', $data, $redir2)) $post["down_direct"] = $redir2[1];
						
						if (stristr($data,'<input type="password" name="password" class="myForm">')) {
							if (empty($password) == false) {
								$post["password"] = $password;
							}
							else die($this->lang['reportpass']);
						}
						
						$data = $this->curl($url, $cookie, $post);
						if (stristr($data, 'Wrong password')) die($this->lang['reportpass']);		
						elseif (preg_match('/ocation: (.*)/i',$data,$match)) $link = trim($match[1]);
						elseif (preg_match('/<span style="background:#f9f9f9;border:1px dotted #bbb;padding:7px;">\n<a href="(.*?)">/i', $data, $match)) $link = trim($match[1]);
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
							$this->save_cookies("rarefile.net","");
						}
					}
					else {
						$link = "";
						$cookie = "";
						$this->save_cookies("rarefile.net","");
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