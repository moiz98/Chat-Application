# This is Chating App developed on laravel and vue js ,laravel echo and pusher is used for the broadcasting and listening of messages.
this chat application has group and single chats with file sharing and emojies feature.  

To get it up and running, follow steps below:

# 1) Clone this repo, After you clone this project, do the following:
        1.1 -> go into the project
        1.2 -> create a .env file
        1.3 -> copy '.env.example' to '.env' file

# 2) Install composer dependencies
        2.1 -> run command 'composer install'
        2.2 -> run command 'composer update'

# 3) Install npm dependencies
        3.1 -> run command 'npm install'

# 4) Generate a key for your application
        4.1 -> run command 'php artisan key:generate'

# 5) create a local MySQL database
        5.1 -> add the database connection config to your .env file
            DB_CONNECTION=mysql
            DB_DATABASE=<db name>
            DB_USERNAME=root
            DB_PASSWORD=

# 6) Run the migration files to generate the schema/database
        6.1 -> run command 'php artisan migrate'

# 7) Create pusher app and copy the keys into ur .env file
        PUSHER_APP_ID=
        PUSHER_APP_KEY=
        PUSHER_APP_SECRET=
        PUSHER_APP_CLUSTER=

# 8) change the BROADCAST_DRIVER in your .env to pusher
        BROADCAST_DRIVER=pusher

# 9) link storage for file saving stuff
        9.1 -> run command 'php artisan storage:link'

# 10) run webpack and watch for changes
        10.1 -> run command 'npm run watch'

# 11) start your App in other terminal
        11.1 -> run command 'php artisan serve'