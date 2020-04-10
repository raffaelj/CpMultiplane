<?php
/**
 * fix (relative) urls in wysiwyg fields
 * 
 * Usage:
 * ./mp multiplane/fix/urls --oldbase "/my-website" --newbase ""
 * ./mp multiplane/fix/urls --oldbase "/my-website" --newbase "/subdir"
 * ./mp multiplane/fix/urls --oldbase "http://localhost/my-website" --newbase "https://my-website.com"
 * 
 * Separate multiple bases with `|` (only available for `--oldbase`)
 * ./mp multiplane/fix/urls --oldbase "/my-website|http://localhost/my-website" --newbase "/subdir"
 * 
 * Use case:
 * If you build your website locally in a subfolder (e. g. `http://localhost/my-website`)
 * and you copy it to the remote `https://www.my-website.com`, relative src urls from images point to the wrong location (e. g. `/my-website/getImage`).
 * 
 * Note for Windows users:
 * You might have to escape cli args starting with a slash with a second slash!
 * ./mp multiplane/fix/urls --oldbase "//my-website" --newbase "//subdir"
 *
 */

if (!COCKPIT_CLI) return;

$time_all = time();

$oldbase = $app->param('oldbase', null);
$newbase = $app->param('newbase', null);

if ($oldbase === true) $oldbase = '';
if ($newbase === true) $newbase = '';

if ($oldbase === null) {
    CLI::writeln('--oldbase parameter missing', false);
    return;
}
if ($newbase === null) {
    CLI::writeln('--newbase parameter missing', false);
    return;
}
if ($oldbase === $newbase) {
    CLI::writeln('Old base and new base are the same.', false);
    return;
}

CLI::writeln("Old base: $oldbase");
CLI::writeln("New base: $newbase");

// stop if leading slash is unescaped (Windows)
if (strpos($oldbase, __DIR__) === 0 || strpos($oldbase, \getcwd()) === 0 ||
    strpos($newbase, __DIR__) === 0 || strpos($newbase, \getcwd()) === 0 ||
    strpos($oldbase, ':') === 1 || strpos($newbase, ':') === 1) {
    CLI::writeln('It seems, that you are on a Windows machine and forgot to escape a leading slash.', false);
    return;
}

$collections = cockpit('collections')->collections();

$languages = $app->module('multiplane')->getLanguages(false, false);

// collect wysiwyg field names
$fixFields = [];
foreach ($collections as $col) {
    if (isset($col['fields']) && is_array($col['fields'])) {
        $f = [];
        foreach ($col['fields'] as $field) {
            if ($field['type'] == 'wysiwyg') {
                $f[] = $field['name'];
                if ($field['localize'] == true) { // check translated fields
                    foreach ($languages as $lang) {
                        $f[] = $field['name'] . '_' . $lang;
                    }
                }
            }
        }
        if (!empty($f)) $fixFields[$col['name']] = $f;
    }
}

foreach ($fixFields as $collection => $fields) {

    $count = cockpit('collections')->count($collection);

    CLI::writeln("Found $count entries in $collection");

    $options = [];

    foreach ($fields as $field) {
        $options['fields'][$field] = true;
    }

    $entries = cockpit('collections')->find($collection, $options);

    CLI::writeln('Starting to convert fields: ' . implode(', ', $fields));

    $time = time();
    $replaceCount = 0;

    foreach ($entries as &$entry) {

        foreach ($fields as $field) {
            if (isset($entry[$field])) {

                $parts = explode('|', $oldbase);
                $base = [];
                foreach ($parts as $v) $base[] = \preg_quote($v, '/');
                $base = implode('|', $base);

                $regex = '/\s+(src|href|poster)="(' . $base . ')([^"]*)"/m';

                $entry[$field] = \preg_replace($regex, " $1=\"$newbase\$3\"", $entry[$field], -1, $c);

                $replaceCount = $replaceCount + $c;

            }
        }

    }

    // save entries
    cockpit('collections')->save($collection, $entries);

    CLI::writeln("Replaced $replaceCount urls in $count entries from $collection in " . (time() - $time) . ' seconds');

}

CLI::writeln("Done in " . (time() - $time_all) . ' seconds', true);
