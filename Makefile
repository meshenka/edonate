##
# binaries path
#
include make.properties-dist
-include make.properties

##
# default variables
#
export SYMFONY_ENV = dev
ASSETS_OPT = --symlink web

##
# Default target (dev symfony env)
#
all: build

##
# Build pipeline for dev environment
#
build:
	$(PHP) app/check.php
	$(COMPOSER) install $(COMPOSER_OPT)
	$(CONSOLE) assets:install $(CONSOLE_OPT) $(ASSETS_OPT)
	$(NPM) install
	$(BOWER) install
	$(GULP)
	$(CONSOLE) assetic:dump $(CONSOLE_OPT)
	$(CONSOLE) cache:clear $(CONSOLE_OPT)

##
# Load fixtures
#
fixture:
	$(CONSOLE) doctrine:schema:drop --force
	$(CONSOLE) doctrine:schema:create
	$(CONSOLE) doctrine:fixture:load -n

##
# Build pipeline for recette (prod symfony environment)
# with dev dependencies
#
recette: export SYMFONY_ENV = prod
recette: build

##
# Build pipeline for production
# answer symfony env
#
prod: recette

unit: export SYMFONY_ENV = test
unit:
		$(PHPSPEC) run -f pretty

inte: export SYMFONY_ENV = test
inte:
	$(CONSOLE) cache:clear $(CONSOLE_OPT)
	#find features/ -name "*.feature" | $(FASTEST) "$(BEHAT) {}"
	$(BEHAT) --format=pretty
test: unit inte

lint:
	$(PHPCSFIXER) fix . --config=sf23
