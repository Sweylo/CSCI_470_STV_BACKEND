# README #

Chess & Conquer Backend Server / API Host

### Software Prerequisites ###
* PHP 7.x
* MySQL 5.7.x

### Suggestions ###
* SSL/TLS enabled connection

### Installation ###
* Clone the repo into the appropriate directory for your web server.
* Create a directory named **config** under the application root directory.
* Create and enter the following (with your info) into the file **config/db_config.json**:

    ```json
    {
        "mysql_host": "localhost",
        "mysql_user": "root",
        "mysql_password": "sesame",
        "mysql_db": "chess_n_conquer",
        "mysql_port": 3306
    }
    ```

* Create and enter the following (with your info) into the file **config/webui_config.json**:

    ```json
    {
        "site_title": "Chess & Conquer",
        "registration_secret": "drink_your_ovaltine"
    }
    ```

* Execute the script **docs/db_create.sql** on your database server to create the database and tables.
* Currently, you must manually manipulate the database to make a user an admin. So, register your admin user and in the database, set the *user_perm_level_id* to **4**.

### Documentation ###

* Check the [wiki](https://github.com/Sweylo/CSCI_470_STV_BACKEND/wiki).
