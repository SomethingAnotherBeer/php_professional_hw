#!/bin/bash


if [ -z "$1" ]; then
	echo "Не передан путь к файлу"
	exit 1
fi

file_head=1
lines_count=$(wc -l < $1)
tail_number=0

if [[ -n $2  && $2 =~ ^[[:digit:]]+$  && $2 -gt 0 ]]; then
	tail_number=$2

	if [ $tail_number -eq $lines_count ]; then
		tail_number=$(($tail_number-$file_head))
	fi
else
	tail_number=$(($lines_count-$file_head))
fi


tail -n$tail_number $1 | sort -k4 -n -r | awk ' !seen[$3]++ { print $3}' | head -n3

exit 0