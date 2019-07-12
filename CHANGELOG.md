# Changelog

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
* improved breadcrumbs (now they are alos disabled by default)
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
