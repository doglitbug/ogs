# ogs
Online Garage Sale website

## Deployment steps

First(1) location in database should be 'Unknown'/'Unset'

### Prevent browser access to index-less folders:
Edit `/etc/apache2/apache2.conf` and remove `Indexes` or change it to `-Indexes`

Alternatively create a new directory block just for the project (allows access for other projects in development)

```
<Directory /var/www/ogs/>
Options FollowSymLinks
AllowOverride None
Require all granted
</Directory>
```

### Increase file size for form uploads
Edit `/etc/php/8.1/apache2/php.ini` and increase the size of `upload_max_filesize` and `post_max_size`

```
; Maximum allowed size for uploaded files.
upload_max_filesize = 5M

; Must be greater than or equal to upload_max_filesize
post_max_size = 5M
```

### Restart server
Restart server:

`sudo service apache2 restart`