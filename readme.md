# Starry

Starry is a small package for creating a standard repository system in Laravel(PHP Framework).

## Installation

TODO

```bash
TODO
```

## Usage

### Launch Starry

```bash
php artisan starry:launch
```

This command will launch a basic setup for the starry and publish a config file ```starry.php``` to manage configration of starry package.

Basic setup will create 
```
---App
------Repository
----------------Eloquent(Default Setup)
---------------------------------------BaseRepository.php
----------------StarryInterfaces(Default Setup)
----------------------------------------------EloquentRepositoryInterface.php(Default Setup)
```

## License
[MIT](https://choosealicense.com/licenses/mit/)