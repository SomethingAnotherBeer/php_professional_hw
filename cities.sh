#!/bin/bash

file_head=1
lines_count=$(wc -l < file.txt)
read_for_tail=$(($lines_count-$file_head))

list=($(tail file.txt -n$read_for_tail |  sort -n -r -k4 | awk '{ print $3 }' | head -n3))
unique_list=()

is_contained=0;
index_for_unique=0

for item in "${list[@]}"; do
	for (( i=0; i < ${#unique_list[@]}; i++ )); do
		if [ ${unique_list[$i]} == $item ]; then
			is_contained=1
		fi
	done

	if [ $is_contained -ne 1 ]; then
		unique_list[$index_for_unique]=$item
		index_for_unique=$(($index_for_unique + 1))
	fi
	is_contained=0

done

echo ${unique_list[@]}
exit 0