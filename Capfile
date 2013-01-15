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
# Git/SVN Variables
# --------------------------------------------
set :repository, "git@github.com:grafikchaos/blackcoraldive.com.git"
set :scm, "git"

set :deploy_via,        :copy       # Copies repo to local folder on your machine and then copies it up via SFTP
set :copy_strategy,     :checkout
set :copy_cache,        true        # faster copy strategy
set :copy_compression,  :bz2        # compresses the directory befor copying it up


# --------------------------------------------
# Drush executable
# --------------------------------------------
set :drush_bin, "~/drush/drush"

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



