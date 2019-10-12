

-------------------------------------------------------
to run php server
P:
cd \Projects\MikesCommandAndControl2\src

php -S localhost:9999


-------------------------------------------------------
to run test suite:

cd P:\Projects\MikesCommandAndControl2\


	phpunit
   - or -
	phpunit --bootstrap vendor/autoload.php

-------------------------------------------------------


https://www.cloudways.com/blog/getting-started-with-unit-testing-php/


----------------
when copying to the PI - do this:
	sudo chmod 7777 /var/www/html/MikesCommandAndControl2/src/logs/*
