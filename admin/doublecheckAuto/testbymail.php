<?php
header('Content-type: text/html; charset=utf-8');
$t = 0;
$time = time() + 300;
while (time() < $time) {
    if ($t < time()) {
        echo date("h:i:sa")."<br>";
        ob_flush();
        flush();
    }
    $t = time();
}
?>