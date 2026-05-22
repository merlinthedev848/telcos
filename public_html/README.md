# TelecomOS public_html Mode

For shared-hosting tests, build the upload folder with:

```bash
php tools/build-shared-hosting-package.php
```

Then upload `build/shared-hosting/public_html` into your host's `public_html` directory.

The generated package includes the root `.htaccess` that protects Laravel internals and routes requests through `public/index.php`.
