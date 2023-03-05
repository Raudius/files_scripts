# This file is licensed under the Affero General Public License version 3 or
# later. See the COPYING file.
VERSION?=$(shell sed -ne 's/^\s*<version>\(.*\)<\/version>/\1/p' appinfo/info.xml)
composer=$(shell which composer 2> /dev/null)

# Internal variables
APP_NAME:=$(notdir $(CURDIR))
PROJECT_DIR:=$(CURDIR)/../$(APP_NAME)
BIN_DIR:=$(CURDIR)/bin
BUILD_DIR:=$(CURDIR)/build
BUILD_TOOLS_DIR:=$(BUILD_DIR)/tools
RELEASE_DIR:=$(BUILD_DIR)/release
CERT_DIR:=$(HOME)/.keys
OCC?=php ../../occ

all: dev-setup lint build-js-production test

# Dev env management
dev-setup: clean clean-dev composer npm-init

build-docs:
	composer install --ignore-platform-reqs \
	&& php "$(BIN_DIR)/generate_docs.php" \
	&& composer install --ignore-platform-reqs --no-dev

# Installs and updates the composer dependencies. If composer is not installed
# a copy is fetched from the web
composer:
ifeq (, $(composer))
	@echo "No composer command available, downloading a copy from the web"
	mkdir -p $(BUILD_TOOLS_DIR)
	curl -sS https://getcomposer.org/installer | php
	mv composer.phar $(BUILD_TOOLS_DIR)
	php $(BUILD_TOOLS_DIR)/composer.phar install --prefer-dist --ignore-platform-reqs --no-dev
else
	composer install --prefer-dist --ignore-platform-reqs --no-dev
endif

npm-init:
	npm ci

npm-update:
	npm update

# Building
build-js:
	npm run dev

build-js-production:
	npm run build

watch-js:
	npm run watch

serve-js:
	npm run serve

# Linting
lint:
	npm run lint

lint-fix:
	npm run lint:fix

# Style linting
stylelint:
	npm run stylelint

stylelint-fix:
	npm run stylelint:fix

# Cleaning
clean:
	rm -rf js/*

clean-dev:
	rm -rf node_modules

# Tests
test:
	./vendor/phpunit/phpunit/phpunit -c phpunit.xml
	./vendor/phpunit/phpunit/phpunit -c phpunit.integration.xml

prepare-build:
	mkdir -p $(RELEASE_DIR)
	rsync -a --delete --delete-excluded --verbose\
		--exclude=".[a-z]*" \
		--exclude="Makefile" \
		--exclude="Dockerfile" \
		--exclude /$(APP_NAME)/build \
		--exclude /$(APP_NAME)/docs \
		--exclude /$(APP_NAME)/screenshots \
		--exclude /$(APP_NAME)/bin \
		--exclude /$(APP_NAME)/src \
		--exclude /$(APP_NAME)/node_modules \
		--exclude /$(APP_NAME)/tests \
		--exclude="composer.*" \
		--exclude="package*.json" \
		--exclude="*config.js" \
		--exclude="*config.json" \
		--exclude="*.md" \
	$(PROJECT_DIR) $(RELEASE_DIR)/ \
	&& chmod -R 777 $(RELEASE_DIR)/

# Signs the build files
sign-build:
	echo "Signing app files…"; \
	sudo -u \#33 -- $(OCC) integrity:sign-app --privateKey="$(CERT_DIR)/$(APP_NAME).key" \
    			--certificate="$(CERT_DIR)/$(APP_NAME).crt" \
    			--path="$(RELEASE_DIR)/$(APP_NAME)";

# Packages the build into a release tarball, then signs the tarball.
package-build:
	tar -czf $(RELEASE_DIR)/$(APP_NAME)-$(VERSION).tar.gz \
		-C $(RELEASE_DIR) $(APP_NAME)

# Sign the release tarball
sign-tar:
	echo "Signing release tarball…"; \
	openssl dgst -sha512 -sign $(CERT_DIR)/$(APP_NAME).key \
		$(RELEASE_DIR)/$(APP_NAME)-$(VERSION).tar.gz | openssl base64;

# Deletes any unnecessary files after the build is completed
clean-up-build:
	rm -rf $(RELEASE_DIR)/$(APP_NAME)

# Build a release package
build: npm-init build-js-production composer prepare-build sign-build package-build sign-tar clean-up-build
