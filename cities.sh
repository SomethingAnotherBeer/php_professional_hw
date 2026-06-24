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



list=($(tail -n$tail_number $1 |  sort -n -r -k4 | awk '{ print $3 }' | head -n3))
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