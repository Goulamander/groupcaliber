FROM debian:10.11-slim

# IMPORTANT: DOCKER CONTAINER MUST RUN WITH --device /dev/fuse --cap-add SYS_ADMIN for sshfs to work

# Update packages
RUN apt-get update
RUN apt-get dist-upgrade -y

# Copy the database and add import entries to init script
#COPY ./databasename.sql /root/databasename.sql

# Copy and run init script
COPY ./init.sh /root/script.sh

# Convert file to unix. This is relevant if running on Windows.
# This does not have any effect on Linux or MacOS.
# This installs and removes the dos2unix package for the conversion.
RUN apt -y install dos2unix
RUN dos2unix /root/script.sh
RUN apt -y --purge remove dos2unix

RUN chmod +x /root/script.sh
RUN /root/script.sh
RUN rm /root/script.sh

# Install SSHFS for files on different server
RUN apt -y install sshfs
RUN echo 'user_allow_other' >> /etc/fuse.conf

RUN apt install -y php-redis
RUN apt install -y libphp-phpmailer
#RUN debconf-set-selections <<< "postfix postfix/mailname string portal.groupcaliber.com"
#RUN debconf-set-selections <<< "postfix postfix/main_mailer_type string 'Internet Site'"
#RUN echo "postfix postfix/mailname string portal.groupcaliber.com" | debconf-set-selections &&\
#            echo "postfix postfix/main_mailer_type string 'Internet Site'" | debconf-set-selections &&\
#            apt-get install -y mailutils

RUN mv /var/www/html/index.html /var/www/html/index.org.html
COPY ./portal /var/www/html

# Create logost folder for sshfs
RUN mkdir /var/www/html/images/logos
RUN mkdir /var/www/html/images/slides
RUN mkdir /var/www/html/exports

# Copy and run run script
COPY ./run.sh /root/run.sh

# Convert file to unix. This is relevant if running on Windows.
# This does not have any effect on Linux or MacOS.
# This installs and removes the dos2unix package for the conversion.
RUN apt -y install dos2unix
RUN dos2unix /root/run.sh
RUN apt -y --purge remove dos2unix

RUN chmod +x /root/run.sh

# Open port 22 for ssh access
RUN echo "Port 22" >> /etc/ssh/sshd_config
RUN echo "PasswordAuthentication yes" >> /etc/ssh/sshd_config

RUN service ssh restart

#### USER ####
RUN groupadd -r user && useradd -r -g user user

RUN chown -R user /var/www/html
RUN chown -R www-data /var/www/html/logs

# Set export script as executable
RUN chmod +x var/www/html/backend/shell/export.sh

# Add identification for connection with files - only used in development
# In production another file form a volume will be used through an environment variable
COPY ./ssh/id_rsa /home/user/.ssh/id_rsa

# Convert file to unix. This is relevant if running on Windows.
# This does not have any effect on Linux or MacOS.
# This installs and removes the dos2unix package for the conversion.
RUN apt -y install dos2unix
RUN dos2unix /home/user/.ssh/id_rsa
RUN apt -y --purge remove dos2unix

# Change mode of key file, to allow ssh operation.
RUN chmod 400 /home/user/.ssh/id_rsa

RUN echo "export SERVER_ID=undefined" > /etc/environment
RUN echo "export BUILD_DATE_TIME='"`date`"'" > /etc/environment2
RUN echo ". /etc/environment" >> /etc/apache2/envvars
RUN echo ". /etc/environment2" >> /etc/apache2/envvars

# Install cron
RUN apt-get install -y cron

# Copy cron file to the cron.d directory
COPY cron.txt /etc/cron.d/docker-cron

# Convert file to unix. This is relevant if running on Windows.
# This does not have any effect on Linux or MacOS.
# This installs and removes the dos2unix package for the conversion.
RUN apt -y install dos2unix
RUN dos2unix /etc/cron.d/docker-cron
RUN apt -y --purge remove dos2unix

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/docker-cron

# Apply cron job
RUN crontab /etc/cron.d/docker-cron

RUN touch /var/log/cron.log

# hide the direct access to the folder structure and adds a login to the exports folder
RUN mkdir /etc/apache2/htpasswd
COPY .htpasswd etc/apache2/htpasswd/.htpasswd
COPY apache2.conf etc/apache2/apache2.conf

# Set entry point, so that servers start, when container is started
# Creates an extra file with database vars
ENTRYPOINT \
service ssh restart && \
# sshfs -o StrictHostKeyChecking=no,nonempty,allow_other,port=$FILES_PORT,IdentityFile=$APPLICATION_SSH_IDENTITY_FILE user@$FILES_URL:/home/user/logos /var/www/html/images/logos && \
# sshfs -o StrictHostKeyChecking=no,nonempty,allow_other,port=$FILES_PORT,IdentityFile=$APPLICATION_SSH_IDENTITY_FILE user@$FILES_URL:/home/user/slides /var/www/html/images/slides && \
sshfs -o StrictHostKeyChecking=no,nonempty,allow_other,port=$FILES_PORT,IdentityFile=$APPLICATION_SSH_IDENTITY_FILE user@$FILES_URL:/home/user/exports /var/www/html/exports && \
echo "export SERVER_ID="$SERVER > /etc/environment && \
echo "export DB_URL="$DB_URL >> /etc/environment && \
echo "export DATABASE_URL="$DATABASE_URL >> /etc/environment && \
echo "export DATABASE_USERNAME="$DATABASE_USERNAME >> /etc/environment && \
echo "export DATABASE_PASSWORD="$DATABASE_PASSWORD >> /etc/environment && \
echo "export DATABASE_DATABASENAME="$DATABASE_DATABASENAME >> /etc/environment && \
echo "export DATABASE_PORT="$DATABASE_PORT >> /etc/environment && \
echo "export CACHE_URL="$CACHE_URL >> /etc/environment && \
echo "export CACHE_PORT="$CACHE_PORT >> /etc/environment && \
echo "export ENVIRONMENT="$ENVIRONMENT >> /etc/environment && \
echo "export SMTP_USERNAME="$SMTP_USERNAME >> /etc/environment && \
echo "export SMTP_PASSWORD="$SMTP_PASSWORD >> /etc/environment && \
echo "export SMTP_HOST="$SMTP_HOST >> /etc/environment && \
echo "export SMTP_PORT="$SMTP_PORT >> /etc/environment && \
echo "export SMTP_SENDER="$SMTP_SENDER >> /etc/environment && \
echo "export SMTP_REPLY_TO="$SMTP_REPLY_TO >> /etc/environment && \
echo user:$APPLICATION_PASSWORD | chpasswd && \
service apache2 restart && \
service ssh restart && \
service cron restart && \
/root/run.sh && \
/bin/bash
