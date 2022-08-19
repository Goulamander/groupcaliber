FROM debian:9

# Upgrade distribution
RUN apt-get update
RUN apt-get -y dist-upgrade

# Install database server
RUN apt-get install -y mariadb-server apt-utils

# Copy database
COPY database.sql /root/database.sql

# Open for outsite requests
RUN echo "bind-address = 0.0.0.0" >> /etc/mysql/mariadb.conf.d/50-server.cnf

# Start SQL server if not already started
RUN service mysql restart

COPY ./run.sh /root/run.sh
RUN chmod +x /root/run.sh

# Convert executable file to unix format. This is relevant if running on Windows.
# This does not have any effect on Linux or MacOS.
# This installs and removes the dos2unix package for the conversion.
RUN apt -y install dos2unix
RUN dos2unix /root/run.sh
RUN apt -y --purge remove dos2unix

EXPOSE 3306

ENTRYPOINT \
service mysql restart && \
/root/run.sh && \
mysql -e "GRANT ALL PRIVILEGES ON $DATABASE_DATABASENAME.* TO '$DATABASE_USERNAME'@'' IDENTIFIED BY '$DATABASE_PASSWORD';" && \
mysql -e "use mysql; update user set authentication_string=password('$DATABASE_PASSWORD'),plugin='mysql_native_password' where user='$DATABASE_USERNAME';" && \
mysql -e "FLUSH PRIVILEGES;" && \
/bin/bash
