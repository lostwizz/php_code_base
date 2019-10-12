rem ----------------------------------------------
rem - zip up p:\projects

set fn=P:\projects\MikesCommandAndControl2\mikes2.zip

cd p:\projects\MikesCommandAndControl2
p:
"C:\Program Files\7-Zip\7z.exe" a -r -mmt22 -mx9 %fn% p:\projects\MikesCommandAndControl2\* -xr!*.bak -xr!Backup*\ -xr!*.log
