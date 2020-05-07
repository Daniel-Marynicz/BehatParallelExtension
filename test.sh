
function installPhp
{
  local version

}

if [[ ! -d C:\tools\php\7.4 ]] ; then
  appveyor-retry cinst --params '""/InstallDir:C:\tools\php\7.4""'  -y php --version 7.4
  cd C:\tools\php\7.4
  cp php.ini-production php.ini
fi
