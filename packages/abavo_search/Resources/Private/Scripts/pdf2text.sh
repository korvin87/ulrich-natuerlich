#!/bin/sh

toolpath=$1
filename="$2"

$toolpath/pdftotext -raw -nopgbrk -enc UTF-8 "$filename" && cat "${filename%.*}.txt" && rm "${filename%.*}.txt"
