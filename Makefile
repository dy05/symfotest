.PHONY: test
test:
	#PANTHER_CHROME_BINARY="/bin/chromium" ./bin/phpunit --filter ContactControllerTest
	./bin/phpunit --filter ContactControllerTest
