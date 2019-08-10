# WebsiteScreenshot
Quick exercise of an application being able to take screenshot of website pushing it to your google drive.
## Getting started
### Installation
First download or [clone](https://help.github.com/en/articles/cloning-a-repository) this repository.
### Prerequisites 
This project run on PHP 7.1.
### Download dependencies 
To download all dependencies, go to the project root and run:
```
php composer.phar install
```
### Configure database
#### Configure access
Go to the ".env" file and edit the DATABASE_URL line as:
```
DATABASE_URL=mysql://'database_user_username':'database_user_pass'@'database:id':'database_access_port'/'database_name'
```
example for a database that you want to named 'mysqlreport' running on your local machine with the original configuration:
```
DATABASE_URL=mysql://root:@127.0.0.1:3306/mysqlreport
```
#### Create database
To create the database run:
```
php bin/console doctrine:database:create
```
#### Update schema
To update the database schema run:
```
php bin/console doctrine:schema:update --force
```
#### Create data with fixtures
You can load fixtures by running:
```
php bin/console doctrine:fixtures:load
```
### Run the project
You can now run the project by doing:
```
php bin/console server:run
```
### See the result
You can now go to http://localhost:8000/ to see the dashboard.