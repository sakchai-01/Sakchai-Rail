FROM php:8.2-apache

# ติดตั้ง extensions ที่จำเป็นสำหรับเชื่อมต่อ MySQL (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# เปิดใช้งาน mod_rewrite และ headers สำหรับ Apache
RUN a2enmod rewrite headers

# กำหนด working directory
WORKDIR /var/www/html

# คัดลอกไฟล์ทั้งหมดเข้า image
COPY . /var/www/html/

# กำหนดสิทธิ์การเข้าถึงไฟล์
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# สคริปต์ Entrypoint เพื่อรองรับ dynamic PORT ของ Railway ($PORT)
RUN echo '#!/bin/sh\n\
PORT="${PORT:-80}"\n\
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80 (Railway จะ override ด้วยตัวแปร $PORT ให้อัตโนมัติ)
EXPOSE 80

CMD ["/usr/local/bin/docker-entrypoint.sh"]
