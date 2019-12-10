@echo off
@p:
@cd P:\Projects\NB_projects\php_code_base\

@if NOT (%1)==(clean) goto do_tests
	del P:\Projects\NB_projects\php_code_base\\.phpunit.result.cache

pause

:do_tests
    rem @call  phpunit --check-version

REM @call  phpunit --list-tests -c phpunit.xml
REM @call  phpunit --list-suites -c phpunit.xml

rem phpunit --verbose --debug -c phpunit.xml --coverage-php P:\Projects\NB_projects\php_code_base\tests\generated_coverage.html

rem phpunit --dump-xdebug-filter build/xdebug-filter.php

rem phpunit --prepend build/xdebug-filter.php --coverage-html build/coverage-report

Set opt=
@if (%1)==(d) set opt= --debug

rem --debug
@echo .
@echo .
@echo .
@echo .
@echo .
@echo *******************************************************************************************************************

@echo on
cmd /c phpunit --verbose %opt% -c phpunit.xml


type f:\temp\data.txt

