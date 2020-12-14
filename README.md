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

### How to run the tests
- Open a terminal in the root folder and run command **./bin/phpunit**. This will run all the tests inside **test/** folder. Registration and Login tests might fail if the database **(db/symfonyTest)** is empty. Another reason could be static data inside testcases. Please make sure to change them in case of a failure.

- To test only an individual file with test cases e.g. DashboardControllerTest, run command **./bin/phpunit tests/Functional/Controller/DashboardControllerTest.php**
