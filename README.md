composer i





create db 
name=auction
env


DB_CONNECTION=mysql         
DB_HOST=127.0.0.1           
DB_PORT=3306        
DB_DATABASE=auction          
DB_USERNAME=root       
DB_PASSWORD=         



php artisan migrate
php artisan db:seed

create virtual host with name auction.loc 