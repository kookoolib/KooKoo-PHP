<?php

//This is the number which the web site customer entered.
$number=$_REQUEST['number'];
//This is the number which will be connected to the customer. Replace this with your number
$dial_to='04034653456';
//replace this with your KooKoo API key
$api_key='KKKKKKKKKKKKKKKKKKKKKKKKKKKK';
$click2call = curl_init();
$url = "http://www.kookoo.in/outbound/outbound.php?phone_no=0".$number."&api_key=".$api_key."&extra_data=<response><dial>".$dial_to."</dial></response>";
curl_setopt($click2call, CURLOPT_URL, $url);
curl_setopt($click2call, CURLOPT_RETURNTRANSFER, true);
//execute post
$result = curl_exec($click2call);
$arr=array("html"=>"<font color='#00CC99'>Queued</font>");
header('Content-type: text/javascript');
$jsonp = $_REQUEST['callback']."(".json_encode($arr).")";
echo $jsonp;
//You can add you own error checking or storing in database etc here.
curl_close ($click2call);
?>
