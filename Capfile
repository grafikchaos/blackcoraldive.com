# Capfile
load 'deploy' if respond_to?(:namespace) # cap2 differentiator

# --------------------------------------------
# :application HAS TO BE DEFINED BEFORE
# REQUIRING 'ash/drupal'
# --------------------------------------------
set :application, "blackcoraldive.com"

# --------------------------------------------
# Define required Gems/libraries
# --------------------------------------------
require 'ash/drupal_shared_hosting'


# --------------------------------------------
# Variables
# --------------------------------------------
# SSH user
set :user, "d2a38525"

# --------------------------------------------
# Server Variables/Defaults
# --------------------------------------------
server "blackcoraldive.com", :web, :db, :primary => true
set(:deploy_to) { "~/code/#{application}/#{stage}" }

# --------------------------------------------
# Git/SVN Variables
# --------------------------------------------
set :repository, "git@github.com:grafikchaos/blackcoraldive.com.git"
set :scm, "git"

# set :copy_strategy,     :export
set :deploy_via,        :copy       # Copies repo to local folder on your machine and then copies it up via SFTP
set :copy_strategy,     :checkout
set :copy_cache,        true        # faster copy strategy
set :copy_compression,  :gzip        # compresses the directory befor copying it up


# --------------------------------------------
# Drush executable
# --------------------------------------------
set :drush_bin, "~/drush/drush"


# --------------------------------------------
# Compass/Sass
# --------------------------------------------
set :compass_bin, "compass" # we're trusting that compass is in the User's path
set(:compass_env) { "#{stage}" }
#  One of: nested, expanded, compact, compressed
set :compass_output, "compressed"


# --------------------------------------------
# Database/Backup Variables
# --------------------------------------------
# Database credentials will be defined in the
# settings.production.php or settings.staging.php
# files, which drush will use when doing database
# dumps
set :keep_backups, 3 # only keep 3 backups (default is 10)

# Set Excluded directories/files (relative to the application's root path)
set(:backup_exclude) { [ "var/", "tmp/" ] }

# --------------------------------------------
# Callbacks - Set Before/After Precedence
# --------------------------------------------
# before "deploy:update_code", "backup"

# --------------------------------------------
# Drupal-specific methods (overridden)
# --------------------------------------------
namespace :drupal do
  desc <<-DESC
    Symlinks the appropriate environment's settings file within the proper sites directory

    Assumes the environment's settings file will be in one of two formats:
      settings.<environment>.php    => new default
      settings.php.<environment>    => deprecated
  DESC
  task :symlink_config_file, :roles => :web, :except => { :no_release => true} do
    multisites.each_pair do |folder, url|
      config_settings_dir = "#{shared_path}/#{url}"
      drupal_app_site_dir = "#{latest_release}/sites/#{url}"

      case true
        when remote_file_exists?("#{drupal_app_site_dir}/settings.#{stage}.php")
          run "ln -nfs #{drupal_app_site_dir}/settings.#{stage}.php #{drupal_app_site_dir}/settings.php"
        when remote_file_exists?("#{drupal_app_site_dir}/settings.php.#{stage}")
          run "ln -nfs #{drupal_app_site_dir}/settings.php.#{stage} #{drupal_app_site_dir}/settings.php"
        when remote_file_exists?("#{config_settings_dir}/settings.#{stage}.php")
          run "ln -nfs #{config_settings_dir}/settings.#{stage}.php #{drupal_app_site_dir}/settings.php"
        when remote_file_exists?("#{config_settings_dir}/settings.php.#{stage}")
          run "ln -nfs #{config_settings_dir}/settings.php.#{stage} #{drupal_app_site_dir}/settings.php"
        else
          logger.important "Failed to symlink the settings.php file in #{drupal_app_site_dir} because an unknown pattern was used"
      end
    end
  end
end

# --------------------------------------------
# Compass/Sass compiling
# --------------------------------------------
namespace :compass do
  desc 'Compile minified version of CSS assets using Compass gem'
  task :compile, :roles => :web, :except => { :no_release => true } do
    system "#{compass_bin} compile --output-style #{compass_output} --environment #{compass_env} ./sites/blackcoraldive.com/files/ambit"
  end
end
