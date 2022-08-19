DATABASE_FILE_NAME_LOCAL=/root/database.sql

if [ -f "$DATABASE_FILE_NAME_LOCAL" ]; then
    echo "$DATABASE_FILE_NAME_LOCAL exist"

    # Create database and user and set credentials
    mysql -e "CREATE DATABASE $DATABASE_DATABASENAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

    # Import database
    echo "Importing database ..."
    mysql $DATABASE_DATABASENAME < $DATABASE_FILE_NAME_LOCAL

    # Remove database file
    rm $DATABASE_FILE_NAME_LOCAL
    echo "database imported."

fi
