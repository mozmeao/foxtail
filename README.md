# Foxtail (A custom Wordpress theme for Firefox)

## Running locally:

To run locally on your machine, you'll need to do a few things.

1. You need a local instance of Wordpress. [Try local by flywheel](https://localwp.com)
2. For browsersync to work, you will need to have this Wordpress instance running at foxtail.local
3. You will need to place this theme inside the wp-content/themes folder
4. Once the above is done, you'll need to run the following from this theme directory in order to set up gulp

```sh
# Install dependancies
npm install

# Download necessary JS files from protocol (you only need to do this once, or whenever you change wpgulp.config.js to bring in another .js file)
npm run protocolJS


# Run gulp script (requires local instance of wordpress running at foxtail.local)
npm run dev
```

_Note: CSS is compiled, and not checked into git. You will need to run gulp before css works locally._

## Protocol CSS

Protocol CSS is imported directly from the node_modules/@mozilla-protocol/core. If you have not run npm install, you will get errors when running gulp.

## Protocol Javascript

Protocol javascript is imported, but requires a few steps:

1. Add the files you want to import to the protocolJS task in gulpfile.babel.js
2. `npm run protocolJS`
3. You only need to run this npm script once, or whenever you update the list of javascript files to import from Protocol.
