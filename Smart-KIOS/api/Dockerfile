
# Use official PHP Apache image
FROM php:8.0-apache

# Copy all files from the 'api' directory into the container
COPY ./ /var/www/html/

# ติดตั้งและเปิดใช้งาน PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# กำหนดค่าโฮสต์เว็บไซต์ที่อนุญาตให้เข้าถึงไฟล์ PHP
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Upgrade package lists
RUN apt-get update && apt-get upgrade -y



