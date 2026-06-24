#!/bin/bash

checkIsNum(){
	if [[ $1 =~ ^([[:digit:]]+|[[:digit:]]+\.[[:digit:]]+)$ ]]; then
		return 0
	else
		return 1
	fi
}


if [[ 2 -ne $# ]]; then
	echo "Количество аргументов должно быть 2"
	exit 1
fi


current=1
for arg in $@; do
	if ! checkIsNum $arg; then
		echo "Аргумент № $current со значением $arg не является числом"
		exit 1
	fi
	current=$(($current+1))
done


echo $@ | awk '{ print $1+$2 }'
exit 0