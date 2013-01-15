# Deploy Drupal site using Capistrano
#
# Usage:
# cap staging deploy:setup
# cap staging deploy

# --------------------------------------------
# Variables
# --------------------------------------------
set :public_site, "/home2/#{user}/www/development/blackcoraldive"

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

# --------------------------------------------
# Callbacks - Set Before/After Precedence
# --------------------------------------------
# before "deploy:update_code", "backup"
after "deploy:cleanup", "deploy:copy_to_public"


# --------------------------------------------
# Capistrano deploy methods (overridden)
# --------------------------------------------
namespace :deploy do
  desc <<-DESC
    Copy the current release of the application to it's public directory
    due to an issue with symlinks
  DESC
  task :copy_to_public, :roles => :web, :except => { :no_release => true} do
    run "cp -r #{latest_release} #{public_site}"
  end
end
