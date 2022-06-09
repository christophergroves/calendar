## Used Cars NI Programming Task Pricing


### Technologies

- Laravel 9.x
- jQuery 3
- Boostrap 5


### Setup

- Download or clone the repository from github.
- Create a new database in your MySQL environment (eg. usedcarsni).
- Configure Laravel to use your MySQL database via settings in .env (database,username,password).
- Using Laravel's Artisan command migrate db to your environment (php artisan migrate).
- Using Laravel's Artisan command seed the database (php artisan db:seed).
- Using a browser navigate to '/localhost/usedcarsni/public/'
- The coloured buttons will load the appropriate JSON file from the public folder and send request.
- The API will return a JSON response of the calulated price which will display on the page.


### Assumptions made

Rule 5 Below: I assumed that where rule 5 was concerned the 'last_listed' date used to check if listing is within 30 days of expiry should be taken from storage and not from the request as the request should be compared against a listing that already exists. 



    1. A request in the format of the samples supplied would be made to the API (e.g. /api/calculate)
    2. If the vehicle to be listed is valued at under £1000, the listing is completely free.
    3. If the vehicle is valued between £1000 and £5000, the listing is £7.99
    4. If the vehicle is valued over £5000, the listing is £14.99
    5. If the same vehicle is being listed by the same customer within 30 days of the listing expiring, they receive a 15% discount on the new listing.
    6. If a different vehicle is listed by the same customer at any time, the listing price is standard.
    7. The system should reject any invalid requests with an appropriate error code and message.


### Limitations
- No PHPUnit tests are present. This is due to my lack of knowlege in this area. 
- I ran tests from the front end with JavaScript loading in JSON files and then sending request.
- I used test driven development as much as possible however I do realise the fundamental importance of learning PHPUnit to properly implement a test driven approach. 
- I did not use authentication in the requests.

### Learning
- Learned how to use Laravel migrations.
- Learned how to populate the database using Laravel db:seed