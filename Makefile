.PHONY: test
test:
	#PANTHER_CHROME_BINARY="./drivers/chromedriver"
	#PANTHER_NO_HEADLESS=1 ./bin/phpunit --filter ContactControllerWithJsTest
	./bin/phpunit --filter ContactControllerWithJsTest
detect:
	vendor/bin/bdi detect drivers

jwt:
	php bin/console lexik:jwt:generate-keypair


