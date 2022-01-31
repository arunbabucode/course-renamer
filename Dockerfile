FROM php:8.0-cli-alpine
WORKDIR /usr/src/app
COPY . /usr/src/app
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN cd /usr/src/app && \
    composer install --no-interaction --no-scripts --no-dev

ENTRYPOINT ["/usr/src/app/course-renamer"]
