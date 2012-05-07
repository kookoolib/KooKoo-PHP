  <?php
  //Description:Sample Code to check a pin given by user.
  session_start();
  require_once("response.php");//response.php is the kookoo xml preparation class file
  $r = new Response();
  $store =array(9704); // declaration of array
  if($_REQUEST['event']== "NewCall" ) 
  {
	  $collectInput = new CollectDtmf();
	  $collectInput->addPlayText('please enter the pin',3);
	  $collectInput->setMaxDigits('4'); //max inputs to be allowed
	  $collectInput->setTimeOut('4000');  //maxtimeout if caller not give any inputs
	  $collectInput->setTermChar('#');  
	  $r->addCollectDtmf($collectInput);
	  $_SESSION['next_goto']='check_pin';
  
  
  }
  else if($_REQUEST['event'] == "GotDTMF" && $_SESSION['next_goto']=='check_pin')
  {
	 if($_REQUEST['data']=="")
	 {

	  $collectInput = new CollectDtmf();
	  $collectInput->addPlayText('You have not given any input',3);
	  $collectInput->addPlayText('please enter the pin again',3);
	  $collectInput->setMaxDigits('4'); //max inputs to be allowed
	  $collectInput->setTimeOut('4000');  //maxtimeout if caller not give any inputs
	  $collectInput->setTermChar('#');  
	  $r->addCollectDtmf($collectInput);
	  $_SESSION['next_goto']='check_pin';
	  
	  }
	   else if($_REQUEST['data']==$store[0])
	   {
		   $r->addPlayText("Welcome to YourCompany");
	  //your code here 
	   }
	   else
	   {
		  $r->addPlayText("The pin you entered is wrong");
		  $r->addHangup();//call disconnects at this stage 
	   }
  
  
  }
  $r->send();
  ?> 