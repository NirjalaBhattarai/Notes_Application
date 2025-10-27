
# Notes Application

## Project Overview
Notes App is a web application where users can create, edit, and delete notes. It supports categorization and secure authentication using JWT. The app features a responsive design for both desktop and mobile users.

## Technology Stack
- **Backend:** Laravel PHP Framework  
- **Frontend:** HTML, CSS, JavaScript  
- **Database:** SQLite  
- **Authentication:** JWT  
- **Styling:** Custom CSS with responsive design  

## Requirements
- PHP 7.4 or higher  
- Composer  
- SQLite  
- A web server (Apache/Nginx) or PHP built-in server  



## Installation Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/nirjuu/Notes_Application.git
   cd Notes_Application/notes_taking


## Installation Steps

Clone the repository

Clone or download the project files.


Run composer install to install PHP packages.

## Generate application key 

1. Run these 

composer require tymon/jwt-auth


php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

php artisan jwt:secret


## Set up database

Create the SQLite database file

Run migrations: php artisan migrate

## Start the development server

Run php artisan serve.




