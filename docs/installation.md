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
cd html

# base
git clone https://github.com/raffaelj/CpMultiplane.git .
git clone https://github.com/agentejo/cockpit.git cockpit

# install addons
git clone https://github.com/raffaelj/cockpit_CpMultiplaneBundle.git cockpit/addons/CpMultiplaneBundle
git clone https://github.com/raffaelj/cockpit_BootManager.git cockpit/addons/BootManager
git clone https://github.com/pauloamgomes/CockpitCms-EditorFormats.git cockpit/addons/BootManager/addons/EditorFormats

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

[1]: https://github.com/agentejo/cockpit/#installation
