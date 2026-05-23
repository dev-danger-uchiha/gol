# Usamos la imagen oficial de PHP ligera que ya incluye el servidor Apache
FROM php:8.2-apache

# Copiamos todos los archivos del directorio actual (tu juego) 
# a la carpeta pública donde Apache lee los sitios web
COPY . /var/www/html/

# Por seguridad y buenas prácticas, le damos los permisos correctos
# al usuario de Apache (www-data) sobre los archivos copiados
RUN chown -R www-data:www-data /var/www/html/

# Exponemos el puerto 80, que es el puerto por defecto para tráfico HTTP
EXPOSE 80