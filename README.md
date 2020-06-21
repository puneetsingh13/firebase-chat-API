# MilliPixels-firebase-chat-API
Laravel - Firebase - Realtime Database

# Setup Firebase
- Create Project without hosting
- Create a Database - Realtime Database in Test Mode.
- Download service-account-key json file from Project > Setting > Service Account.
- Click on "Generate New Private Key" button.
- 

# Composer Command
- composer update


# Migration Command
- php artisan migrate


# Passport Create Encrption Keys
- php artisan passport:install


# Database Seeder
- php artisan db:seed


# API Flow:
- Login: User Authenticate with Email/Password to generate access token.
- User Listing: Get listing of all users.
- Post Message: Send Message to reciever.
- Messages: Get All message between Sender and Reciever User.


# API: Postman Collection link with Example
- https://www.getpostman.com/collections/085501042563673f5a47

