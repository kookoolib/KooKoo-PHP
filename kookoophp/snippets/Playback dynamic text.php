<?php
//Description:Sample Code to playback a dynamic text
session_start();
require_once("response.php");//response.php is the kookoo xml preparation class file
$r = new Response();
$languages=array("Press 1 for English","Press 2 for Hindi","Press 3 for Kannada");
//Declaration of array consisting three values
if($_REQUEST['event']== "NewCall" ) 
{
       foreach($languages as $key  => $value)
	     {
			     $r->addPlayText($value);
	     }
}
$r->send();
?> 