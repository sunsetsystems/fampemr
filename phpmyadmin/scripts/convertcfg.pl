#!/usr/bin/perl
#
# $Id: convertcfg.pl,v 1.1.1.1 2009/05/12 21:27:30 bradymiller Exp $
#
# Configuration converter
# Converts from old-style (Pre-2.3) configuration files to new format found in PMA-2.3
#
# Takes input from STDIN, sends output to STDOUT
#
# By Robin Johnson robbat2@users.sourceforge.net
# Many thanks to Patrick Lougheed pat@tfsb.org
#

while(<>) 
{	s/\$cfg(\w+)/\$cfg\[\'$1\'\]/g; 
	print; 
	}


