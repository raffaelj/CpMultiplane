# CpMultiplane

CpMultiplane is a small PHP front end for the fast and headless [Cockpit CMS][1]. It is no Cockpit addon, so don't copy it in the cockpit addon folder. It is designed to use Cockpit as a library to keep the idea of having a headless CMS. When calling Cockpit directly (UI or API), it has no clue about CpMultiplane in the background.

CpMultiplane is the refactored version of [Monoplane][8]. The code base was ugly, it was designed for very simple portfolio websites with a few pages and it didn't really support multilingual setups.


## Requirements

* PHP >= 7.0
* PDO + SQLite (or MongoDB)
* GD extension
* mod_rewrite, mod_versions enabled (on apache)

make also sure that `$_SERVER['DOCUMENT_ROOT']` exists and is set correctly.

## Installation

* copy all files of this repository in your web root
* copy Cockpit in a subfolder of your web root and name it `cockpit`
* copy additional addons, create your collections, adjust some settings

You can find the detailed version and a cli install example in [/docs/installation.md](/docs/installation.md).

## Features

* pages and posts
* multilingual with language prefix, e. g.: `example.com/en/my-page`
* 2 modes for structured content
  1. one collection per content type, e. g. a collection named `pages` and a collection named `posts`
  2. a single collection named `pages` - each entry has a type `page` or `post`
* maintenance mode with option for allowed ips
* simple content preview while editing pages
* a basic responsive theme with scss files
* simple privacy notice banner, that gets enabled when clicking on video link
* contact forms - fully functional without javascript
* pre-rendering of fields, e. g. markdown, wysiwyg
* multiple ways to change everything
* GUI is coming soon...
* ...

## Recommended Addons

Install these addons in `cockpit/addons/`.

* [CpMultiplaneBundle][9] - it contains the following addons:
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

If you install the bundle and EditorFormats, you should also use the [BootManager][11], because the VideoLinkField addon must be loaded after EditorFormats if you want to configure it via this addon.

## Intended use

1. Create a singleton `site` for your default page definitions.
2. Create a collection `pages` for all of your pages.
3. Create a collection `posts` for all of your blog posts.
4. Use the CpMultiplaneGUI addon.

## Default templates

coming soon...

## Settings

The fastest way to change some defaults, is to add some values to `MP_DOCS_ROOT/cockpit/config/config.yaml`:

to do...

```yaml
multiplane:
    slugName: slug
    isMultilingual: true
    # isInMaintenanceMode: true
    # allowedIpsInMaintenanceMode: 127.0.0.1
    # isPreviewEnabled: false
    preRenderFields: ["content", "excerpt"]
    displayPostsLimit: 6
    paginationDropdownLimit: 5
    formSendReferer: true                         # send current page with the contact form
    lexy:
        logo:
            width: 200
            height: 200
        headerimage:
            width: 968
            height: 200
```

If you change some settings and your page doesn't update, clear your cache in *Settings --> System --> Cache --> click trash icon* or just call `/cockpit/call/cockpit/clearCache?acl=qwe` while you are logged in and have cockpit manage rights.

## Reserved routes

* `/login` - Calling `example.com/login` reroutes to the admin folder, e. g. `example.com/cockpit`
* `/getImage` - Calling `/getImage?src=assets_id?w=100&h=100&m=thumbnail` returns images/thumbnails with predefined settings, that can be adjusted with params
* `/submit` and `/forms` - for contact forms
* `/getPreview` and `/livePreview` for content preview


## Copyright and License

Copyright 2019 Raffael Jesche under the MIT license.

See [LICENSE][12] for more information.

## Credits and third party resources

Without Cockpit, CpMultiplane couldn't exist. Thanks to [Artur Heinze][16] and to all [contributors][17].

I used [wa-mediabox][13] from [Jiří Hýbek][14] for gallery lightboxes, which is released under the [MIT License][15]. It is a lightweight lightbox without jQuery.


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
[13]: https://github.com/jirihybek/wa-mediabox
[14]: https://github.com/jirihybek
[15]: https://github.com/jirihybek/wa-mediabox/blob/master/LICENSE
[16]: https://github.com/aheinze
[17]: https://github.com/agentejo/cockpit/graphs/contributors
