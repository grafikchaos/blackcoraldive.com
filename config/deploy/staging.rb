# Deploy Drupal site using Capistrano
#
# Usage:
# cap staging deploy:setup
# cap staging deploy

# --------------------------------------------
# Variables
# --------------------------------------------
# SSH user
set :user, "grafikch"

# repository branch
set :branch, "master"

# --------------------------------------------
# Server Variables/Defaults
#
#    Alternative Server(s) Configuration:
#      role :web, "domain.com"  # can also use IP-address or host's servername
#      role :db, "domain.com"   # can also use IP-address or host's servername
# --------------------------------------------
server "grafikchaos.com", :web, :db, :primary => true
set :deploy_to, "/home2/#{user}/code/#{application}/#{stage}"


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
