
if NOT (%COMPUTERNAME%)==(infosys15) goto work
 p:
 cd P:\Projects\php_code_base\src

 start "php_localhost:9999" /D P:\Projects\php_code_base\src php -S localhost:9999

 start "tst" /D P:\Projects\php_code_base phpunit
 goto the_end


:work
 p:
 cd P:\Projects\php_base\src

 start "php_localhost:9999" /D P:\Projects\php_base\src php -S localhost:9999

 start "tst" /D P:\Projects\php_base phpunit
 goto the_end


:the_end
