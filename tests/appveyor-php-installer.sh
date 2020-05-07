#!/bin/bash

set -x
set -e


function installPhp
{
  local version=$1
  local path="C:\tools\php\${version}"

  if [[ ! -d "${path}" ]] ; then
    appveyor-retry cinst --params '"/InstallDir:${path}"'  -y php --version 7.4
    cd "${path}"
    cp php.ini-production php.ini
  fi
}

installPhp ${PHP_VERSION}

