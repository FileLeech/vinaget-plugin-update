<?php
/*
== PLEASE DO NOT REMOVE MY CREDIT ==
* Vinaget Plugin: fshare.vn [Ver 3]
* Created & Fixed: LTT
* Year: 2015
* Bug Fixed: LTT [06/06/2015]
*/

if (preg_match('#^http(s)?://([a-z0-9]+\.)?fshare\.vn/#', $url)){
	$maxacc = trim(count($this->acc['fshare.vn']['accounts']));
	if($maxacc > 0){
		for ($k=0; $k < $maxacc; $k++){
			$account = $this->acc['fshare.vn']['accounts'][$k];
			if (stristr($account,':')) list($user, $pass) = explode(':',$account);
			else $cookie = $account;
			$password = "";
			if(strpos($url,"|")) {
				$linkpass = explode('|', $url); 
				$url = $linkpass[0]; $password = $linkpass[1];
			}
			if (isset($_POST['password'])) $password = $_POST['password'];
			if(empty($cookie)==false || ($user && $pass)){
				for ($j=0; $j < 2; $j++){
					if(!$cookie) $cookie = $this->get_cookie("fshare.vn");
					if(!$cookie){
						$page = $this->curl("https://www.fshare.vn/login","", "");
						$cookie = $this->GetCookies($page);
						if (preg_match('/<input type="hidden" value="(.*?)" name="fs_csrf" \/>/', $page, $value)) $fs_csrf = $value[1];
						$page = $this->curl("https://www.fshare.vn/login", $cookie,  "LoginForm[email]={$user}&LoginForm[password]={$pass}&LoginForm[rememberMe]=0&yt2=%C4%90%C4%83ng+nh%E1%BA%ADp&fs_csrf={$fs_csrf}");
						$cookie = $this->GetCookies($page);
						if (preg_match('/session_id=(.*?);/', $cookie, $match)) $cookie = "session_id=".$match[1];
						$this->save_cookies("fshare.vn",$cookie);
					}
					$this->cookie = $cookie;
					$page = $this->curl($url, $cookie, "");

					/* MULTI ACCOUNT */
					if (stristr($page,"ocation: http://www.fshare.vn/logout.php") || stristr($page, "Tài khoản của quý khách đang được sử dụng để tải xuống bởi một thiết bị khác")) { // Change New Account
						$cookie = '';
						$this->save_cookies('fshare.vn', $cookie);
						unset($link);
						continue;
					}
					/* MULTI ACCOUNT */
					
					if(stristr($page, 'name="FilePwdForm[pwd]"')) // Link Password
					{	
						if($password) {
							$cookie1 = $this->GetCookies($page);
							$post = array();
							if (preg_match('/<input type="hidden" value="(.*?)" name="fs_csrf" \/>/', $page, $value)) $fs_csrf = $value[1];
							$page = $this->curl($url, $cookie1.'; '.$this->cookie, "FilePwdForm[pwd]={$password}&fs_csrf={$fs_csrf}");
							if (stristr($page,'class="form-control pwd_input error" title="Mật khẩu không chính xác"')) die($this->lang['reportpass']);
						}
						else die($this->lang['reportpass']);
					}
					if (stristr($page,'<div class="error-code">') && stristr($page, "<div class='error-msg'>")) die(Tools_get::report($Original,"dead"));					
					elseif (preg_match('/ocation: (.*)/i', $page, $redir)) $link = trim($redir[1]); // Acc was set using Direct Link
					elseif (stristr($page, '<form id="download-form"')) { // Click button "Tai nhanh"
						if (preg_match('/<input type="hidden" value="(.*?)" name="fs_csrf"/', $page, $match)) $fs_csrf = $match[1];
						$link = ""; $k = 0;
						while ($k < 5) {
							$content = json_decode(new_curl("https://www.fshare.vn/download/get", $url, $this->cookie, "fs_csrf={$fs_csrf}&DownloadForm%5Bpwd%5D=&ajax=download-form"), true);
							if (isset($content["url"])) {
								$link = $content["url"];
								break;
							}
							$k++;
						}
					}
					elseif (stristr($page,'Tải miễn phí<br>')) {
						if($k <= $maxacc-1) {
							$cookie = '';
							$this->save_cookies("fshare.vn","");
							break;
						}
					}
					
					if($link) {
						$size_name = Tools_get::size_name($link, $this->cookie);
						$filesize = $size_name[0];
						$filename = $size_name[1];
						break;
					}
					else {
						$cookie = '';
						$this->save_cookies('fshare.vn', $cookie);
					}
				}
			}
			if (isset($link)) break;
		}
	}
}

function new_curl($url,$url_fs,$cookie,$post) {
	$ch = @curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $url_fs);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$page = curl_exec($ch);
	curl_close($ch);
	return $page;
}
/*
== PLEASE DO NOT REMOVE MY CREDIT ==
* Vinaget Plugin: fshare.vn [Ver 3]
* Created & Fixed: LTT
* Year: 2015
* Bug Fixed: LTT [06/06/2015]
*/
?>