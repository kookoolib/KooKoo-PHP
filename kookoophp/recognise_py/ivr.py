#!/usr/bin/env python
# -*- coding: utf-8 -*- 
# Import modules for CGI handling 
import cgi, cgitb
import kookoo  # kookoo library
import urllib
import sys, os


 
# The same XML can be created above using the convenience methods
r = kookoo.Response()
form = cgi.FieldStorage()

#variables
lang = 'en-IN'
lang_hi = 'hi-IN'
#kookoo language format for both recognise and tts 
# english : en-IN
# hindi	: hi-IN ..,
def getAudioPath(filename):
	filename = "/audios/" + filename
	noQueryString = os.environ["SCRIPT_NAME"]
	dir = noQueryString.split("/")
	dir.pop()
	noQueryString = "/". join(dir)
	url = ""
        if os.environ["SERVER_PORT"] == 443:
                url = "https://" + os.environ["SERVER_ADDR"] + noQueryString + filename
        else:
                url = "http://" + os.environ["SERVER_ADDR"] + noQueryString + filename
	return url;

def getGotoURL(state):
	url = ""
	if os.environ["SERVER_PORT"] == 443:
		url = "https://" + os.environ["SERVER_ADDR"] + os.environ["SCRIPT_NAME"] + "?state=" + state  
	else:
		url = "http://" + os.environ["SERVER_ADDR"] + os.environ["SCRIPT_NAME"] + "?state=" + state  
	return url

# Get data from fields
event = form.getvalue('event')
state = form.getvalue('state')
if form.getvalue('state') is None:
	state = 'ask_language'
if event is not None and event == 'NewCall':
        r.addPlayText("welcome to voice bot testing application, ",speed=2,quality='best',lang=lang,type="Aditi")
        r.addPlayText("to continue in english, please say english",speed=2,quality='best',lang=lang,type="Aditi")
        r.addPlayText('हिंदी में जारी रखने के लिए हिंदी बोलिये',speed=2,quality='best',lang=lang_hi,type="Aditi")
	r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang,length="1",grammar="qa",success_file=getAudioPath('please_wait_en.wav'))
elif event is not None and event == 'Recognize' and state == 'ask_language':
	recog_text = form.getvalue('data')
	recog_text = urllib.unquote(recog_text)
        if recog_text is not None and 'english' in recog_text.lower():
        	r.addPlayText("Thankyou",speed=2,quality='best',lang=lang,type="Aditi")
        	r.addPlayText('Please say something in english',speed=2,quality='best',lang=lang,type="Aditi")
		r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang,length="1",grammar="qa",success_file=getAudioPath('please_wait_en.wav'))
		state = 'english'
	elif recog_text is not None and 'hindi' in recog_text.lower():
        	r.addPlayText("धन्यवाद",speed=2,quality='best',lang=lang,type="Aditi")
        	r.addPlayText('कृपया हिंदी में कुछ बोलिये',speed=2,quality='best',lang=lang_hi,type="Aditi")
		r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang_hi,length="1",grammar="qa",success_file=getAudioPath('please_wait_hi.wav'))
		state = 'hindi'
	else:
		state = 'ask_language'
        	r.addPlayText("to continue in english, please say english",speed=2,quality='best',lang=lang,type="Aditi")
        	r.addPlayText('हिंदी में जारी रखने के लिए हिंदी बोलिये',speed=2,quality='best',lang=lang_hi,type="Aditi")
		r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang,length="1",grammar="qa",success_file=getAudioPath('please_wait_en.wav'))
elif event is not None and event == 'Recognize':
	recog_text = form.getvalue('data')
	recog_text = urllib.unquote(recog_text)
        if recog_text is not None and state == 'english':
        	r.addPlayText("you have said",speed=2,quality='best',lang=lang,type="Aditi")
        	r.addPlayText(recog_text,speed=2,quality='best',lang=lang,type="Aditi")
        	r.addPlayText('please continue',speed=2,quality='best',lang=lang_hi,type="Aditi")
		r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang,length="1",grammar="qa",success_file=getAudioPath('please_wait_en.wav'))
	elif recog_text is not None and state == 'hindi':
		r.addPlayText("आप ने कहा है",speed=2,quality='best',lang=lang_hi,type="Aditi")
        	r.addPlayText(recog_text,speed=2,quality='best',lang=lang_hi,type="Aditi")
        	r.addPlayText('कृपया जारी रखें',speed=2,quality='best',lang=lang_hi,type="Aditi")
		r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang_hi,length="1",grammar="qa",success_file=getAudioPath('please_wait_hi.wav'))
	elif state == 'hindi':
                r.addPlayText("क्षमा करें, हम आपको समझ नहीं पाए, क्या आप कृपया पुनः प्रयास कर सकते हैं",speed=2,quality='best',lang=lang_hi,type="Aditi")
		r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang_hi,length="1",grammar="qa",success_file=getAudioPath('please_wait_hi.wav'))	
	else:
		r.addPlayText("sorry, we did not get you, could you please try again",speed=2,quality='best',lang=lang,type="Aditi")
                r.addRecognize(type="ggl-stream",timeout="10", silence="3",lang=lang,length="1",grammar="qa",success_file=getAudioPath('please_wait_en.wav'))	
else:
        r.addHangup()
r.addGoto(getGotoURL(state));
print "Content-type: text/xml\n\n"
print(r);
