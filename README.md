# BookStorm
Build a backend service and a database for a book store platform

## How to use
1. git clone https://github.com/pcreem/bookStorm.git
2. composer install 
3. fill .env variable
4. run 
```$php -S 127.0.0.1:8000 index.php```

## Dataset
1. Book Store data: [Data/book_store_data.json](Data/book_store_data.json)
2. User data: [Data/user_data.json](Data/user_data.json)

## Design
![Alt Text](https://dev-to-uploads.s3.amazonaws.com/uploads/articles/kljwmcjweedrcm4605qw.png)

## API document

| Description | Router |
| ------------- |:-------------:|
| List all book stores that are open at a certain datetime     | GET /store/storesOpenAt?time=9:30     |
| List all book stores that are open on a day of the week, at a certain time | GET /store/storesOpenOnDayAt?day=Mon&time=10:00 |
|List all book stores that are open for more or less than x hours per day|GET /store/storesOpenMoreThanHoursPerDay?hour=9|
|List all book stores that are open for more or less than x hours per week|GET /store/storesOpenMoreThanHoursPerWeek?hour=9|
|List all book stores that have more or less than x number of books|GET /store/storesHaveMoreXnumberBooks?x=9 |
|List all book stores that have more or less than x number of books|GET /store/storesHaveLessYnumberBooks?y=9|
|List all book stores that have more or less than x number of books within a price range|GET /store/storesHaveMoreXnumberBooksPriceBetween?x=9&lowPri=3.00&highPri=9.00|
|List all book stores that have more or less than x number of books within a price range|GET /store/storesHaveLessYnumberBooksPriceBetween?y=9&lowPri=3.00&highPri=9.00|
|Search for book stores or books by name, ranked by relevance to search term|GET /store/searchStores?searchTerm=th|
|Search for book stores or books by name, ranked by relevance to search term|GET |
|The total number and dollar value of transactions that happened within a date range| GET /totalNumAmountWithinDate?y=9&startDate=2020-01-01&endDate=2020-04-30|
|The most popular book stores by transaction volume, either by number of transactions or transaction dollar value|GET /store/topStoreRankByAmount?|
|The most popular book stores by transaction volume, either by number of transactions or transaction dollar value|GET /store/topStoreRankByTrasactTimes?|
|Total number of users who made transactions above or below $v within a date range|GET /user/usersAmountMoreWithinDate?amount=3&startDate=2020-01-01&endDate=2020-09-30|
|Total number of users who made transactions above or below $v within a date range|GET /user/usersAmountLessWithinDate?amount=12.00&startDate=2020-01-01&endDate=2020-09-30 |
|The top x users by total transaction amount within a date range | GET /user/usersAmountMoreWithinDate?amount=3&startDate=2020-01-01&endDate=2020-09-30 |
|List all books that are within a price range, sorted by price |GET /book/booksPriceBetween?lowPri=4.30&highPri=9.00|

* Edit book store name, book name, book price and user name
```
PUT http://127.0.0.1:8000/store/updateStoreName HTTP/1.1
content-type:application/json
{
    "storeId":"1", 
    "storeName":"test"
}
PUT http://127.0.0.1:8000/book/updateBookName HTTP/1.1
content-type:application/json
{
    "bookId":"106", 
    "bookName":"106test"
}
PUT http://127.0.0.1:8000/book/updateBookPrice HTTP/1.1
content-type:application/json
{
    "bookId":"106", 
    "bookPrice":"1.20"
}
PUT http://127.0.0.1:8000/user/updateUserName HTTP/1.1
content-type:application/json
{
    "userId":"1", 
    "userName":"test"
}
```

* Process a user purchasing a book from a book store, handling all relevant data changes in an atomic transaction
```
POST http://127.0.0.1:8000/user/userPurchaseOneBook HTTP/1.1
content-type:application/json
{
    "userId":"3", 
    "storeId":"1",
    "bookId":"3"
}
```

## Reference
https://github.com/aiueoH/book_storm