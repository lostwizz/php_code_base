rem

robocopy P:\Projects\php_code_base\src   \\vm-app-prd5\c$\inetpub\wwwroot\TestApp * /s /e /z /xf *.bak /xd bak _config /R:11 /w:1 /eta

rem  pause
robocopy P:\Projects\php_code_base\src\_config   \\vm-app-prd5\c$\inetpub\wwwroot\TestApp\_config * /s /e /z /xf *.bak /xd bak  /R:11 /w:1 /eta
