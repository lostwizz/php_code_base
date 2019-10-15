rem ----------------------------------------------
rem - zip up p:\projects

set fn=P:\projects\php_code_base\mikes2.zip

cd p:\projects\php_code_base
p:
"C:\Program Files\7-Zip\7z.exe" a -r -mmt22 -mx9 %fn% p:\projects\php_code_base\* -xr!*.bak -xr!Backup*\ -xr!*.log
