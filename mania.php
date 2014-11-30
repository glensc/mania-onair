<?php
/**
 * Radiomania Now-Playing app for Chrome
 * Copyright (C) 2013-2014 Elan Ruusamäe <glen@alkohol.ee>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$url = 'http://www.mania.ee/eeter.php';
require_once 'UrlUtil.php';
$content = UrlUtil::HttpRequest($url, array(), UrlUtil::HTTP_METHOD_GET, array(CURLOPT_TIMEOUT => 5));
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
