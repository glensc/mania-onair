<?php

$url = 'http://www.mania.ee/eeter.php';
require_once 'UrlUtil.php';
$content = UrlUtil::HttpRequest($url);
if ($content === false) {
	die('Error fetching');
}

// <font color='ffffff'><b>THE SUN</b>,<br>Hingede müüja  (2012)<br>(16:13)</font></body></html>
// json_encode() requires UTF8, and so our webpage
$content = iconv('latin1', 'utf8', $content);

if (preg_match('#<font[^>]+><b>(?P<artist>.+)</b>,<br>(?P<title>.+)<br>\((?P<time>[\d:]+)\)</font>#U', $content, $m)) {
	$res = array();
	foreach ($m as $key => $value) {
		if (!is_numeric($key)) {
			$res[$key] = $value;
		}
	}
	$ret = json_encode($res);

	$callback = isset($_REQUEST['callback']) ? trim($_REQUEST['callback']) : null;

	if ($callback) {
		header("Content-type: text/javascript");
		echo $callback, '(', $ret, ');';
	} else {
		header('Content-type: application/json');
		echo $ret;
	}

} else {
	echo $content;
}
