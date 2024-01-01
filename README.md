## Lumen PHP Framework
RUN composer update

## JWT
RUN php artisan jwt:secret

## CorsMiddleware
EDIT CorsMiddleware

## SUPERVISOR FOR QUEUE (OPTIONAL)
RUN sudo apt install supervisor<br>
copy myapp-worker.conf to /etc/supervisor/conf.d<br>
edit myapp-worker.conf section command, stdout_logfile and user<br>
RUN sudo supervisorctl reread<br>
RUN sudo supervisorctl update<br>
RUN sudo supervisorctl start "myapp-worker:*"