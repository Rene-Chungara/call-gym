#!/bin/sh

# Navega a la carpeta donde nginx sirve los archivos
cd /usr/share/nginx/html/assets

# Sobrescribe env.js con las variables de entorno reales
# Nota: Usamos los nombres exactos que tienes en Azure (producturl, reporturl, etc.)
echo "window._env_ = {" > env.js
echo "  API_PRODUCT_URL: '${producturl}'," >> env.js
echo "  API_REPORT_URL: '${reporturl}'," >> env.js
echo "  API_SALES_URL: '${salesurl}'," >> env.js
echo "  API_USER_URL: '${userurl}'" >> env.js
echo "};" >> env.js

# Inicia Nginx
nginx -g "daemon off;"