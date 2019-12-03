@p:
@cd P:\Projects\NB_projects\php_code_base\

@if NOT (%1)==(clean) goto do_tests
	del P:\Projects\NB_projects\php_code_base\\.phpunit.result.cache

pause

:do_tests
    rem @call  phpunit --check-version
@call  phpunit --list-tests -c phpunit.xml
@call  phpunit --list-suites -c phpunit.xml


rem phpunit --verbose --debug -c phpunit.xml --coverage-php P:\Projects\NB_projects\php_code_base\tests\generated_coverage.html

rem phpunit --dump-xdebug-filter build/xdebug-filter.php

rem phpunit --prepend build/xdebug-filter.php --coverage-html build/coverage-report

rem --debug
phpunit --verbose --debug -c phpunit.xml

