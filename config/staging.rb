# Deploy Drupal site using Capistrano
#
# Usage:
# cap staging deploy:setup
# cap staging deploy

# --------------------------------------------
# Variables
# --------------------------------------------
# SSH user
# set :user, "grafikch"
# set :user, "d2a38525"

# repository branch
set :branch, "master"

# --------------------------------------------
# Server Variables/Defaults
#
#    Alternative Server(s) Configuration:
#      role :web, "domain.com"  # can also use IP-address or host's servername
#      role :db, "domain.com"   # can also use IP-address or host's servername
# --------------------------------------------
# server "grafikchaos.com", :web, :db, :primary => true
# set :deploy_to, "/home2/#{user}/code/#{application}/#{stage}"
