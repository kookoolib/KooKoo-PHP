<?php
require 'response.php';//'test_responce.php'; //include response.php into your code
$r = new Response();
$state = isset($_GET['state'])?$_GET['state']:'ask_language';

if (isset($_GET['event']) && $_GET['event'] == 'NewCall') 
{
	$r->addPlayText("welcome to voice bot testing application, ","2","en-IN","best","Aditi");
	$r->addPlayText("to continue in english, please say english ","2","en-IN","best","Aditi");
	$r->addPlayText("हिंदी में जारी रखने के लिए हिंदी बोलिये","2","hi-IN","best","Aditi");
	$r->addRecognize('ggl-stream','recogmo',3,3,'en-IN',getAudioPath('please_wait_en.wav'));
} 
elseif (isset($_GET['event']) && $_GET['event'] == 'Recognize' && $state == 'ask_language')  
{
	if (isset($_GET['data']) && !empty($_GET['data']) && strpos(strtolower($_GET['data']),'english') !== false) 
	{
		$r->addPlayText("Thankyou","2","en-IN","best","Aditi");
		$r->addPlayText("Please say something in english","2","en-IN","best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,10,'en-IN',getAudioPath('please_wait_en.wav'));
		$state = 'english';	
	} else if(isset($_GET['data']) && !empty($_GET['data']) && strpos(strtolower($_GET['data']),'hindi') !== false) {
		$r->addPlayText("धन्यवाद","2","hi-IN","best","Aditi");
		$r->addPlayText("कृपया हिंदी में कुछ बोलिये","2","hi-IN", "best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,10,'hi-IN',getAudioPath('please_wait_hi.wav'));
		$state = 'hindi';
	} else {
		$state = 'ask_language';	
		$r->addPlayText("to continue in english, please say english ","2","en-IN","best","Aditi");
		$r->addPlayText("हिंदी में जारी रखने के लिए हिंदी बोलिये","2","hi-IN","best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,3,'en-IN',getAudioPath('please_wait_en.wav'));
	}
}
elseif (isset($_GET['event']) && $_GET['event'] == 'Recognize') 
{
	if (isset($_GET['data']) && !empty($_GET['data']) && $state == 'english') 
	{
		$r->addPlayText("you have said ","3","en-IN","best","Aditi");
		$r->addPlayText($_GET['data'],"3","en-IN","best","Aditi");
		$r->addPlayText("please continue","2","en-IN","best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,10,'en-IN',getAudioPath('please_wait_en.wav'));
		$state = 'english';	
	} 
	else if (isset($_GET['data']) && !empty($_GET['data']) && $state == 'hindi')
	{
		$r->addPlayText("आप ने कहा है ","3","hi-IN","best","Aditi");
		$r->addPlayText($_GET['data'],"3","hi-IN","best","Aditi");
		$r->addPlayText("कृपया जारी रखें","2","hi-IN","best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,10,'hi-IN',getAudioPath('please_wait_hi.wav'));
		$state = 'hindi';
	} else if($state == 'hindi') {
		$r->addPlayText("क्षमा करें, हम आपको समझ नहीं पाए, क्या आप कृपया पुनः प्रयास कर सकते हैं","2","hi-IN","best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,10,'hi-IN',getAudioPath('please_wait_hi.wav'));
	} else {
		$r->addPlayText("sorry, we did not get you, could you please try again ","2","en-IN","best","Aditi");
		$r->addRecognize('ggl-stream','recogmo',3,10,'en-IN',getAudioPath('please_wait_en.wav'));
	}
}else{
#invalid block 
	$r->addHangup("invalid block, ending the application");
}
$r->addGoto(getGotoURL($state));
$r->send();
exit;


/**functions**/
function getAudioPath($filename) {
	$filelocation = "audios/$filename";
	$noQueryString = preg_replace('/\?.*$/', '', $_SERVER["REQUEST_URI"]);
	$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $noQueryString;
	$url = trim($url);
	$url .= "/" . $classname;
	$url = $_SERVER['REQUEST_URI']; //returns the current URL
	$parts = explode('/',$url);
	$dir = $_SERVER['SERVER_NAME'];
	for ($i = 0; $i < count($parts) - 1; $i++) {
		$dir .= $parts[$i] . "/";
	}
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$dir";
	return $url.$filelocation;
}

function getGotoURL($state, $params = array()) {
	$classname = 'state=' . $state;
	foreach ($params as $key => $value) {
		$classname .= "&amp;" . $key . "=" . urlencode($value);
	}
	$noQueryString = preg_replace('/\?.*$/', '', $_SERVER["REQUEST_URI"]);
	$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $noQueryString;
	$url = trim($url);
	$url .= "?" . $classname;
	return $url;
}


?>
