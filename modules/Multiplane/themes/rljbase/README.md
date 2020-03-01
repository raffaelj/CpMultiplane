# rljbase theme

to do...

## Features

* basic css
* ...

### mp.js

* simple cookie management
* handle privacy events
* simple video - display YouTube and Vimeo iframes with a thumbnail and don't load videos without user's privacy consent
* simple image lightbox
* simple image carousel

### Image lightbox

```js
MP.ready(function() { // document is ready
    // init lightbox
    MP.Lightbox.init({
        group: '.gallery',  // all elements with class 'gallery' are galleries
        selector: 'a'       // all a tags are detected as image links
    });
});
```

*Marginalia:* The lightbox is compatible with WordPress Gutenberg galleries, but it might have unwanted side effects, if your theme doesn't fit exactly.

```js
MP.ready(function() {
    MP.Lightbox.init({group: '.wp-block-gallery', selector: 'a'});
}
```

### Video

requires VideoLinkField addon and videolink field, that is named "video"

## build

* `npm install` - install dev dependencies
* `npm run build` to rebuild js+css files
* `npm run watch` watch js+css changes
* `npm run update` to build js+css with regenerated copyright preamble
