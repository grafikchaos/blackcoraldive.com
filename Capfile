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
