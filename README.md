# Calendar

 - A Calendar using FullCalendar javascript event calendar with PHP backend processing and MySql storage. 
 - This was built to track user participation within a time limited programme. A user agreed upon an activity (training, work placement etc.) and time spent on this activity was tracked via the calendar. 
 - This calendar is currently being separated off from a bigger project to become a standalone code example and is under heavy development, refactoring and clean up of the code base thus there are many parts that are not yet operational.
 - User authorisation middleware yet to be implemented.
 
 
## Technologies Used

- Laravel 9.x
- FullCalendar  [https://fullcalendar.io/](https://fullcalendar.io/)
- jQuery 
- jQueryUI
- Boostrap CSS framework
- Bootstrap dialog  [https://nakupanda.github.io/bootstrap3-dialog/](https://nakupanda.github.io/bootstrap3-dialog/)
- Select2 [https://select2.org/](https://select2.org/)


## Setup

1. Clone the repository from github.
2. Create a new database in your MySQL environment.
3. Configure Laravel to use your MySQL database in .env e.g.
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=calendar
    DB_USERNAME=root
    DB_PASSWORD=
    ```
#### Run the following commands in your terminal:

4. Migrate db to your environment:
    ```bash
    $ php artisan migrate
    ```

5. Seed the database:
    ```bash
    $ php artisan db:seed
    ```
6. Navigate your browser to 
    ```bash
    /calendar/public/
    ```


## Tests

- A few preliminary tests have been created in SessionTest, run them with command

    ```bash
    $ php artisan test --filter SessionTest
    ```
