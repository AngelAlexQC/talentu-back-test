<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Talentu Backend Test

## [View Deplyment](https://talentu-back.herokuapp.com/)

## Requirements
* Linux (Debian, Ubuntu, CentOS, RHEL, WSL)
* PHP up to 8.1
* composer
* Docker version up to 20.10.5
## Deploy Locally

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
```


## Execute Tests

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan test
```
