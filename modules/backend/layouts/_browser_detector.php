<?php

/**
 * PHP CSS Browser Selector v0.0.1
 * Bastian Allgeier (http://bastian-allgeier.de)
 * http://bastian-allgeier.de/css_browser_selector
 * License: http://creativecommons.org/licenses/by/2.5/
 * Credits: This is a php port from Rafael Lima's original Javascript CSS Browser Selector: http://rafael.adm.br/css_browser_selector
 */

$ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : null;
if (!$ua) {
    return;
}

$g = 'gecko';
$w = 'webkit';
$s = 'safari';
$m = 'mobile';
$b = [];

// browser
if (!preg_match('/opera|webtv/i', $ua) && preg_match('/msie\s(\d)/', $ua, $array)) {
    $b[] = 'ie ie' . $array[1];
} elseif (strstr($ua, 'firefox/2')) {
    $b[] = $g . ' ff2';
} elseif (strstr($ua, 'firefox/3.5')) {
    $b[] = $g . ' ff3 ff3_5';
} elseif (strstr($ua, 'firefox/3')) {
    $b[] = $g . ' ff3';
} elseif (strstr($ua, 'gecko/')) {
    $b[] = $g;
} elseif (preg_match('/opera(\s|\/)(\d+)/', $ua, $array)) {
    $b[] = 'opera opera' . $array[2];
} elseif (strstr($ua, 'konqueror')) {
    $b[] = 'konqueror';
} elseif (strstr($ua, 'chrome')) {
    $b[] = $w . ' ' . $s . ' chrome';
} elseif (strstr($ua, 'iron')) {
    $b[] = $w . ' ' . $s . ' iron';
} elseif (strstr($ua, 'applewebkit/')) {
    $b[] = (preg_match('/version\/(\d+)/i', $ua, $array)) ? $w . ' ' . $s . ' ' . $s . $array[1] : $w . ' ' . $s;
} elseif (strstr($ua, 'mozilla/')) {
    $b[] = $g;
}

// platform
if (strstr($ua, 'j2me')) {
    $b[] = $m . ' j2me';
} elseif (strstr($ua, 'iphone')) {
    $b[] = $m . ' iphone';
} elseif (strstr($ua, 'ipod')) {
    $b[] = $m . ' ipod';
} elseif (strstr($ua, 'ipad')) {
    $b[] = $m . ' ipad';
} elseif (strstr($ua, 'android')) {
    $b[] = $m . ' android';
} elseif (strstr($ua, 'blackberry')) {
    $b[] = $m . ' blackberry';
} elseif (strstr($ua, 'mac')) {
    $b[] = 'mac';
} elseif (strstr($ua, 'darwin')) {
    $b[] = 'mac';
} elseif (strstr($ua, 'webtv')) {
    $b[] = 'webtv';
} elseif (strstr($ua, 'win')) {
    $b[] = 'win';
} elseif (strstr($ua, 'freebsd')) {
    $b[] = 'freebsd';
} elseif (strstr($ua, 'x11') || strstr($ua, 'linux')) {
    $b[] = 'linux';
}

echo join(' ', $b);
