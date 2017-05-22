#!/usr/bin/env bash

mkdir vendor

bundle install --path vendor/bundle
bower install

mkdir vendor/meyer-reset
curl -L -0 https://github.com/adamstac/meyer-reset/raw/master/stylesheets/_meyer-reset.scss > vendor/meyer-reset/meyer-reset.scss
