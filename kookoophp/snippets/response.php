<?php
    /*
    Copyright (c) 2010-2011 Ozonetel Pvt Ltd.

    Permission is hereby granted, free of charge, to any person
    obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without
    restriction, including without limitation the rights to use,
    copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following
    conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
    HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
    WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
    OTHER DEALINGS IN THE SOFTWARE.
    */    
    
    // VERSION: 1.0
    
    // KooKoo Php Libraries
    // ========================================================================
    class Response
    {
		private $doc;
		private $response;
		//constructor to have multiple constructors
		function __construct()
		{
			$a = func_get_args();
			$i = func_num_args();
			if (method_exists($this,$f='__construct'.$i)) {
				call_user_func_array(array($this,$f),$a);
			}
		}
	   
		function __construct0()
		{
			$this->doc= new DOMDocument("1.0", "UTF-8");
           
            $this->response= $this->doc->createElement("response");
            
			$this->doc->appendChild( $this->response);// root tag for xml responce
		
		}
	   
		function __construct1($sid)
		{
			$this->doc= new DOMDocument("1.0", "UTF-8");
           
            $this->response= $this->doc->createElement("response");
            
			$this->response->setAttribute( "sid", $sid);
			
			$this->doc->appendChild( $this->response); // root tag for xml responce


		}
		public function setSid($sid) // unique id for each call : session identifation number
		{
			$this->response->setAttribute( "sid", $sid);
		}
		
    	public function setFiller($filler)
    	//set filler yes or no 
    	//When, url invocation takes time, it would play the music back ground, untill get response from url
		{                      
			$this->response->setAttribute( "filler", $filler);
		}
		
		public function addPlayText($text,$speed=2)// to play text 
		{
			$play_text =$this->doc->createElement("playtext",$text);
			$play_text ->setAttribute( "speed", $speed );
			//speed used for voice-rate speed limit form 1-9 
			//if speed 1 plays slow, speed =9 plays fastly, this is only for nuance tts
			$this->response->appendChild($play_text);
		}
		
		public function addHangup(){// To Disconnect the call
			$hangup =$this->doc->createElement("hangup");
			$this->response->appendChild($hangup);
		}
     //Dial
    	public function addDial($no,$record="false",$limittime="1000",$timeout,$moh='default',$promptToCalledNumber ='no')
         {
          	$dial= $this->doc->createElement("dial",$no);
          	$dial ->setAttribute( "record", $record );
          	$dial ->setAttribute( "limittime", $limittime );// for max calltime //maxtime call allowed after called_number answered
          	$dial ->setAttribute( "timeout", $timeout );
          	$dial ->setAttribute( "moh", $moh ); //moh=default will be music on hold moh=ring for normal ring
          	$dial ->setAttribute( "promptToCalledNumber", $promptToCalledNumber ); //=no
        	                                                                      //If would like to play prompt to called number, give audio url
          	                                                                      // promptToCalledNumber = 'http://www.kookoo.in/recordings/promptToCallerParty.wav'  
           	$this->response->appendChild($dial);
         }
       //for conferencing the call
		 public function addConference($confno,$record="true")
         {
                //$confno confirence number to set
                $conf= $this->doc->createElement("conference",$confno);
                $conf ->setAttribute( "record", $record ); // to enable conference recording, record = 'true'
                // record file you can get http://kookoo.in/recordings/<kookoo-username>/<did>_<confno>.wav
                $this->response->appendChild($conf);
         }


 		//send sms
		public function sendSms($text,$no)
		 {
		$sendsms = $this->doc->createElement( "sendsms",$text);
		$sendsms ->setAttribute( "to", $no );
		$this->response->appendChild($sendsms);
		}
		public function addPlayAudio($url){
		// audio to play
		//$url = 'http://ipadress/welcome.wav'
		//wav file format must be
		//PCM-128kbps-16bit-mono-8khz
		//see http://kookoo.in/index.php/kookoo-docs/audio for audio preparation
			$play_audio =$this->doc->createElement("playaudio",$url);
			$this->response->appendChild($play_audio);
		}
       public function addGoto($url){
    	//url should be full url : 'http://host../nextapp.app'
    	// it will jump to next url
			$goto =$this->doc->createElement("gotourl",$url);
			$this->response->appendChild($goto);
		}
		public function playdtmf(){
			$playdtmf =$this->doc->createElement("playtdtmf-i");
			$this->response->appendChild($playdtmf);
		}
		
		public function addCollectDtmf($cd){
			$collect_dtmf=$this->doc->importNode($cd->getRoot(),true);
			$this->response->appendChild($collect_dtmf);
		}
		//recordtag
		public function addRecord($filename,$format="wav",$silence="4",$maxduration="60",$option="k")
		{
			$record = $this->doc->createElement( "record",$filename);
			$record->setAttribute( "format", $format );
			$record->setAttribute( "silence", $silence);
			$record->setAttribute( "maxduration",$maxduration);
			$record->setAttribute( "option",$option);//k= keep recording after hangup
			$this->response->appendChild($record );
	    }

		// Parse the XML.and Deconstruct

		public function getXML()
		{
			return $this->doc->saveXML();
		}
		
		public function send()
		{
			print $this->doc->saveXML();

		}


	}
