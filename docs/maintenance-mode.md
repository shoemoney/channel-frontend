# Maintenance mode

Laravel includes a [maintenance mode](https://laravel.com/docs/8.x/configuration#maintenance-mode), which will display a custom maintenance screen to all website visitors. To enable it, run the following command:

```bash
php artisan down
```

If you will be performing more drastic changes which could cause errors (such as updating Composer dependencies or other infrastructure components) while the site is in maintenance mode, you can pre-render a maintenance mode view that will be returned at the very beginning of the request cycle:

```bash
php artisan down --render="errors::503"
```

To disable maintenance mode run:

```bash
php artisan up
```
