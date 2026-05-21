# Ilhaam Mission

## Project Structure

- `app/Config` - database and environment configuration.
- `app/Http` - request handlers used by public entry points.
- `app/Pages` - page implementations grouped by public page/feature.
- `public/assets/js` - browser JavaScript assets.
- `storage/logs` - runtime log files.

Page URLs are routed by `.htaccess` to the matching `app/Pages/<page>/index.php` file. Root PHP files are kept only for backend endpoints and shared configuration.
