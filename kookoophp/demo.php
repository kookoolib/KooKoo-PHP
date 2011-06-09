<?php
session_start();
//start session, session will be maintained for entire call
require_once("response.php");//response.php is the kookoo xml preparation class file
$r = new Response();

$r->setFiller("yes");


/*Description 
 * kookoo request and response process
 * 
 * Step 1: KooKoo NewCall Request
 * 	For Every New call kookoo will send you the below Request Parameters
 *   
 * event=NewCall
 * cid=Caller Number
 * called_number=Dialled Number 
 * sid=session parameter 
 *   // Capture these details to your own Session variables
 * 
 * Step 2: Maintain One session Variable X
 * 		   Maintaining Combination Of KooKoo 'event' parameter and Session Variable X in Unique
 *         This will help you in tracking your position in the application
 *  
 *  Step 3: For Every Event Prepare KooKoo Response XML
 *  
 *   they are 5 events handled by the KooKoo  
 *  make sure event tag is last tag in your kookoo response
 *  Example:
 *  
 *  <response>.....<eventtag></eventtag></response>
 *  
 *  Use response.php to prepare proper kookoo response XML
 *  
 *  Below are the different eventtags: 
 *   1) CollectDTMF  
 *  <?xml version="1.0" encoding="UTF-8"?>    
2	<response sid="12345"> 
    <playtext>Text</playtext>
    <playaudio>AudioUrl</playaudio/>   
3	<collectdtmf l="4" t="#" o="5000">    
4	<playtext>Please enter your pin number
5	</playtext>    
6	</collectdtmf>    
7	</response>

 *   In Response from KooKoo You get below Request Parameters
     event=GotDTMF
 *   data={dtmf given by user}
 *  
 *   2) Play only Text or Audio
 *   ----------------
 *    <response>
 *         <playtext>Text</playtext> //do something as you like playtext or Audio
 *         <playaudio>audioUrl</playaudio> //do something as you like playtext or Audio
 *    </response>       
 *   ---------------
 *   3) Dial 
 *  ---------------
 *      <response>
 *         <playtext></playtext> //do something as you like playtext or Audio
 *         <dial>9xxxxxxxx</dial>   // dont send hangup from kookoo response
 *      </response>  
          
 *   In Response from KooKoo You get below Request Parameters
     event=Dial
 *   data='dial_record_url'
 *   status='dial status' (answered/not_answered)
 *   callduration= 'duration of call after call answered in Seconds'
 *   -------------
 *   4) Record 
 *    ---------------
 *  <?xml version="1.0" encoding="UTF-8"?>
2	<response sid="12345"> 
    <playText>please record your message</playText> //do    
3	<record format="wav" silence="3" maxduration="30">myfilename</record>
4	</response>
 *   In Response from KooKoo You get below Request Parameters
 *   event=Record
 *   data='recordfile_url'
 *   status='record status'
 * ------------------
 *   5) Disconnect 
 *  <?xml version="1.0" encoding="UTF-8"?>   
2	<response>  
3	<hangup></hangup>   
4	</response>
 *     
 *   In Response from KooKoo You get below Request Parameters
 *   event=Disconnect          
 *             
 *   ------------------------------------------          
 *   6) Hangup
 *     Whenever User Hangup the Call, You will get this event
 *      
  *   In Response from KooKoo You get below Request Parameters
  *  event=Hangup
 *   
 *   {
 *   If User Hangup the Call on Dial event You will get below paramters also
 *   data='dial_record_url'
 *   status='dial status' (answered/not_answered)
 *   }        
 *  
 */      

$fileName="./kookoo_trace.log";// create logs to trace your application behaviour
if (file_exists($fileName))
{
        $fp = fopen($fileName, 'a+') or die("can't open file");
}
else
{
        $fp= fopen($fileName, 'x+');// or die("can't open file");
}
fwrite($fp,"----------- kookoo params ------------- \n ");
  foreach ($_REQUEST as $k => $v) {
 	 	fwrite($fp,"param --  $k =  $v \n ");
   } 
fwrite($fp,"----------- session params maintained -------------  \n");
     foreach ($_SESSION as $k => $v) {
	 	fwrite($fp,"session params $k =  $v  \n");
	}
 
if($_REQUEST['event']== "NewCall" ) 
{
	
fwrite($fp,"-----------NewCall from kookoo  -------------  \n");
	// Every new call first time you will get below params from kookoo
	//                                        event = NewCall
	//                                         cid= caller Number
	//                                         called_number = sid
	//                                         sid = session variable
	//    
	//You maintain your own session params store require data
	$_SESSION['caller_number']=$_REQUEST['cid'];
	$_SESSION['kookoo_number']=$_REQUEST['called_number']; 
	//called_number is register phone number on kookoo
	//
	$_SESSION['session_id']   = $_REQUEST['sid'];
	//sid is unique callid for each call
    // you maintain one session variable to check position of your call
    //here i had maintain next_goto as session variable
  $_SESSION['next_goto']='Menu1';
} 
if ($_REQUEST['event']=="Disconnect" || $_REQUEST['event']=="Hangup" ){
	//when users hangs up at any time in call  event=Disconnect 
    // when applicatoin sends hangup event event=Disconnect  
	
    //if users hang up the call in dial event you will get data ans status params also
    //$_SESSION['dial_record_url']=$_REQUEST['data'];
    //$_SESSION['dial_status']=$_REQUEST['status'];
exit;
} 

