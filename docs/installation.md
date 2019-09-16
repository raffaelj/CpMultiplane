# CpMultiplane - Installation


## Requirements

* PHP >= 7.0
* PDO + SQLite (or MongoDB)
* GD extension
* mod_rewrite, mod_versions enabled (on apache)

make also sure that `$_SERVER['DOCUMENT_ROOT']` exists and is set correctly.

## Installation

1. Download CpMultiplane and put all files in the root of your web project (later called: `MP_DOCS_ROOT`).
2. Download Cockpit and put the cockpit folder in a subdirectory of the root of your web project - `MP_DOCS_ROOT/cockpit`
3. Follow the [installation instructions of Cockpit][1].
    1. Make sure that the __/cockpit/storage__ folder and all its subfolders are writable
    2. Go to __/cockpit/install__ via Browser
    3. You're ready to use Cockpit :-)
4. Install optional cockpit addons
5. Create a collection "pages" with these fields:
  * to do...
6. Adjust your settings
7. Add your content
8. Change everything, if you don't like the defaults

Now login with admin/admin, change your password and start your work.

## Fast CLI installation

**Don't copy and paste everything!** Read it, understand and modify it to your needs.

```bash
# cd into docs root
cd ~/html
```

```bash
# clone CpMultiplane
git clone https://github.com/raffaelj/CpMultiplane.git .
cp .htaccess.dist .htaccess

# clone Cockpit
git clone https://github.com/agentejo/cockpit.git cockpit

# install addons
git clone https://github.com/raffaelj/cockpit_CpMultiplaneGUI.git cockpit/addons/CpMultiplaneGUI
git clone https://github.com/raffaelj/cockpit_FormValidation.git cockpit/addons/FormValidation
git clone https://github.com/raffaelj/cockpit_rljUtils.git cockpit/addons/rljUtils
git clone https://github.com/raffaelj/cockpit_SimpleImageFixBlackBackgrounds.git cockpit/addons/SimpleImageFixBlackBackgrounds
git clone https://github.com/raffaelj/cockpit_UniqueSlugs.git cockpit/addons/UniqueSlugs
git clone https://github.com/raffaelj/cockpit_VideoLinkField.git cockpit/addons/VideoLinkField

git clone https://github.com/raffaelj/cockpit_ImageResize.git cockpit/addons/ImageResize
git clone https://github.com/pauloamgomes/CockpitCms-EditorFormats.git cockpit/addons/EditorFormats

# check for dependencies
# to do: implement cli commands from Monoplane
#./mp check

# Use other cli commands to import collections or singletons
# All cockpit cli commands should work with `./mp`, but you can call `cockpit/cp` instead, too.

# create cockpit config dir
mkdir -p cockpit/config

# write config file
cat > cockpit/config/config.yaml <<EOF
app.name: CpMultiplane

i18n: en
languages:
    default: English
    de: Deutsch

unique_slugs:
    collections:
        pages: title
        posts: title
    localize:
        pages: title
        posts: title

multiplane:
    slugName: slug
    isMultilingual: true
    preRenderFields: ["content", "excerpt"]
EOF

# i18n
mkdir -p cockpit/config/cockpit/i18n
wget -O cockpit/config/cockpit/i18n/de.php https://raw.githubusercontent.com/agentejo/cockpit-i18n/master/de.php

# CpMultiplane i18n
mkdir -p config/i18n
cat > config/i18n/de.php <<EOF
<?php return [
    'Page not found' => 'Seite nicht auffindbar',
    'Something went wrong. This site doesn\'t exist.' => 'Etwas ist schiefgegangen. Diese Seite existiert nicht.',
    'back to start page' => 'Zurück zur Startseite',
    'built with' => 'erstellt mit',
    'since' => 'seit',
];
EOF

# This is the last step, because it requires a user input. 

# create admin user, type a email, a password and press Enter
# to do: implement cli commands from Monoplane
# ./mp account/create --user raffael --name Raffael
```

## Installation with more comfort

It is a bit annoying to update local and production environments if the data lays all over the place. Wouldn't it be nice to have a single folder for all addons, themes, config files, uploads and SQLite databases?

```bash
# cd into docs root
cd ~/html
```

```bash
# clone CpMultiplane
git clone https://github.com/raffaelj/CpMultiplane.git .
cp .htaccess.dist .htaccess

# clone Cockpit
git clone https://github.com/agentejo/cockpit.git cockpit

# create data dir
mkdir data
mkdir data/cp
mkdir data/mp

# change environment routes
cat > cockpit/defines.php <<EOF
<?php
define('COCKPIT_ENV_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', realpath(__DIR__.'/../data/cp')));
EOF

cat > defines.php <<EOF
<?php
define('MP_ENV_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', realpath(__DIR__.'/data/mp')));
EOF

# copy storage folder and remove .gitignore files
cp -r cockpit/storage data/cp
find data/cp/storage/ -name .gitignore -exec rm {} +

# install addons
git clone https://github.com/raffaelj/cockpit_CpMultiplaneGUI.git data/cp/addons/CpMultiplaneGUI
git clone https://github.com/raffaelj/cockpit_FormValidation.git data/cp/addons/FormValidation
git clone https://github.com/raffaelj/cockpit_rljUtils.git data/cp/addons/rljUtils
git clone https://github.com/raffaelj/cockpit_SimpleImageFixBlackBackgrounds.git data/cp/addons/SimpleImageFixBlackBackgrounds
git clone https://github.com/raffaelj/cockpit_UniqueSlugs.git data/cp/addons/UniqueSlugs
git clone https://github.com/raffaelj/cockpit_VideoLinkField.git data/cp/addons/VideoLinkField

git clone https://github.com/raffaelj/cockpit_ImageResize.git data/cp/addons/ImageResize
git clone https://github.com/pauloamgomes/CockpitCms-EditorFormats.git data/cp/addons/EditorFormats

# create admin user, type a email, a password and press Enter
./mp account/create --user raffael --name Raffael
```

```bash
cd data

# local
# git init

# production
# update: git pull
git clone https://github.com/raffaelj/my_private_repository_with_project_files.git .
```

Now you only have to keep track of

```
data
defines.php
cockpit/defines.php
```

Now create your config file(s) and/or adjust your settings via UI, create pages and posts collections and start publishing your content :-)

[1]: https://github.com/agentejo/cockpit/#installation
