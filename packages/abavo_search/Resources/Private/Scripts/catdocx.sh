#!/bin/sh

toolpath=$1
filename="$2"
extension=${filename##*.}

if [ ${extension} = "doc" ]
then
# Use cat doc for older document formats
$toolpath/catdoc "$filename"
else
# Unzip the docx file, and grab just the text with sed
# This also replaces opening <w:r> tags with newlines
# The final `sed G` double spaces the output
$toolpath/unzip -p "$filename" | grep '<w:r' | sed 's/<w:p[^<\/]*>/ \
/g' | sed 's/<[^<]*>//g' | grep -v '^[[:space:]]*$' | sed G
fi
