# Use the official Nginx image as the base
FROM nginx:alpine

# Add www-data user to match the PHP container
RUN adduser -D -H -u 82 -s /bin/bash www-data -G www-data

COPY . /var/www
# Copy the custom Nginx configuration
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy the default configuration for virtual hosts
COPY ./docker/nginx/conf.d/ /etc/nginx/conf.d/

# Expose port 80 for the web server
EXPOSE 80
EXPOSE 433
