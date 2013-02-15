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
set :public_site, "~/html/"

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
# Compass/Sass compiling variables
# --------------------------------------------
set :watched_dirs, "sites/blackcoraldive.com/files/ambit"

# --------------------------------------------
# Callbacks - Set Before/After Precedence
# --------------------------------------------
# before "deploy:update_code", "backup"
after "drupal:symlink", "compass"
after "deploy:cleanup", "deploy:copy_to_public"

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
# Capistrano deploy methods (overridden)
# --------------------------------------------
namespace :deploy do
  desc <<-DESC
    Copy the current release of the application to it's public directory
    due to an issue with symlinks
  DESC
  task :copy_to_public, :roles => :web, :except => { :no_release => true} do
    # begin
      run "cp -rf #{latest_release}/* #{public_site}"
      run "cp -rf #{latest_release}/.htaccess #{public_site}"
      # run "ln -nfs #{shared_path}/#{application}/files files"
      # run "cp -rf #{shared_path}/#{application}/settings.#{stage}.php settings.php"
    # rescue Exception => e
    #   logger.debug e.message
    # end
  end
end

# --------------------------------------------
# Compass/Sass compiling
# --------------------------------------------
namespace :compass do

  desc "Compile SASS stylesheets and upload to remote server"
  task :default do
    compile
    upload_stylesheets
    ash.fixperms
  end

  desc 'Uploads compiled stylesheets to their matching watched directories'
  task :upload_stylesheets, :roles => :web, :except => { :no_release => true } do
    watched_dirs          = fetch(:watched_dirs, nil)
    stylesheets_dir_name  = fetch(:stylesheets_dir_name, 'stylesheets')

    if !watched_dirs.nil?
      if watched_dirs.is_a? String
        logger.debug "Uploading compiled stylesheets for #{watched_dirs}"
        logger.debug "trying to upload stylesheets from ./#{watched_dirs}/#{stylesheets_dir_name} -> #{latest_release}/#{watched_dirs}/#{stylesheets_dir_name}"

        upload_command = "scp -r ./#{watched_dirs}/#{stylesheets_dir_name}/*.css #{user}@#{application}:#{latest_release}/#{watched_dirs}/#{stylesheets_dir_name}/"

        logger.info "running SCP command:"
        logger.debug upload_command
        system(upload_command)

        # upload("./#{watched_dirs}/stylesheets/global-crest.css", "#{latest_release}/#{watched_dirs}/stylesheets/", :via => :scp)
      elsif watched_dirs.is_a? Array
        logger.debug "Uploading compiled stylesheets for #{watched_dirs.join(', ')}"
        watched_dirs.each do |dir|
          logger.debug "trying to upload stylesheets from ./#{dir}/#{stylesheets_dir_name}/ -> #{latest_release}/#{dir}/#{stylesheets_dir_name}/"

          upload_command = "scp -r ./#{dir}/#{stylesheets_dir_name}/*.css #{user}@#{application}:#{latest_release}/#{dir}/#{stylesheets_dir_name}/"

          logger.info "running SCP command:"
          logger.debug upload_command
          system(upload_command)
        end
      else
        logger.debug "Unable to upload compiled stylesheets because :watched_dirs was neither a String nor an Array"
      end
    else
      logger.info "Skipping uploading of compiled stylesheets `compass:upload` because `:watched_dirs` wasn't set"
    end
  end

  desc 'Compile minified version of CSS assets using Compass gem'
  task :compile, :roles => :web, :except => { :no_release => true } do

    compass_bin_local     = find_compass_bin_path
    watched_dirs          = fetch(:watched_dirs, nil)

    compass_bin           = fetch(:compass_bin, compass_bin_local)
    compass_env           = fetch(:compass_env, "production")
    compass_output        = fetch(:compass_output, 'compressed') # nested, expanded, compact, compressed

    if !compass_bin.nil?
      if !watched_dirs.nil?
        if watched_dirs.is_a? String
        logger.debug "Compiling SASS for #{watched_dirs}"
          system "#{compass_bin} compile --output-style #{compass_output} --environment #{compass_env} ./#{watched_dirs}"
        elsif watched_dirs.is_a? Array
          logger.debug "Compiling SASS for #{watched_dirs.join(', ')}"
          watched_dirs.each do |dir|
            system "#{compass_bin} compile --output-style #{compass_output} --environment #{compass_env} ./#{dir}"
          end
        else
          logger.debug "Unable to compile SASS because :watched_dirs was neither a String nor an Array"
        end
      else
        logger.info "Skipping SASS compilation in `compass:compile` because `:watched_dirs` wasn't set"
      end
    else
      logger.info "Skipping SASS compilation in `compass:compile` because unable to find the bin executable for the compass gem"
    end
  end

  desc "Finds the bin executable path for the compass gem"
  task :find_compass_bin_path, :except => { :no_release => true } do
    begin
      spec      = Gem::Specification.find_by_name("compass")
      gem_root  = spec.gem_dir
      gem_bin   = gem_root + "/bin/compass"
    rescue Gem::LoadError => e
      logger.debug "Unable to find the gem 'compass'! Check to see if it's installed: `gem list -d compass` or install: `gem install compass`"
      gem_bin = nil
    rescue Exception => e
      logger.debug "Unable to find the compass executable bin path because of this error: #{e.message}"
      gem_bin = nil
    end

    logger.debug "Path to compass executable: #{gem_bin.inspect}"

    # return the path the compass executable
    gem_bin
  end
end
