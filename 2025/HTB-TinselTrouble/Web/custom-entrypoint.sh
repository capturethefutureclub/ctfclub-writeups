#!/bin/bash
set -euo pipefail

# Change to WordPress directory
cd /var/www/html || exit 1

# Initialize local database
echo "Initializing local database..."
mkdir -p /var/run/mysqld
chown -R mysql:mysql /var/run/mysqld
mkdir -p /var/lib/mysql
chown -R mysql:mysql /var/lib/mysql

if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Installing MariaDB system tables..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null
fi

# Start MariaDB in the background
echo "Starting MariaDB..."
mysqld_safe --user=mysql --datadir=/var/lib/mysql --skip-syslog &
MYSQL_PID=$!

# Wait for MariaDB to be ready
echo "Waiting for MariaDB to be ready..."
DB_READY=false
for i in {1..60}; do
    if mysqladmin ping --silent; then
        echo "MariaDB is ready!"
        DB_READY=true
        break
    fi
    sleep 1
done

if [ "$DB_READY" = false ]; then
    echo "Timeout waiting for MariaDB to start"
    # Try to show logs if available
    cat /var/lib/mysql/*.err 2>/dev/null || true
    exit 1
fi

# Create database and user if they don't exist
echo "Configuring database..."
mysql -e "CREATE DATABASE IF NOT EXISTS $WORDPRESS_DB_NAME;"
mysql -e "CREATE USER IF NOT EXISTS '$WORDPRESS_DB_USER'@'localhost' IDENTIFIED BY '$WORDPRESS_DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON $WORDPRESS_DB_NAME.* TO '$WORDPRESS_DB_USER'@'localhost';"
mysql -e "CREATE USER IF NOT EXISTS '$WORDPRESS_DB_USER'@'127.0.0.1' IDENTIFIED BY '$WORDPRESS_DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON $WORDPRESS_DB_NAME.* TO '$WORDPRESS_DB_USER'@'127.0.0.1';"
mysql -e "FLUSH PRIVILEGES;"

# Initialize WordPress files
echo "Initializing WordPress files..."
if [ ! -e index.php ] && [ ! -e wp-includes/version.php ]; then
    echo "Copying WordPress files from /usr/src/wordpress..."
    cp -rT /usr/src/wordpress/ .
fi

# Always update custom content from image
echo "Updating custom content..."
cp -r /usr/src/wordpress/wp-content/plugins/my-plugin wp-content/plugins/
cp -r /usr/src/wordpress/wp-content/themes/my-theme wp-content/themes/

if [ ! -f wp-config.php ]; then
    echo "Creating wp-config.php..."
    wp config create \
        --dbname="$WORDPRESS_DB_NAME" \
        --dbuser="$WORDPRESS_DB_USER" \
        --dbpass="$WORDPRESS_DB_PASSWORD" \
        --dbhost="$WORDPRESS_DB_HOST" \
        --allow-root
fi

# Wait for WordPress files to be ready
echo "Waiting for WordPress core files..."
for i in {1..15}; do
    if [ -f wp-config.php ] && [ -d wp-includes ] && [ -f wp-load.php ]; then
        echo "WordPress files are ready!"
        break
    fi
    sleep 1
done

# Brief wait for everything to be ready
sleep 1

# Install WordPress automatically if not already installed
if ! wp core is-installed --allow-root 2>/dev/null; then
    echo "Installing WordPress automatically..."
    
    # Generate random admin password
    ADMIN_PASSWORD=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 20)

    wp core install \
        --url="${WORDPRESS_URL:-http://localhost:1337}" \
        --title="${WORDPRESS_TITLE:-My WordPress Site}" \
        --admin_user="${WORDPRESS_ADMIN_USER:-admin}" \
        --admin_password="$ADMIN_PASSWORD" \
        --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@example.com}" \
        --allow-root \
        --skip-email
    
    echo "========================================="
    echo "WordPress installed successfully!"
    echo "Admin username: ${WORDPRESS_ADMIN_USER:-admin}"
    echo "Admin password: $ADMIN_PASSWORD"
    echo "Site URL: ${WORDPRESS_URL:-http://localhost:1337}"
    echo "========================================="
else
    echo "WordPress is already installed."
fi

# Configure dynamic site URL in wp-config.php
if ! grep -q "WP_HOME" wp-config.php; then
    echo "Configuring dynamic site URL..."
    sed -i "2i if (isset(\$_SERVER['HTTP_HOST'])) { \$proto = (\$_SERVER['HTTPS'] ?? '') === 'on' ? 'https://' : 'http://'; define('WP_HOME', \$proto . \$_SERVER['HTTP_HOST']); define('WP_SITEURL', \$proto . \$_SERVER['HTTP_HOST']); }" wp-config.php
fi

# Activate custom theme and plugin
echo "Activating custom theme and plugin..."
wp theme activate my-theme --allow-root || echo "Failed to activate theme"
wp plugin activate my-plugin --allow-root || echo "Failed to activate plugin"

# Create users
echo "Creating custom users..."
BEA_PASS=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 20)
CHES_PASS=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 20)
LOTTIE_PASS=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 20)
TILDA_PASS=$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 20)

BEA_ID=$(wp user create bea "bea@example.com" --display_name="Beatrix “Bea” Purl" --role=author --user_pass="$BEA_PASS" --porcelain --allow-root || wp user get bea --field=ID --allow-root)
CHES_ID=$(wp user create ches "ches@example.com" --display_name="Chestnut “Ches” Burble" --role=author --user_pass="$CHES_PASS" --porcelain --allow-root || wp user get ches --field=ID --allow-root)
LOTTIE_ID=$(wp user create lottie "lottie@example.com" --display_name="Lottie Thimblewhisk" --role=author --user_pass="$LOTTIE_PASS" --porcelain --allow-root || wp user get lottie --field=ID --allow-root)
TILDA_ID=$(wp user create tilda "tilda@example.com" --display_name="Tilda Baublenose" --role=author --user_pass="$TILDA_PASS" --porcelain --allow-root || wp user get tilda --field=ID --allow-root)

# Create posts
echo "Creating custom posts..."

# Post 1
wp post create --post_title="Unraveling the Festive Threads: Tips for Fixing Those Tangles in the Season's Magic" \
    --post_content="Good evening, Tinselwick! My threads are never wrong—they flutter when the village magic feels a bit pulled apart. Lately, the 'Wish-Wires' have felt particularly knotted. Like a bad stitch that ruins a whole scarf, small snarls can stop the cheer from flowing. I've found that the best way to fix any tangle is to pause, pull gently, and look for the very first, smallest knot. Don't let the mischievous threads distract you; they thrive on confusion. We'll knit peace and sparkle back into the season, one mindful stitch at a time. Ignore the fuss, and focus on the pattern. See you at the Cottage!" \
    --post_author="$BEA_ID" \
    --tags_input="TinselwickTips,WinterWeaving,HolidayMending,KnitterOfSecrets,EverlightEve" \
    --post_status=publish \
    --allow-root

# Post 2
wp post create --post_title="Where Did All the Cocoa Supplies Go? A Scout's Guide to Tracking Treats" \
    --post_content="Scout Ches here! Has anyone noticed the cocoa stock seem to vanish from the North Plaza? Don't fret! My Jingle-Map always has the answers. By tracking the pattern of Mrs. Blummel’s delivery cart and the faint trail of powdered sugar, I can confirm the cocoa wasn't lost—it was simply relocated to the warmest spot behind the bakery. It’s all about observation: no detail is too small, from the placement of a ribbon to the tracks of a runaway marshmallow. Always log everything, and you'll find that nothing in Tinselwick truly disappears, it just moves to its proper place. Let's keep charting the joy!" \
    --post_author="$CHES_ID" \
    --tags_input="ChesCharts,ScoutTracking,TrackTheTreats,TinselwickMaps,MarshmallowBoots" \
    --post_status=publish \
    --allow-root

# Post 3
wp post create --post_title="Sweet Riddle: Decoding the Sugar Secrets in Your Cocoa Cup" \
    --post_content="Every mug of hot cocoa is a puzzle waiting to be solved. Did you know the pattern of cream and the swirl of candy canes can sometimes spell a secret message? I've been working with my cipherwheel and discovered a new riddle hidden in the peppermint swirls this morning: 'The Star sees but does not sleep.' What do you think the shifting letters are trying to tell us? The best secrets are always written in treats. Leave your clever guesses in the comments (but whisper them softly!). Let's use our wits to unlock the true meaning of the season's sweet mysteries. Happy decoding!" \
    --post_author="$LOTTIE_ID" \
    --tags_input="LottieRiddles,RiddleCookie,PeppermintCipher,UnravelTheWarmth,TinselwickSecrets" \
    --post_status=publish \
    --allow-root

# Post 4
wp post create --post_title="The Festival Lights: Making Sure the Magic Always Returns" \
    --post_content="The Festival lights run on a precise, beautiful logic, but even magic can get a little mixed up. When a system finds a line of sadness, it sometimes tries to reset itself, going back to a perfect, happier moment. That’s why we need to make sure the final instruction in the village’s spell is always clear and warm. My job is to write that logic so the cheer never fails. Today, I'm reminding everyone of the most important command: return joy(); Your task: Perform a random act of kindness. That is the perfect way to compile the magic and ensure the lights of Tinselwick flicker with pure warmth. Let's make sure the magic always comes home." \
    --post_author="$TILDA_ID" \
    --tags_input="TildaLogic,FestivalScripts,CodingKindness,ReturnJoy,BaubleWarmth" \
    --post_status=publish \
    --allow-root

# Ensure wp-content is writable
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/wp-content

# Start Apache in foreground
echo "Starting Apache server..."
exec /usr/local/bin/docker-entrypoint.sh apache2-foreground

