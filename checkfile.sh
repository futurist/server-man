#!/bin/bash

# $crontab -e
# add below line:
# */2 * * * * /home/wdlinux/checkfile.sh>/dev/null 2>&1                                             

running=0

if /sbin/service cwpsrv status | grep -c "running">/dev/null 
then
    running=1
else
    running=0
fi


if [ ! -f /home/1111hui/public_html/__manager/wdapache.001 ]; then
    # echo "File not found!"
    if test "$running" -eq 1
    then
	/sbin/service cwpsrv stop
    fi
else
    if test "$running" -eq 0
    then
	/sbin/service cwpsrv start
    fi
fi

hostfile=/home/1111hui/public_html/__manager/host.ip
if [ -a "$hostfile" ]; then
  hostip=$(cat $hostfile)
  if ! grep -s -e "$hostip" /etc/hosts.allow>/dev/null; then
    echo "sshd:$hostip" >> /etc/hosts.allow
  fi
  rm -f "$hostfile"
fi

#source /home/wdlinux/loadaverage.sh
