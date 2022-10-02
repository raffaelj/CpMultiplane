# CpMultiplane

A small PHP front end for the fast and headless [Cockpit CMS][1].

[Docs][19] (work in progress), [i18n][21]

---

**CpMultiplane is not compatible with Cockpit CMS v2.**

See also [Cockpit CMS v1 docs](https://v1.getcockpit.com/documentation), [Cockpit CMS v1 repo](https://github.com/agentejo/cockpit) and [Cockpit CMS v2 docs](https://getcockpit.com/documentation/), [Cockpit CMS v2 repo](https://github.com/Cockpit-HQ/Cockpit).

---

**My main goals:**

1. privacy by design and privacy by default
2. developer friendliness
  * no plugins to deactivate half of the core features needed
  * ability to adjust everything
3. clean and structured backend for my clients - Cockpit CMS with addons and modifications
4. structured data - keep the system and the data portable and future proof
5. modular, small and reusable code
6. semantic html, responsive css, *usable* without javascript
6. multilingualism by design

CpMultiplane is no classic Cockpit addon. It uses Cockpit as a library, registers `multiplane` as a new module and than uses cockpit's core features. The backend still works as a standalone tool to manage and structure data.

It is the refactored version of [Monoplane][8], which is not maintained anymore.

## Requirements

* PHP >= 7.1
* PDO + SQLite (or MongoDB)
* GD extension
* pecl intl extension (optional)
* mod_rewrite, mod_versions enabled (on apache)

Make also sure that `$_SERVER['DOCUMENT_ROOT']` exists and is set correctly.

You can find the detailed version and a cli install example in [docs/installation][20].

## Installation

### manually

* copy all files of this repository into your web root
* copy `.htaccess.dist` to `.htaccess`
* copy Cockpit in a subfolder of your web root and name it `cockpit`
* copy additional addons, create your collections, adjust some settings

### via git

```bash
cd ~/html
git clone https://github.com/raffaelj/CpMultiplane.git .
cp .htaccess.dist .htaccess
git clone https://github.com/agentejo/cockpit.git cockpit
git clone https://github.com/raffaelj/cockpit_CpMultiplaneGUI.git cockpit/addons/CpMultiplaneGUI
git clone https://github.com/raffaelj/cockpit_FormValidation.git cockpit/addons/FormValidation
git clone https://github.com/raffaelj/cockpit_UniqueSlugs.git cockpit/addons/UniqueSlugs
```

### via composer

```bash
cd ~/html
composer create-project --ignore-platform-reqs raffaelj/cpmultiplane .
```

If you use composer, Cockpit and the addons CpMultiplaneGUI, FormValidation and UniqueSlugs are installed automatically.

### via docker

The [docker image][22] comes preinstalled with the quickstart routine of the "basic" template, with a default admin user (password: admin) and with dummy data from installed addons.

This is not meant for production use, but for local development.

```bash
docker pull raffaelj/cpmultiplane
docker run --rm -d --name cpmultiplane -p 8080:80 raffaelj/cpmultiplane
```

Now open your browser on `localhost:8080` and see it in action.

## Features

* pages and sub pages (e. g. posts)
* multilingual with language prefix, e. g.: `example.com/en/my-page`
* 2 modes for structured content
  1. one collection per content type, e. g. a collection named `pages` and a collection named `posts`
  2. a single collection named `pages` - each entry has a type `page` or `post` (**experimental**)
* maintenance mode with option for allowed ips
* simple content preview while editing pages
* two basic responsive themes with scss files
* simple privacy notice banner, that gets enabled when clicking on video link
* contact forms - fully functional without javascript
* pre-rendering of markdown fields
* multiple ways to change everything
* GUI via [CpMultiplaneGUI addon][2]
* full-text search
* ...

## Recommended Addons

Install these addons in `cockpit/addons/`.

* [CpMultiplaneGUI][2]
  * adds a few fields to the sidebar, so you don't have to define them in your collection definitions
  * some gui tweaks for easier access
  * work in progress...
* [UniqueSlugs][3]
  * If links should point to `slug` instead of `_id`
  * for multilingual slugs in language switch
* [rljUtils][4]
  * fixes security issues in Admin UI for multi user setups
  * big language buttons for multilingual setups
* [FormValidation][5]
  * The inbuilt Forms Controller requires field definitions from this addon
  * The inbuilt views and css files are written to match the field definitions
* [VideoLinkField][6]
  * inbuilt `/assets/js/mp.js`, some views and css files are designed to load videos privacy friendly with a privacy notice, that pops up only when a user clicks a play button
* [SimpleImageFixBlackBackgrounds][7]
  * replaces the SimpleImage library with a modified version to fix black backgrounds of transparent png and gif files on hosts with a non-bundled PHP GD version
* [EditorFormats][10] - if you want to give your users a Wysiwyg field

## Intended use

### Backend - Cockpit

1. Create a singleton `site` for your default page definitions.
2. Create a collection `pages` for all of your pages.
3. Create a collection `posts` for all of your blog posts.
4. Use the CpMultiplaneGUI addon.

### Frontend - CpMultiplane

1. create a child theme of rljbase or create your own theme
2. adjust defaults in `/child-theme/config/config.php`
3. add snippets to `/child-theme/bootstrap.php`, that are explicitly for your theme
4. add snippets to `/config/bootstrap.php`, that are specifically for your setup
5. change some partials to fit your needs

## Settings

The fastest way to change some defaults, is to add some values to `/cockpit/config/config.php`:

```php
<?php
return [
    'app.name' => 'CpMultiplane',

    'i18n' => 'en',
    'languages' => [
        'default' => 'English',
        'de' => 'Deutsch',
    ],

    // define settings here
    'multiplane' => [
        'pages' => 'pages',
        'siteSingleton' => 'site',
        'slugName' => 'slug',
        'use' => [
            'collections' => [
                'pages',
                'posts',
                'products',
            ],
            'singletons' => [
                'site',
            ],
            'forms' => [
                'contact',
            ],
        ],
    ],
];
```

The cleaner and more user friendly way is to use the GUI. Create a profile, name it `my-profile` and set multiplane to the profile name:

```php
return [
    'app.name' => 'CpMultiplane',

    'i18n' => 'en',
    'languages' => [
        'default' => 'English',
        'de' => 'Deutsch',
    ],

    // define settings via profile
    'multiplane' => [
        'profile' => 'my-profile',
    ],
];
```

## Reserved routes

* `/login` - Calling `example.com/login` reroutes to the admin folder, e. g. `example.com/cockpit`
* `/search` - full-text search
* `/getImage` - Calling `/getImage?src=assets_id?w=100&h=100&m=thumbnail` returns images/thumbnails with predefined settings, that can be adjusted with params
* `/submit/form_name` and `/form/form_name` - for contact forms
* `/getPreview` and `/livePreview` for content preview
* `/clearcache` to clear cockpit's cache (**only in debug mode**)

## Copyright and License

Copyright 2019 Raffael Jesche under the MIT license.

See [LICENSE][12] for more information.

## Credits and third party resources

Without Cockpit, CpMultiplane couldn't exist. Thanks to [Artur Heinze][16] and to all [contributors][17].

[1]: https://github.com/agentejo/cockpit/
[2]: https://github.com/raffaelj/cockpit_CpMultiplaneGUI
[3]: https://github.com/raffaelj/cockpit_UniqueSlugs
[4]: https://github.com/raffaelj/cockpit_rljUtils
[5]: https://github.com/raffaelj/cockpit_FormValidation
[6]: https://github.com/raffaelj/cockpit_VideoLinkField
[7]: https://github.com/raffaelj/cockpit_SimpleImageFixBlackBackgrounds
[8]: https://github.com/raffaelj/Monoplane
[9]: https://github.com/raffaelj/cockpit_CpMultiplaneBundle
[10]: https://github.com/pauloamgomes/CockpitCms-EditorFormats
[11]: https://github.com/raffaelj/cockpit_Bootmanager
[12]: https://github.com/raffaelj/CpMultiplane/blob/master/LICENSE
[16]: https://github.com/aheinze
[17]: https://github.com/agentejo/cockpit/graphs/contributors
[19]: https://cpmultiplane.rlj.me
[20]: https://cpmultiplane.rlj.me/en/docs/installation
[21]: https://github.com/raffaelj/CpMultiplane-i18n
[22]: https://hub.docker.com/r/raffaelj/cpmultiplane
