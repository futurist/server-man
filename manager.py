#!/usr/bin/python
#encoding=utf-8
print "Content-type: text/html\n"

import os
import re
import sys
import cgi
import cgitb
import subprocess
cgitb.enable(display=1, logdir="./")


def servercmd(cmd):
    proc = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
    (output,outerr) = proc.communicate()
    # script_response = proc.stdout.read()
    if outerr:
        print outerr
    print cgi.escape(output)


form = cgi.FieldStorage()
action = form.getvalue("action","")

if action=='start' :
    servercmd("service wdapache start")

if action=='stop' :
    servercmd("service wdapache stop")



print

print """
<input type="button" value="Start" onclick=" this.disabled='disabled'; window.location='?action=start' "><br><br><br>
<input type="button" value="Stop" onclick=" this.disabled='disabled'; window.location='?action=stop' "><br><br><br>

"""

