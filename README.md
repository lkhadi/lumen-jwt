## Lumen PHP Framework
RUN composer update

## JWT
RUN php artisan jwt:secret

## CorsMiddleware
EDIT CorsMiddleware
$allowedOrigins = [getenv("APP_EXTERNAL_URL1"), getenv("APP_EXTERNAL_URL2"), getenv("APP_EXTERNAL_URL3")];

## SUPERVISOR FOR QUEUE (OPTIONAL)
RUN sudo apt install supervisor<br>
copy myapp-worker.conf to /etc/supervisor/conf.d<br>
edit myapp-worker.conf section command, stdout_logfile and user<br>
RUN sudo supervisorctl reread<br>
RUN sudo supervisorctl update<br>
RUN sudo supervisorctl start "myapp-worker:*"

## ENABLE EMAIL (OPTIONAL)
Uncomment these lines in the bootstrap/app.php file.
$app->configure('mail');

$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);
$app->register(Illuminate\Mail\MailServiceProvider::class);