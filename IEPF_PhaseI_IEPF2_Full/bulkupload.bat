@ECHO OFF
ECHO bulk upload  
cd C:\inetpub\wwwroot\iepf
php artisan call:bulkupload
EXIT