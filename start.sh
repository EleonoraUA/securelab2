file=".key"

if [ ! -f "$file"  ]
then
  echo "There is no signature found!"
  exit 1
fi
if [ ! -r "$file" ] ; then
    echo Cannot write to $file
    exit 1
fi

signature=$(cat $file)
echo signature

# Collect information to validate signature

user_name=$(whoami)
computer_name=$(hostname)
kernel=$(uname -r)

keyboard=$(setxkbmap -print | grep xkb_symbols | awk '{print $4}' | awk -F"+" '{print $2}')
screen=$(xdpyinfo  | grep dimensions)
disks=$(cat /proc/partitions | awk '{print $4}' | grep sd)
fs_type=$(df -T  "$filename" | awk 'FNR == 2 {print $2}')

hash_string=$(echo -n "$keyboard + $screen + $disks + $fs_type + $user_name + $computer_name + $kernel" | md5sum | awk '{print $1}')

echo hash_string
echo  $(cat $file)
if [ "$hash_string" == "$signature" ]
then
  echo "Verified! Allowed to execute..."
  php bin/console ser:run
else
  echo "Signature doesn't appear to be right! Exiting..."
  exit 1
fi