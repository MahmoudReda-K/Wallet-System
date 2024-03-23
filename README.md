
## Laravel Wallet System

Welcome to the Laravel Wallet System. This system provides users with the ability to manage their funds through various features such as creating wallets, depositing funds, withdrawing funds, transferring money between user accounts, checking wallet balances, and viewing transaction history.

## Requirements

- PHP >= 8.1
- Laravel >= 10.x
- MySQL
- Composer

## Installation

- `git clone https://github.com/MahmoudReda-K/Wallet-System.git`
- `cd wallet-system`
- `composer install`
- `create your database wallet-system`
- `cp .env.example .env`
- `php artisan key:generate`
- `php artisan migrate --seed`
- `php artisan serve`

You can now access the application at http://localhost:8000.

## Usage

#Authentication: 
- Users can register and log in to their accounts to access wallet features. 
#Wallet Management: 
 - Each user gets a wallet upon registration and can manage their funds through deposit, withdrawal, and transfer functionalities. 
#Balance and Transaction History:
- Users can check their current wallet balance and view transaction history to monitor their financial activities.

## API Documentation

You can find the API documentation for this project in the [Postman Documentation](<https://documenter.getpostman.com/view/4099038/2sA35Bb4LC>).
