FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    imagemagick \
    ghostscript \
    libmariadb-dev \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN POLICY_PATH=$(find /etc/ImageMagick* -name policy.xml) && \
    sed -i 's/domain="coder" rights="none" pattern="PDF"/domain="coder" rights="read|write" pattern="PDF"/' "$POLICY_PATH"

RUN echo "upload_max_filesize=20M\npost_max_size=20M\nmemory_limit=256M" > /usr/local/etc/php/conf.d/uploads.ini