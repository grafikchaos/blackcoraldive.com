# Deploy Drupal site using Capistrano
#
# Usage:
# cap staging deploy:setup
# cap staging deploy

# --------------------------------------------
# Variables
# --------------------------------------------

# repository branch
set :branch, "master"

# --------------------------------------------
# Multisites
# --------------------------------------------
# Setting which folder in the sites directory to use
# change's the name of your local site folder to the
# fully qualified domain name (URL) of the live site,
# so if you want the 'www' included in the url use
# this example:
#    set :multisites, {
#      'bestappever.local' => 'www.bestappever.com'
#    }
set :multisites, {
  "#{application}" => "#{application}"
}

# --------------------------------------------
# Callbacks - Set Before/After Precedence
# --------------------------------------------
# before "deploy:update_code", "backup"
