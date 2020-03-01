/*
 * helper to generate uglify options and license preambles in minified files
 *
 * npm run generate-preamble-files
 * npm run update
 */

const fs = require('fs');

fs.readFile('package.json', 'utf8', (error, text) => {

    const pkg        = JSON.parse(text);
    const sassPath   = 'assets/css/sass/_copyright-preamble.scss';
    const uglifyPath = 'uglify.json';
    const preamble   = `${pkg.name} - version: ${pkg.version}\r\nCopyright © ${(new Date).getFullYear()} ${pkg.author}, ${pkg.license} Licensed, ${pkg.homepage || ''}`;

    const uglify = {
        'output': {
            'comments': 'some',
            'beautify': false,
            'preamble': `/*\r\n${preamble}\r\n*/`
        },
    }

    const sass = `/**\r\n * auto generated copyright preamble file\r\n *\r\n * npm run generate-preamble-files\r\n * npm run update\r\n */\r\n\r\n/*!\r\n${preamble}\r\n*/\r\n`;

    fs.writeFile(uglifyPath, JSON.stringify(uglify, null, 4) + "\r\n", 'utf8', () => {
        console.log('Saved ' + uglifyPath);
    });

    fs.writeFile(sassPath, sass, 'utf8', () => {
        console.log('Saved ' + sassPath);
    });
});
