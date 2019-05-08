# Installation
* clone the repository
* run `composer update` in the projects root directory
* make sure to set the websites root directory to the public folder (see example nginx config)
* access the install.php in your browser, or copy and adjust the `-sample.php` files in `core/config`. Make sure to **remove or move the install.php file** after the setup is done.

```
server {

  ...
  server_name cloud.example.com;
  root /var/www/cloud/public;
  
  rewrite ^/f/(.*)$         /file.php?token=$1 last;
  rewrite ^/download/(.*)$  /download.php?token=$1 last;
  
  ...
}
```
