# README #

CSCI B470 Backend

### Requirements to run this project on a server: ###

* PHP 7.0.25 or higher
* MySQL 5.7.21 or higher
* MySQLi 5+ (Tested with 5.0.12)
* SSL/TLS enabled connection

### How do I get set up? ###

* Clone the repo into the appropriate directory for your web server
* Create a folder named 'config' in the application root directory
* In the 'config' directory create a file named 'db_config.json' and enter something similar to the following:

    ```json
    {
        "mysql_host": "localhost",
        "mysql_user": "root",
        "mysql_password": "sesame",
        "mysql_db": "chess_champions",
        "mysql_port": 3306
    }
    ```

* Execute the script 'docs/db_create_fresh.sql' on your database server