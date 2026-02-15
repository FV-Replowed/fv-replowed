# FV Replowed
A revival project for the world's finest Flash farming game

## Requirements
* PHP >= 8.3.x
* MySQL >= 5.x (8.4.3 recommended)
* Game assets from farmville/assets
* A copy of this repo

## Links
* PHP: https://windows.php.net/downloads/releases/archives/
* MySQL: https://downloads.mysql.com/archives/community/
* Laragon: https://laragon.org/download/

## Structure Preparation
* Delete and replace the PHP and MySQL versions within Laragon's bin subfolders.
* From Laragon's main folder, move a copy of this repo into www/fv.
* Within fv, move the game assets into public/farmville/assets.
* Also within fv, rename .env.example to .env.

## Setup
* From Laragon, start and stop all services to initialize data, then change the MySQL root password.
* While there, set display_errors in php.ini to Off, then start all services again.
* Create and log into a SQL session using your new password.
* From the SQL window, create a new DB called farmvilledb. Then, load and run the fv/farmvilledb.sql file.
* Add your DB credentials into .env. 
* Add the same credentials into config.php and database.php within public/farmville/flashservices/Amfphp/Helpers.
* Open a Laragon terminal. Execute cd fv, composer install, php artisan key:generate, and php artisan serve.
* Navigate to the resulting URL in a Flash-enabled browser, register, and play.

## Notes
* Error dialogs popping up for missing assets is expected.
* Alternative data can be located using the assethash file.
* You can open a debug console in-game using F5.
* Later on, you can simply start all services and execute php artisan serve.
* Be careful, as only non-commented lines within php.ini change settings.
* You ahould run Laragon as an administrator at a path without spaces.
* Laragon may prompt you to configure virtual hosts as another way to access the game.

## Credits
* kehayeah: PHP work and reverse engineering
* puccamite.tech: Dehasher development
* rabbetsbigday: Additional technical advising

## Docker

```bash
make assets # if it fails, try sudo make assets
make build # or make no-vnc for headless machines
make run
make migrate
make items
```
