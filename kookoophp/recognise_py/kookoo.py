"""
Copyright (c) 2010 Ozonetel, Inc.

Adapted from Twilio Python library. Thanks a lot to Twilio
for providing the library.

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
"""

__VERSION__ = "1.0.0"

from xml.sax.saxutils import escape, quoteattr

try:
    from google.appengine.api import urlfetch
    APPENGINE = True
except:
    APPENGINE = False

class KooKooException(Exception): pass

# KooKoo ML Response Helpers
# ===========================================================================

class Verb(object):
    """KooKoo basic verb object.
    """
    def __init__(self, **kwargs):
        self.name = self.__class__.__name__
        self.body = None
        self.nestables = None

        self.verbs = []
        self.attrs = {}
        for k, v in kwargs.items():
            if k == "sender": k = "from"
            if v: self.attrs[k] = quoteattr(str(v))

    def __repr__(self):
        s = '<%s' % self.name
        keys = self.attrs.keys()
        sorted(keys)
        for a in keys:
            s += ' %s=%s' % (a, self.attrs[a])
        if self.body or len(self.verbs) > 0:
            s += '>'
            if self.body:
                s += escape(self.body)
            if len(self.verbs) > 0:
                s += '\n'
                for v in self.verbs:
                    for l in str(v)[:-1].split('\n'):
                        s += "\t%s\n" % l
            s += '</%s>\n' % self.name
        else:
            s += '/>\n'
        return s

    def append(self, verb):
        if not self.nestables:
            raise KooKooException("%s is not nestable" % self.name)
        if verb.name not in self.nestables:
            raise KooKooException("%s is not nestable inside %s" % \
                (verb.name, self.name))
        self.verbs.append(verb)
        return verb

    def asUrl(self):
        return urllib.quote(str(self))

    def addPlayText(self, text, **kwargs):
        return self.append(PlayText(text, **kwargs))

    def addPlayAudio(self, url, **kwargs):
        return self.append(PlayAudio(url, **kwargs))

    def addHangup(self, **kwargs):
        return self.append(Hangup(**kwargs))

    def addCollectDtmf(self, **kwargs):
        return self.append(CollectDtmf(**kwargs))

    def addRecord(self, filename,**kwargs):
        return self.append(Record(filename,**kwargs))

    def addDial(self,dialnumber,**kwargs):
        return self.append(dial(dialnumber,**kwargs))

    def addConference(self,conferenceNumber,**kwargs):
        return self.append(Conference(conferenceNumber,**kwargs))

    def addRecognize(self,**kwargs):
            return self.append(Recognize(**kwargs))
  
    def addGoto(self,url,**kwargs):
	return self.append(Gotourl(url,**kwargs))	

class Response(Verb):
    """KooKoo response object.

    """
    def __init__(self, version=None, **kwargs):
        Verb.__init__(self, version=version, **kwargs)
        self.nestables = ['PlayText', 'Gotourl', 'PlayAudio', 'CollectDtmf', 'Record', 'Hangup', 'Sms','Conference','dial','Recognize']

class PlayText(Verb):
    """PlayText text

    text: text to say
    """

    def __init__(self, text, **kwargs):
        Verb.__init__(self,**kwargs)
        self.body = text

class Gotourl(Verb):
    """gotourl url

    url: url for next request
    """
    def __init__(self, url, **kwargs):
        Verb.__init__(self,**kwargs)
        self.body = url


class Conference(Verb):
    """Conference conferenceNumber

    conferenceNumber: The identifier for your conference
    """

    def __init__(self, conferenceNumber,caller_onhold_music=None,record=None,caller_id=None,timeout=None,
        version=None,**kwargs):
        Verb.__init__(self,caller_onhold_music=caller_onhold_music,record=record,caller_id=caller_id,timeout=timeout,
            version=version,**kwargs)
        self.body = conferenceNumber

class PlayAudio(Verb):
    """Play audio file at a URL

    url: url of audio file, MIME type on file must be set correctly
    """
    def __init__(self, url, **kwargs):
        Verb.__init__(self, **kwargs)
        self.body = url



class Hangup(Verb):
    """Hangup the call
    """
    def __init__(self, **kwargs):
        Verb.__init__(self)

class CollectDtmf(Verb):
    """Collect digits from the caller's keypad

    maxDigits: how many digits to gather before returning
    timeout: wait for this many seconds before returning
    finishOnKey: key that triggers the end of caller input
    """
    GET = 'GET'
    POST = 'POST'

    def __init__(self, maxDigits=None, timeout=None,
        termchar=None, **kwargs):

        Verb.__init__(self,
            l=maxDigits, o=timeout, t=termchar,
            **kwargs)
        self.nestables = ['PlayText', 'PlayAudio']

class dial(Verb):

    GET = 'GET'
    POST = 'POST'
    def __init__(self,dialnumber,record=None,limittime=None,timeout=None,moh=None,promptToCalledNumber=None,
        caller_id=None,**kwargs):

        Verb.__init__(self,record=record,limittime=limittime,timeout=timeout,moh=moh,promptToCalledNumber=promptToCalledNumber,
            caller_id=caller_id,**kwargs)
        self.body = dialnumber

class Record(Verb):
    """Record audio from caller
	filename: File name of file to be recorded
    maxLength: maximum number of seconds to record
    timeout: seconds of silence before considering the recording complete
    """
    GET = 'GET'
    POST = 'POST'

    def __init__(self, filename,maxDuration=None,
        silence=None, **kwargs):
        Verb.__init__(self,  maxduration=maxDuration,
            silence=silence, **kwargs)
        self.body = filename

class Recognize(Verb):

    GET = 'GET'
    POST = 'POST'

    def __init__(self,type,timeout=None,
        silence=None,lang=None,length=None,grammar=None,success_file=None, **kwargs):
        Verb.__init__(self,type=type,timeout=timeout,
            silence=silence,lang=lang,length=length,grammar=grammar,success_file=success_file, **kwargs)

