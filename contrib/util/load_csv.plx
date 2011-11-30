#!/usr/bin/perl
use strict;

use DBI;

#######################################################################
# Copyright (C) 2008, 2011 Rod Roark <rod@sunsetsystems.com>
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This is an example of generating SQL from CSV data for loading into
# the "codes" table of OpenEMR.
#
# You should customize this program, and then run it like this:
#
#   ./load_csv.plx < something.csv
#######################################################################

#######################################################################
#                 Parameters that you may customize                   #
#######################################################################

# To load the short descriptions (SHORTU.txt, not currently used by
# OpenEMR but probably should), change this to "code_text_short":
#
my $TEXT_COL = "code_text";

my $CODE_TYPE = 11;

#######################################################################
#                             Startup                                 #
#######################################################################

my $countnew = 0;
my $prevcode = '';

$| = 1; # Turn on autoflushing of stdout.

#######################################################################
#                            Main Loop                                #
#######################################################################

while (my $line = <STDIN>) {
  # next unless ($line =~ /^\d+,[A-Z]+,"(\d+)",[A-Z]+,[A-Z]+,[A-Z]+,"[A-Z]+","(.*?)",/);
  next unless ($line =~ /^"(\d+)",,"(.*?)",,/);

  my $code = $1;
  my $desc = $2;
  $desc =~ s/\s*$//g; # remove all trailing whitespace
  $desc =~ s/'/''/g;  # just in case there are any quotes

  next if ($desc =~ /^$/);      # skip of description is empty
  next if ($code == $prevcode); # skip if duplicate key
  $prevcode = $code;

  my $query = "INSERT INTO codes " .
    "( code_type, code, modifier, $TEXT_COL ) VALUES " .
    "( $CODE_TYPE, '$code', '', '$desc' )";
  ++$countnew;

  print $query . ";\n";
}

#######################################################################
#                             Shutdown                                #
#######################################################################

# print "\n$countnew inserts generated.\n";

