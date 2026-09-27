FROM php:8.2-apache

# ป้องกันปัญหา Apache โหลด MPM ซ้ำซ้อน (AH00534: More than one MPM loaded)
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite headers

# ให้ Apache ส่งต่อตัวแปร Environment ทั้งหมดไปยัง PHP
RUN echo "PassEnv DATABASE_URL MYSQL_URL MYSQL_PRIVATE_URL MYSQLHOST MYSQLPORT MYSQLUSER MYSQLPASSWORD MYSQLDATABASE DB_HOST DB_PORT DB_USER DB_PASS DB_NAME" > /etc/apache2/conf-enabled/railway-env.conf

# ติดตั้ง extensions สำหรับ MySQL (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# กำหนด working directory
WORKDIR /var/www/html

# คัดลอกไฟล์ทั้งหมดเข้า image
COPY . /var/www/html/

# กำหนดสิทธิ์การเข้าถึงไฟล์
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# สคริปต์ Entrypoint เพื่อรองรับ dynamic PORT ของ Railway ($PORT)
RUN echo '#!/bin/sh\n\
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* 2>/dev/null || true\n\
PORT="${PORT:-8080}"\n\
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
sed -i "s/<VirtualHost \\*:80>/<VirtualHost \\*:$PORT>/g" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

CMD ["/usr/local/bin/docker-entrypoint.sh"]
