<?php
if (preg_match('#^http://(up\.|www\.)?4share\.vn/#', $url)){
	$account = trim($this->get_account('4share.vn'));
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
			if(!$cookie) $cookie = $this->get_cookie("4share.vn");
			if(!$cookie) {
				$data = $this->curl('http://4share.vn/default/index/login', '', 'username='.$user.'&password='.$pass);
				$cookie = $this->GetCookies($data);
				$this->save_cookies('4share.vn', $cookie);
			}
			
			$this->cookie = $cookie;
			$data = $this->curl($url, $cookie, '');

			if (stristr($data,"File not found")) {
				$this->error("$url <br> The requested file is not found");
				exit;
			}
			elseif (stristr($data,"FID Không hợp lệ!")) {
				$this->error("$url <br> The requested file was deleted");
				exit;
			}
			elseif (stristr($data,'File này có password, bạn nãy nhập password để download:')) {
				if (!$password) die($this->lang['reportpass']);
				else {
					$post["password_download_input"] = $password;
					$post["submit"] = "Download with password";
					$data = $this->curl($url, $cookie, $post);
					if (stristr($data,'Password download không đúng!')) die($this->lang['reportpass']);
					elseif (preg_match('/ocation: (.*)/i', $data, $redir)) $link = trim($redir[1]);
				}
			}
			if (!$link) {
				if (preg_match("/ocation: (.*)/i", $data, $matches)) $link = trim($matches[1]);
				else {
					preg_match("/FileDownload:.*?<br\/>.*?<a href='(.*?)'>/i", $data, $value);
					$link = $value[1];
				}
			}
			if ($link!="") {
				$size_name = Tools_get::size_name($link, $this->cookie);
				$filesize = $size_name[0];
				$filename = $size_name[1];
				break;
				}
			else 
			{
				$link = '';
				$cookie='';
				$this->save_cookies('4share.vn', $cookie);
			}
		}
	}
}
?>