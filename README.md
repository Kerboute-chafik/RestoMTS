Hello you can find here a small description on how ze can run the project 
 CONFIG:

    Symfony >= 5.4
    PHP >= 7.4
    Mysql >= 5.7

STEPS:
1) Clone project using git clone command
2) navigate to the directory where you cloned the project 
3) run the command composer install to instlal your dependencies
4) Set up .Env file for ur db conf
5) run symfony console d:m:m to migrate tables to your db
6) run symfony console d:f:l to load fixtures
7) run symfony serve to start you local

API:

1) /api/restaurants (LIST restaurants)
2) /api/login_check (jwt auth)
3) /api/register 
BODY {
   "username": "test@live.com",
   "password": "123456",
   "email": "test@live.com",
   "firstname": "testm",
   "lastname": "tesm"
   ) --- type POST
4) /api/review (
 BODY {
   "message": "example",
   "note": 13,
   "restaurant": "2",
   "user": "test@live.com"
   }
) --- type POST
5) /api/review
BODY {
    "id": 1,
    ) --- type DELETE