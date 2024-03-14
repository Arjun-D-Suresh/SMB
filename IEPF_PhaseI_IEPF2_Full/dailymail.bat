@ECHO OFF
ECHO daily mail 
cd C:\inetpub\wwwroot\iepf
php artisan dailymail:eod
EXIT