class CollectDtmf
    {
		private $doc;
		private $collect_dtmf;
		//constructor to have multiple constructors
		function __construct()
		{
			$a = func_get_args();
			$i = func_num_args();
			if (method_exists($this,$f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
			}
		}
	function __construct0()
		{
			$this->doc= new DOMDocument("1.0", "UTF-8");
            $this->collect_dtmf= $this->doc->createElement("collectdtmf");
			$this->doc->appendChild( $this->collect_dtmf);
		}
	function __construct3($max_digits,$term_char,$time_out=4000) //time out in ms
		{
			$this->doc= new DOMDocument("1.0", "UTF-8");
            $this->collect_dtmf= $this->doc->createElement("response");
			$this->collect_dtmf->setAttribute( "l", $max_digits);
			$this->collect_dtmf->setAttribute( "t", $term_char);
			$this->collect_dtmf->setAttribute( "o", $time_out);
			$this->doc->appendChild( $this->collect_dtmf);
		}
	public function setMaxDigits($maxDigits)
		{
			$this->collect_dtmf->setAttribute("l", $maxDigits);
		}
	public function setTermChar($termChar){
		//if dtmf maxdigits not fixed and variable send termination
		//example if your asking enter amount, user can enter any input 
		// 1 - n number exampe 1 or 20 2000 etc
		//then ask cutomer to enter amount followed by hash set termchar=# 
		//set maxdigits=<maximum number to be allowed>
		
			$this->collect_dtmf->setAttribute("t", $termChar);
			
		}
		public function setTimeOut($timeOut){
	    	$this->collect_dtmf->setAttribute("o", $timeOut=4000);
	    	//time out in ms default is 4000ms,
		}
	public function addPlayText($text,$speed=2)
		{
			//speed used for voice-rate speed limit form 1-9 
			//if speed 1 plays slow, speed =9 plays fastly, this is only for nuance tts
	
			$play_text =$this->doc->createElement("playtext",$text);
			$play_text ->setAttribute("speed", $speed );
			$this->collect_dtmf->appendChild($play_text);
		}
	public function addPlayAudio($url,$speed=2){
		// audio to play
		//$url = 'http://ipadress/welcome.wav'
		//wav file format must be
		//PCM-128kbps-16bit-mono-8khz
		//see http://kookoo.in/index.php/kookoo-docs/audio for audio preparation
	
			$play_audio =$this->doc->createElement("playaudio",$url);
			$this->collect_dtmf->appendChild($play_audio);
		}
	public function getRoot()
		{
			return $this->collect_dtmf;
		}
	
	}
?>
