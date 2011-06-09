<?php

require_once("response.php");
session_start();

$r = new Response();

if($_REQUEST['event']=="NewCall") 
{
	$cd = new CollectDtmf();
	$cd->setTermChar("#");
	$cd->setTimeOut("4000");
	$cd->addPlayText("welcome to adder.Please enter your first number.Terminate with hash");
	$r->addCollectDtmf($cd);
	$_SESSION['state'] = "firstNumber";
	$r->send();

} 
else if ($_REQUEST['event']=="GotDTMF" && $_SESSION['state'] == "firstNumber")
{
	$first = $_REQUEST['data'];

	$cd = new CollectDtmf();
	$cd->setTermChar("#");
	$cd->setTimeOut("4000");
	$cd->addPlayText("Please enter the Second number.Terminate with hash");
	$_SESSION['firstNumber']=$first;
	$_SESSION['state'] = "secondNumber";
	$r->addCollectDtmf($cd);
	$r->send();
} 
else if ($_REQUEST['event']=="GotDTMF" && $_SESSION['state'] == "secondNumber") 
{
	$second = $_REQUEST['data'];
	$total = $_SESSION['firstNumber']+$second;
	$r->addPlayText("your total is $total");
	$r->addHangup();
	$r->send();
}
?>
