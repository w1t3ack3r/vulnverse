FROM php:7.4-apache

# Install packages, sudo, composer dependencies
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    sudo \
    iputils-ping \
    vim \
    git \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip
RUN a2enmod rewrite
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

COPY app/composer.json /var/www/html/composer.json
RUN composer install --no-interaction --no-scripts

RUN touch /var/log/apache2/receipt_sends.log \
    && chown www-data:www-data /var/log/apache2/receipt_sends.log \
    && chmod 664 /var/log/apache2/receipt_sends.log

RUN useradd -ms /bin/bash bayo_dev || true
RUN echo "flag{b4y0_l3ft_h1s_cr3ds_ly1ng_ar0und}" > /home/bayo_dev/user.txt \
    && chown bayo_dev:bayo_dev /home/bayo_dev/user.txt \
    && usermod -aG bayo_dev www-data \
    && chmod 440 /home/bayo_dev/user.txt

RUN echo "flag{d0ck3r_1snt_a_s3cur1ty_b0und4ry}" > /root/root.txt && chmod 400 /root/root.txt

RUN cat <<'BASH' > /usr/local/bin/check_api_status.sh
echo "Pinging external API gateway at $1..."
eval "ping -c 3 $1"
echo "Check complete."
BASH
RUN chmod +x /usr/local/bin/check_api_status.sh

RUN echo "www-data ALL=(ALL) NOPASSWD: /usr/local/bin/check_api_status.sh" > /etc/sudoers.d/www-data-privesc && chmod 0440 /etc/sudoers.d/www-data-privesc

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
