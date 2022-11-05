# Changelog

## 0.3.1

* fixed bugs caused by removing trailing slash
* a11y improvements
* fixed broken form in rljbase theme
* small bug fixes
* Dockerfile: default to cockpit next branch
* Dockerfile: use php8.0 (dev) docker image as base

## 0.3.0

* new and updated docs: https://cpmultiplane.rlj.me
* massive code cleanup and rewrite
* changed `$posts` variable to contain only posts, before it contained posts, collection, pagination
    * effects templates, especially `partials/posts.php`
    * effects `multiplane.page` event
* added globally available viewvars to simplify templating
* added ability to overwrite default field names
* added mpdocs theme
* added debug options
    * add html comments around templates
    * display debug overlay with useful metadata
* improved MailProtection in `mp.js`
* improved nav (markup and css)
* started to add permalink support (**experimental**)
* started to implement hardcoded structure for collection based subpages
* enabled `error_reporting(E_ALL)` if in debug mode
* improved cli commands
* a lot of minor code fixes

* use profiles from ImageResize addon
* implemented localized forms
* added multipleselect form field
* fixed broken honeypot (forms)
* added skip link to rljbase theme
* don't reroute `/*` to `/en/*`, only reroute `/` to `/en`
* some a11y improvements
* added option to change favicon path
* drop `$fn` usage in search (MongoLite, if cockpit version < 0.11.3)
* simplified and fixed sitemap schema
* added tags overview page
* moved live preview logic in separate controller
* removed search button animation
* only show forms in `use` (must be enabled via profile/config)
* removed collection label in search results (may be reintroduced as categories)
* fixed resetting default array keys with missing config keys (e. g. unavailable `search/minLength`
* reroute pages with trailing slash

## 0.2.4

* fixed docker image build automation (a typo in `Dockerfile` after cleanup)

## 0.2.3

* fixed overwriting modified `.htaccess` with `composer update`
* added new cli command `./mp multiplane/update-htaccess`
* added Dockerfile and published docker images, available at: https://hub.docker.com/r/raffaelj/cpmultiplane
* improved `.htaccess` - more restrictive

## 0.2.2

* fixed empty route if `$S_SERVER['PATH_INFO']` is incorrect
* fixed breadcrumbs to display titles instead of slugs
* fixed wrong image urls in `/getImage` route when passing paths instead of asset ids
* added support for `layout` as content field
* added custom sort option for sub pages
* improved path guessing and handling
* improved form templates and css
* started to implement extended search
* started to implement composer usage `raffaelj/cpmultiplane`
* minor code and css fixes

## 0.2.1

* fixed tags route bindings if not multilingual
* added tags fields to templates
* added CpMultiplane i18n repository
* added mp auto i18n download in cli command

## 0.2.0

* changed structure of theme views - **The structure is not backwards compatible to existing child themes.**
* replaced `displaySearch` key with `search/enabled`
* improved full-text search
* added tags pages
* added quickstart cli commands
* added quickstart templates to `/modules/Multiplane/themes/rljbase/templates`
* fixed broken npm watch command in rljbase theme
* rljbase theme can have a repeater as a content field
* updated documentation
* enabled mobile nav in base and stripes theme
* css/sass cleanup and fixes
* multiple fixes for type based pages

## 0.1.7

* fixed minor issues in forms
* fixed wrong language labels in lang menu
* added compatibility with Cockpit's Lime update (2020-02-05)
* added cli commands
  * fix html entities in wysiwyg fields
  * replace url bases in wysiwyg fields
* added simple mail protection script
* improved HTMl indentation (custom Lexy template parser)
* improved build scripts (themes)
* improved mobile nav
* minor code and css fixes

## 0.1.6

* massive rewrite
* added profiles
* dropped setting default collections to `pages` and `posts` and site singleton to `site` - You have to specify them manually
* changed event `multiplane.getposts.before`
* moved npm build scripts to theme folders
* restructured `mp.js` to browserify modules
* restructured/moved page/posts templates
* added events `multiplane.getpage.before`, `multiplane.head`, `multiplane.getpreview.before`, `multiplane.seo`
* added rljstripes theme (experimental)
* added support for repeater as content field (experimental)
* added optional i18n date format for posts meta
* added simple snippet to enable matomo tracking
* added breakpoints for background image
* improved child theming
* improved menu/nav - multi level dropdown, responsive, touch friendly
* improved sitemap
* improved full-text search
* improved seo meta data (og:, twitter:, ld+json)
* improved `mp.js` (event system, accessibility)
* improved privacy popup
* improved live preview
* multiple minor fixes
* moved docs to [own repository](https://github.com/raffaelj/cockpit_CpMultiplane-docs)

## 0.1.5

* changed assets version to time in debug mode and moved version info to `package.json`
* introduced `MP_ENV_ROOT` and `MP_ENV_URL`
* added template and js for simple image carousel
* added lexy short renderer `bigthumbnail` to rljbase theme
* added events `multiplane.getposts.before`, `multiplane.page`, `multiplane.getimage.before`, `multiplane.sitemap`
* added sort order to `getNav` function
* added `/clearcache` route (only in debug mode)
* added sitemap
* introduced theme config file
* improved video field template - more php, less js
* improved search
* improved pagination
* improved css icons
* replaced `.htaccess` with `.htaccess.dist`
* improved Lexy settings
* some accessibility fixes
* deprecated: `convertVideoLinksToIframes()` in `mp.js`

## 0.1.4

* added child theme support
* fixed error in getNav if no entries exist
* improved full-text search
* some i18n fixes
* added cli commands `./mp check` and `./mp account/create`
* rljbase theme
  * fixed/improved font stack
  * restructured scss files into subfolder
  * some color changes and minor fixes

## 0.1.3

* started to implement posts meta data
* started to implement fulltext search
* added option for hardcoded navigation
* improved form (CSS and a session cleanup)
* improved custom theming
* fixed wrong gallery variable in rljbase theme
* removed wa-mediabox lightbox lib and replaced it with my own simple lightbox
* some cleanup

## 0.1.2

* new shorthand function `mp()` returns `cockpit('multiplane')`
* improved breadcrumbs (now they are also disabled by default)
* improved navigation (active state)
* new core function `get()` - works like `$app->retrieve()`, but only inside multiplane module
* changed lexy image shortcuts - `mode` is now `method`
* some cleanup

## 0.1.1

* minor fixes and cleanup
* added more comments to config variables
* changed `useDefaultRoutes => true` to `disableDefaultRoutes => false` (the configuration to disable all default routes)
* added `setConfig()` function to overwrite defaults with options from GUI

## 0.1.0

* initial release
* I rewrote [Monoplane](https://github.com/raffaelj/Monoplane). While trying to make it multilingual in its update branch, I decided, that the code base was too ugly.
