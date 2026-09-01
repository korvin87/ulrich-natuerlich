#!/bin/sh

toolpath=$1
filename="$2"
extension=${filename##*.}

if [ ${extension} = "xls" ]
then
# Use xml2csv for older document formats
$toolpath/xls2csv -s8859-1 -dutf-8 "$filename"
else
# Unzip the xlsx file, and grab just the text with sed
# This also replaces opening <t> tags with newlines
# The final `sed G` double spaces the output
$toolpath/unzip -p "$filename" | grep '<t' | sed 's/<t[^<\/]*>/ \
/g' | sed 's/<[^<]*>//g' | grep -v '^[[:space:]]*$' | sed G
fi
