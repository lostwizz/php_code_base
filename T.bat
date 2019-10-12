@p:
@cd P:\Projects\MikesCommandAndControl2\

@if NOT (%1)==(clean) goto do_tests
	del P:\Projects\MikesCommandAndControl2\.phpunit.result.cache

:do_tests
    rem @call  phpunit --check-version
@call  phpunit --list-tests
@call  phpunit --list-suites


rem --debug
phpunit --verbose --debug

