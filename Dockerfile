FROM php:8.1-apache

# تثبيت الإضافات اللازمة (اختياري، حسب احتياجات مشروعك)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_mysql

# نسخ ملفات المشروع إلى مجلد الخادم
COPY . /var/www/html

# تمكين وحدة إعادة الكتابة (Rewrite Module) لدعم .htaccess
RUN a2enmod rewrite

# إعداد المنفذ
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]
