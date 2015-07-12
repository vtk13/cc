#!/usr/bin/env bash
git pull
composer install
bower --allow-root install
vendor/bin/phinx migrate
rm -rf public/assets
mkdir public/assets

cp bower_components/jquery/dist/jquery.min.js public/assets/jquery.js
cp bower_components/requirejs/require.js public/assets/require.js
mkdir public/assets/selectize
cp bower_components/selectize/dist/js/standalone/selectize.min.js public/assets/selectize/selectize.js
cp bower_components/selectize/dist/css/* public/assets/selectize/
mkdir public/assets/bootstrap
cp -r bower_components/bootstrap/dist/* public/assets/bootstrap
