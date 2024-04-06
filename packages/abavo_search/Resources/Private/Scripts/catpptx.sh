#!/bin/sh

toolpath=$1
filename="$2"
extension=${filename##*.}

if [ ${extension} = "ppt" ]
then
# Use xml2csv for older document formats
$toolpath/catppt -s8859-1 -dutf-8 "$filename"
else
# Unzip the pptx file, and grab just the text with sed
# This also replaces opening <t> tags with newlines
# The final `sed G` double spaces the output
$toolpath/unzip -p "$filename" | grep -oP '(?<=\<a:t\>).*?(?=\</a:t\>)'
fi
