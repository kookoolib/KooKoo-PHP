<?php
//Description:Sample Code Collecting mobile number and dialing out
session_start();
require_once("response.php");//response.php is the kookoo xml preparation class file
$r = new Response();

if($_REQUEST['event']== "NewCall" ||$_SESSION['next_goto']=="Menu1")
{
	
	$collectInput = New CollectDtmf();
	$collectInput->addPlayText('please enter the number that you want to dial followed by hash, if it is s t d number, enter 0 as pre fix ',3);
	$collectInput->setMaxDigits('15'); //max inputs to be allowed
	$collectInput->setTimeOut('4000');  //maxtimeout if caller not give any inputs
	$collectInput->setTermChar('#');  
	$r->addCollectDtmf($collectInput);
    $_SESSION['next_goto']='Menu1_CheckInput1';
	
}
else if($_REQUEST['event'] == 'GotDTMF' && $_SESSION['next_goto'] == 'Menu1_CheckInput1' )
{
//input will come in data param
//print print parameter data value
 if($_REQUEST['data'] == '')
     { //if value null, caller has not given any dtmf
	//no input handled
	 $r->addPlayText('you have not entered any input');
	 $_SESSION['next_goto']='Menu1';
     }
     else 
	 {
     $_SESSION['dial'] = $_REQUEST['data'];
     $r->addPlayText('please wait while we transfer your call to out customer care');
	 $r->addDial($_SESSION['dial'],'true',1000,30,'ring');
	 $_SESSION['next_goto'] = 'Dial1_Status';
     }
}
else if($_REQUEST['event'] == 'Dial' && $_SESSION['next_goto'] == 'Dial1_Status' )
{
//dial url will come in data param  //if dial record is false then data value will be -1 or null
//dial status will come in status (answered/not_answered) param

 	 $_SESSION['dial_record_url']=$_REQUEST['data'];
	 $_SESSION['dial_status']=$_REQUEST['status'];
	 $_SESSION['dial_callduration']=$_REQUEST['callduration'];
	 if($_REQUEST['status'] == 'not_answered'){
	//if you would like to dial another number, if first call not answered,
	 $r->addDial("99999999",'true',1000,30,'default');
	 }
     else
     {
	 	 $r->addPlayText('Thank you for calling, ');
	 	 $r->addHangup();	// do something more or send hang up to kookoo
	     // call is answered
	 }
	 
}
$r->send();
?> 