if($_SESSION['next_goto']=='Menu1'){
 	$collectInput = New CollectDtmf();
	$collectInput->addPlayText('welcome to koo koo',4);
	$collectInput->addPlayText('press 1 for record, press 2 dial, press 3 for conference test, press 4, for other',4);
	$collectInput->setMaxDigits('1'); //max inputs to be allowed
	$collectInput->setTimeOut('4000');  //maxtimeout if caller not give any inputs
	$r->addCollectDtmf($collectInput);
    $_SESSION['next_goto']='Menu1_CheckInput';
}
else if($_REQUEST['event'] == 'GotDTMF' && $_SESSION['next_goto'] == 'Menu1_CheckInput' )
{
//input will come data param
//print parameter data value
 if($_REQUEST['data'] == ''){ //if value null, caller has not given any dtmf
	//no input handled
	 $r->addPlayText('you have not entered any input');
	 $_SESSION['next_goto']='Menu1';
}else if($_REQUEST['data'] == '1'){
	$_SESSION['next_goto'] = 'Record_Status';
	$r->addPlayText('Please record your message after beep ');
	//give unique file name for each recording
	$r->addRecord('filename2','wav','120');
}else if($_REQUEST['data'] == '2'){
    $_SESSION['next_goto'] = 'DialMenu';
}else if($_REQUEST['data'] == '3'){
	$r->addPlayText('your conference number is 1 2 3 4 ');
	$r->addConference(1234); 
	$_SESSION['next_goto'] = 'Dial1_Status';
}else if($_REQUEST['data'] == '4'){
	$r->addGoto('http://127.0.0.1/kookoophp/demo_gotopage.php'); //update the url to redirect from demo.php to demo_gotopage.php 
	//url should be full url : 'http://host../nextapp.app' it will jump to next url
	$_SESSION['next_goto'] = 'Menu1';
}
else{
	$r->addPlayText('Thats an invalid input');
}
}
else if($_SESSION['next_goto']=='DialMenu'){
	
	$collectInput = New CollectDtmf();
	$collectInput->addPlayText('please enter the number that you want to dial followed by hash, if it is s t d number, enter 0 as pre fix ',3);
	//$collectInput->addPlayText('press 1 for record, press 2 dial press 3 for cc transer',4);
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
     $r->addPlayText('please wait while we transfer your call to our customer care');
	 $r->addDial($_SESSION['dial'],'true',1000,30,'ring');
	 $_SESSION['next_goto'] = 'Dial1_Status';
     }
}
else if($_REQUEST['event'] == 'Record' && $_SESSION['next_goto'] == 'Record_Status' )
{
//recorded file will be come as  url in data param
//print parameter data value
	 $r->addPlayText('your recorded audio is ');
	 $_SESSION['record_url']=$_REQUEST['data'];
	 $r->addPlayAudio($_SESSION['record_url']);
	 $r->addPlayText('Thanks you for calling, have a nice day');
	 $r->addHangup();	
}else if($_REQUEST['event'] == 'Dial' && $_SESSION['next_goto'] == 'Dial1_Status' )
{
//dial url will come data param  //if dial record false then data value will be -1 or null
//dial status will come in status (answered/not_answered) param
//print parameter data and status params value
 	 $_SESSION['dial_record_url']=$_REQUEST['data'];
	 $_SESSION['dial_status']=$_REQUEST['status'];
	 $_SESSION['dial_callduration']=$_REQUEST['callduration'];
	 if($_REQUEST['status'] == 'not_answered'){
	//if you would like dial another number, if first call not answered,
	//	
	 $r->addDial("99999999",'true',1000,30,'default');
	 }else{
	 	 $r->addPlayText('Thank you for calling, ');
	 	 $r->addHangup();	// do something more or send hang up to kookoo
	// call is answered
	 }
	 
}else {
	//print you session param 'next_goto' and other details
      $r->addPlayText('Sorry, session and events not maintained properly, Thank you for calling, have nice day');
      $r->addHangup();	// do something more or to send hang up to kookoo	
}


//print final response xml send to kookoo, It would help you to understand request response between kookoo and your application
//  	 	$r->getXML();
//
//$logs->writelog("final response xml addedd  ".$r->getXML().PHP_EOL." ::::\t\t\t");
fwrite($fp,"----------- final xml send to kookoo  -------------  ".$r->getXML()."\n");
$r->send();
?>
