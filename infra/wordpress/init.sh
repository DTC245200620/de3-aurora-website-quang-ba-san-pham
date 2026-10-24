#!/bin/sh
set -eu
cd /var/www/html
if ! wp core is-installed; then
  wp core install --url="$SITE_URL" --title='Aurora — Sản phẩm cho phong cách của bạn' --admin_user="$WP_ADMIN_USER" --admin_password="$(cat /run/secrets/wp_password)" --admin_email="$WP_ADMIN_EMAIL" --skip-email
fi
wp plugin activate aurora-products
wp theme activate aurora
wp option update home "$SITE_URL"
wp option update siteurl "$SITE_URL"
wp rewrite structure '/%postname%/' --hard
wp eval 'aurora_seed();'
