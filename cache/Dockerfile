FROM debian:10

# Update packages
RUN apt-get update
RUN apt-get dist-upgrade -y

# Install REDIS
RUN apt install -y redis-server

# Set entry point, so that servers start, when container is started
# Creates an extra file with database vars
ENTRYPOINT \
redis-server --daemonize yes && \
redis-cli CONFIG SET protected-mode no && \
/bin/bash