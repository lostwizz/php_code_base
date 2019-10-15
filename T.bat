@p:
@cd P:\Projects\php_code_base\

@if NOT (%1)==(clean) goto do_tests
	del P:\Projects\php_code_base\.phpunit.result.cache

:do_tests
    rem @call  phpunit --check-version
@call  phpunit --list-tests
@call  phpunit --list-suites


rem --debug
phpunit --verbose --debug

