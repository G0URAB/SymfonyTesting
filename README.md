# Symfony Test Examples
In this repo, I am going to provide some sample symfony functional test examples. I am also going to provide some examples of end-to-end testing using Panther.

## Setup Instructions
- Copy the repository with git clone
- Run the command **composer install**
- In the .env file provide a mailer DSN

## So far the repository has following functional tests as follows:
- [Registration Process Test](https://github.com/G0URAB/symfonyTestExamples/blob/master/tests/Functional/Controller/RegistrationControllerTest.php)
- [Login with data provider (+fastLogin)](https://github.com/G0URAB/symfonyTestExamples/blob/master/tests/Functional/Controller/SecurityControllerTest.php)
- [3 essential stages of file upload](https://github.com/G0URAB/symfonyTestExamples/blob/master/tests/Functional/Controller/DashboardControllerTest.php#L27)
- [An AJAX request and response](https://github.com/G0URAB/symfonyTestExamples/blob/master/tests/Functional/Controller/DashboardControllerTest.php#L65)
- [Panther E2E test for login, upload file and delete file with JS prompt](https://github.com/G0URAB/symfonyTestExamples/blob/master/tests/Panther/End2EndTest.php)

### How to run the tests and reasons for failure
- Open a terminal in the root folder and run command **./bin/phpunit**. This will run all the tests inside **test/** folder. Registration and Login tests might fail if the database **(db/symfonyTest)** is empty. Another reason could be static data inside testcases. Please make sure to change them in case of a failure.

- To test only an individual file with test cases e.g. DashboardControllerTest, run command **./bin/phpunit tests/Functional/Controller/DashboardControllerTest.php**
- Test can also fail if the test-case is sending an email e.g. during a registration process but no **mailer DSN** has been setup.
- To handle **This version of ChromeDriver only supports Chrome version X**, download respective chromium driver and run the test with Panther environment variable e.g. **PANTHER_CHROME_DRIVER_BINARY=chrome_driver/chromedriver ./bin/phpunit tests/Panther/**
- For visual feedback and debugging purpose, run Panther in headless mode as: **PANTHER_CHROME_DRIVER_BINARY=chrome_driver/chromedriver PANTHER_NO_HEADLESS=1 ./bin/phpunit tests/Panther/ --debug**
