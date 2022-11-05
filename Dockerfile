# docker build -t raffaelj/cpmultiplane:latest .

# This image should be available at https://hub.docker.com/r/raffaelj/cpmultiplane

# https://hub.docker.com/r/raffaelj/php8.0-apache-dev
# https://github.com/raffaelj/dockerfiles/tree/master/php8.0-apache-dev
FROM raffaelj/php8.0-apache-dev

# Possible arguments are "next", "master" or version tags e. g. "0.12.2"
# "latest" is not possible. This would require to switch from pure git tags to Github Releases.
ARG COCKPIT_VERSION="next"

# You can rename the cockpit folder to e. g. "admin" while building the image
# docker build --build-arg COCKPIT_DIR=admin -t raffaelj/cpmultiplane:custom .
ARG COCKPIT_DIR="cockpit"

# copy CpMultiplane source files to docs root
# I have a lot of custom files and folders in my local dev folder, so I decided to use a whitelist
# instead of a .dockerignore file with the command `COPY . /var/www/html`
COPY .htaccess.dist /var/www/html/.htaccess
COPY bootstrap.php CHANGELOG.md favicon.png index.php LICENSE mp package.json README.md /var/www/html/
COPY modules/ /var/www/html/modules/

# download and unzip cockpit
RUN wget -q https://github.com/agentejo/cockpit/archive/${COCKPIT_VERSION}.zip -O /tmp/cockpit.zip \
    && unzip -q /tmp/cockpit.zip -d /tmp/ \
    && rm /tmp/cockpit.zip

# copy everything except dot files, than copy .htaccess, than remove tmp folder
RUN mkdir /var/www/html/${COCKPIT_DIR} \
    && mv /tmp/cockpit-${COCKPIT_VERSION}/* /var/www/html/${COCKPIT_DIR}/ \
    && mv /tmp/cockpit-${COCKPIT_VERSION}/.htaccess /var/www/html/${COCKPIT_DIR}/.htaccess \
    && rm -R /tmp/cockpit-${COCKPIT_VERSION}/

# create defines.php if COCKPIT_DIR !== 'cockpit'
RUN if [ "$COCKPIT_DIR" != "cockpit" ]; \
    then echo "<?php define('MP_ADMINFOLDER', '$COCKPIT_DIR');" > defines.php; fi

# run quickstart routine to create admin account, install addons, copy definition files
RUN ./mp multiplane/quickstart --template basic

# create dummy data from README files of installed addons
RUN ./mp multiplane/create-dummy-data

# change ownership from root to apache user
RUN chown -R www-data:www-data /var/www/html

# run apache
CMD ["apache2-foreground"]
