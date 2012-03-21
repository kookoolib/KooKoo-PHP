<?php
/*Description:Using a record tag you get below request parameters from kookoo in response. * event =Record * data =Recorded file_URL * status =Record Status*/
session_start();
//start session, session will be maintained for entire call
require_once("response.php");//response.php is the kookoo xml preparation class file
$r = new Response();
if($_REQUEST['event']== "NewCall" ) 
{
    $r->addPlayText('Please record your message after beep ');
	//give unique file name for each recording
	$r->addRecord('filename2','wav','120');
    $_SESSION['next_goto'] = 'Record_Status';
}
else if($_REQUEST['event'] == 'Record' && $_SESSION['next_goto'] == 'Record_Status' )
{
//recorded file will be come as  url in request parameter called data
	 $r->addPlayText('your recorded audio is ');
	 //You can also store the URL in your database if you want
	 $_SESSION['record_url']=$_REQUEST['data'];
	 $r->addPlayAudio($_SESSION['record_url']);
	 $r->addPlayText('Thank you for calling, have a nice day');
	 $r->addHangup();	
}
$r->send();
?> 