.PHONY: validate install update php/cs-check php/cs-fix php/analyze php/tests php/testdox ci clean

define header =
    @if [ -t 1 ]; then printf "\n\e[37m\e[100m  \e[104m $(1) \e[0m\n"; else printf "\n### $(1)\n"; fi
endef

#~ Composer dependency
validate:
	$(call header,Composer Validation)
	@composer validate

install:
	$(call header,Composer Install)
	@composer install

update:
	$(call header,Composer Update)
	@composer update
	@composer bump --dev-only

composer.lock: install

#~ Vendor binaries dependencies
vendor/bin/php-cs-fixer: composer.lock
vendor/bin/phpstan: composer.lock
vendor/bin/phpunit: composer.lock

#~ Report directories dependencies
build/reports/phpunit:
	@mkdir -p build/reports/phpunit

build/reports/phpstan:
	@mkdir -p build/reports/phpstan

#~ main commands
php/deps: composer.json
	$(call header,Checking Dependencies)
	@XDEBUG_MODE=off ./vendor/bin/composer-dependency-analyser --config ./ci/composer-dependency-analyser.php # for shadow, unused required dependencies and ext-* missing dependencies

php/cs-check: vendor/bin/php-cs-fixer
	$(call header,Checking Code Style)
	@XDEBUG_MODE=off ./vendor/bin/php-cs-fixer check -v --diff
php/cs-fix: vendor/bin/php-cs-fixer
	$(call header,Fixing Code Style)
	@XDEBUG_MODE=off ./vendor/bin/php-cs-fixer fix -v

php/analyze: vendor/bin/phpstan build/reports/phpstan #manual & ci
	$(call header,Running Static Analyze - Pretty tty format)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --error-format=table

php/tests: vendor/bin/phpunit build/reports/phpunit
	$(call header,Running Unit & Integration Tests)
	@XDEBUG_MODE=coverage php ./vendor/bin/phpunit --testsuite=unit,integration --coverage-clover=./build/reports/phpunit/clover.xml --log-junit=./build/reports/phpunit/unit.xml --coverage-php=./build/reports/phpunit/unit.cov --coverage-html=./build/reports/coverage/ --fail-on-warning

php/test: php/tests

php/testdox: vendor/bin/phpunit #manual
	$(call header,Running Unit & Integration Tests (Pretty format))
	@XDEBUG_MODE=coverage php ./vendor/bin/phpunit --testdox --testsuite=unit,integration --coverage-clover=./build/reports/phpunit/clover.xml --log-junit=./build/reports/phpunit/unit.xml --coverage-php=./build/reports/phpunit/unit.cov --coverage-html=./build/reports/coverage/ --fail-on-warning

php/tests-unit: vendor/bin/phpunit build/reports/phpunit #ci
	$(call header,Running Unit Tests)
	@XDEBUG_MODE=coverage php ./vendor/bin/phpunit --testsuite=unit --fail-on-warning

php/tests-integration: vendor/bin/phpunit build/reports/phpunit #manual
	$(call header,Running Integration Tests)
	@XDEBUG_MODE=coverage php ./vendor/bin/phpunit --testsuite=integration --fail-on-warning

clean:
	$(call header,Cleaning previous build) #manual
	@if [ "$(shell ls -A ./build)" ]; then rm -rf ./build/*; fi; echo " done"

ci: clean validate install php/deps php/cs-check php/tests php/analyze
