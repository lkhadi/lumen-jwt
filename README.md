## Lumen PHP Framework
COPY OR RENAME .env.example to .env<br>
RUN composer update

## JWT
RUN php artisan jwt:secret

## CorsMiddleware
EDIT CorsMiddleware<br>
$allowedOrigins = [getenv("APP_EXTERNAL_URL1"), getenv("APP_EXTERNAL_URL2"), getenv("APP_EXTERNAL_URL3")];

## SUPERVISOR FOR QUEUE (OPTIONAL)
RUN sudo apt install supervisor<br>
copy myapp-worker.conf to /etc/supervisor/conf.d<br>
edit myapp-worker.conf section command, stdout_logfile and user<br>
RUN sudo supervisorctl reread<br>
RUN sudo supervisorctl update<br>
RUN sudo supervisorctl start "myapp-worker:*"

## ENABLE EMAIL (OPTIONAL)
Uncomment these lines in the bootstrap/app.php file.<br>
$app->configure('mail');<br>

$app->alias('mail.manager', Illuminate\Mail\MailManager::class);<br>
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);<br>

$app->alias('mailer', Illuminate\Mail\Mailer::class);<br>
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);<br>
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);<br>
$app->register(Illuminate\Mail\MailServiceProvider::class);<br>