<?php

class Ambit_AssetsHandler_Scss extends Ambit_AssetsHandler_Abstract {

  /**
   * Asset type to extract
   */
  protected $asset_types = array('scss');

  protected $map;
  protected $collection = array();
  protected $data = array();
  protected $data_prefix = array();
  protected $data_suffix = array();
  protected $files = array();
  protected $file = '';
  protected $name = '';
  protected $extras = array();
  protected $import_paths = array();

  protected $destination_uri = 'public://ambit';
  protected $destination_css_dir = 'stylesheets';
  protected $destination_sass_dir = 'sass';
  protected $destination_images_dir = 'images';
  protected $destination_fonts_dir = 'fonts';


  public function __construct($type, $args = NULL){
    parent::__construct($type, $args);
    $this->assets_cache_get();
  }

  /**
   * Return settings
   */
  public function settings_get(){
    $settings = array();
    $settings['uri'] = $this->destination_uri;
    return $settings;
  }

  /**
   * Process the assets if necessary
   */
  protected function assets_process(){
    global $theme;
    $asset_items = $this->asset_items;
    foreach($asset_items as $group => $files){

      $this->files = $files;

      // Create a unique key for this file group
      $key = implode('', array_keys($files));
      $this->name = $original_name = substr($group.'-'.drupal_hash_base64($key), 0, 25).'-'.$theme;

      // Get a file to be used for structure later
      $this->file = $files[key($files)];

      // We havn't yet created a file
      if($group == 'global') $this->name = $group.'-'.$theme;
      if(empty($this->map[$original_name])){
        $this->map[$original_name] = time();
        foreach($files as $file){
          $this->file = $file;
          $this->assets_process_file()->assets_extras_gather_module();
        }
        $this->assets_file_structure()->assets_extras_gather_theme()->assets_extras_process()->assets_file_save();
      }

      $this->assets_add();
    }

    // Save cache
    $this->assets_cache_set();

    return $this;
  }

  protected function assets_process_file(){
    $file = $this->file;
    $realpath = $this->file['realpath'] = drupal_realpath($file['data']);
    if(!file_exists($realpath)) return;
    $parts = pathinfo($realpath);
    $this->collection[$parts['dirname']] = $parts['dirname'];
    if(!empty($file['wrapper'])) $this->data_prefix[] = $file['wrapper'].'{';
      $this->data[] = '@import "'.$realpath.'";';
    if(!empty($file['wrapper'])) $this->data_suffix[] = '}';
    return $this;
  }

  protected function assets_extras_process(){
    foreach($this->extras as $type => $folders){
      foreach($folders as $folder){
        $source = drupal_realpath($folder);
        switch($type){
          case 'fonts':
            $dest = drupal_realpath($this->destination_uri.'/'.$this->destination_fonts_dir);
            break;
          case 'images':
            $dest = drupal_realpath($this->destination_uri.'/'.$this->destination_images_dir);
            break;
        }
        if(!$this->assets_extras_clone($source, $dest)){
          drupal_set_message(t('Asset type %type failed to copy from %folder.', array('%type' => $type, '%folder' => $source)), 'error');
        }
      }
    }
    return $this;
  }

  /**
   * Gets any extras specifed by a module loading a scss file
   */
  protected function assets_extras_gather_module(){
    $modules = module_invoke_all('valet_assets');
    if(!is_array($modules)) return $this;
    foreach($modules as $module => $folders){
      $include_path = drupal_get_path('module', $module);
      foreach($folders as $type => $folder){
        $this->extras[$type][$include_path.'/'.$folder] = $include_path.'/'.$folder;
      }
    }
    return $this;
  }

  /**
   * Gets any extras specifed by a theme loading a scss file
   */
  protected function assets_extras_gather_theme(){
    drupal_theme_initialize();
    $themes = list_themes();
    foreach($themes as $theme){
      $all = array();
      if(isset($theme->stylesheets['all'])) $all += $theme->stylesheets['all'];
      if(isset($theme->stylesheets['screen'])) $all += $theme->stylesheets['screen'];
      foreach($all as $theme_css){
        if(array_key_exists($theme_css, $this->files)){
          $extras = !empty($theme->info['ambit']) ? $theme->info['ambit'] : array();
          foreach($extras as $type => $folder){
            $include_path = drupal_get_path('theme', $theme->name);
            $this->extras[$type][$include_path.'/'.$folder] = $include_path.'/'.$folder;
          }

        }
      }
    }
    return $this;
  }

  protected function assets_file_structure(){
    file_prepare_directory($this->destination_uri, FILE_MODIFY_PERMISSIONS | FILE_CREATE_DIRECTORY);
    $folder = $this->destination_uri.'/'.$this->destination_sass_dir;
    file_prepare_directory($folder, FILE_MODIFY_PERMISSIONS | FILE_CREATE_DIRECTORY);
    $folder = $this->destination_uri.'/'.$this->destination_css_dir;
    file_prepare_directory($folder, FILE_MODIFY_PERMISSIONS | FILE_CREATE_DIRECTORY);
    $this->assets_config_create();
    return $this;
  }

  protected function assets_config_create(){
    
    // Get import paths
    $asset_updates = $this->assets_import_paths();

    if(!file_exists($this->destination_uri.'/config.rb') || $asset_updates){
      $output = "preferred_syntax = :sass\n";
      $output .= "http_path = '/'\n";
      $output .= "sass_dir = '".$this->destination_sass_dir."'\n";
      $output .= "css_dir = '".$this->destination_css_dir."'\n";
      $output .= "images_dir = '".$this->destination_images_dir."'\n";
      $output .= "fonts_dir = '".$this->destination_fonts_dir."'\n";
      $output .= "relative_assets = true\n";
      $output .= "line_comments = true\n";
      if(variable_get('ambit_compass_debug', FALSE)) $output .= "sass_options = {:debug_info => true}\n";
      if($option = variable_get('ambit_compass_output_style', 'nested')) $output .= "output_style = $option\n";
      $output .= $asset_updates;
      $path = $this->destination_uri.'/config.rb';
      $filename = file_unmanaged_save_data($output, $path, FILE_MODIFY_PERMISSIONS | FILE_EXISTS_REPLACE);
    }
  }

  protected function assets_import_paths(){
    $this->import_paths = $original_paths = variable_get('ambit_compass_import_paths', array());
    foreach($this->collection as $path){
      if(!in_array($path, $this->import_paths)) $this->import_paths[] = $path;
    }
    $filepath = $this->destination_uri.'/config.paths.rb';
    if(array_diff($this->import_paths, $original_paths) || !file_exists($filepath)){
      variable_set('ambit_compass_import_paths', $this->import_paths);
      $output = array();
      foreach($this->import_paths as $path){
        $output[] = 'add_import_path "'.$path.'"';
      }
      return implode("\n", $output);
    }
    return FALSE;
  }

  protected function assets_file_save(){
    $this->data = array_merge($this->data_prefix, $this->data, $this->data_suffix);
    file_unmanaged_save_data(implode("\n", $this->data), $this->destination_uri.'/'.$this->destination_sass_dir.'/'.$this->name.'.scss', FILE_MODIFY_PERMISSIONS | FILE_EXISTS_REPLACE);
    return $this;
  }

  protected function assets_add(){
    $path = $this->destination_uri . '/' . $this->destination_css_dir . '/' . $this->name . '.css';
    $path = str_replace($GLOBALS['base_url'].'/','',file_create_url($path));
    $parts = pathinfo($path);

    $this->items[$path] = $this->file;
    $this->items[$path]['data'] = $path;
    $this->items[$path]['preprocess'] = FALSE;
  }

  /** 
   * Copy a file, or recursively copy a folder and its contents 
   * 
   * @param string $source    
   *   Source path 
   * @param string $dest
   *   Destination path 
   *   
   * @return bool
   *   Returns TRUE on success, FALSE on failure 
   */ 
  protected function assets_extras_clone($source, $dest) { 
    // Simple copy for a file 
    if (is_file($source)) {
      return file_unmanaged_copy($source, $dest, FILE_EXISTS_REPLACE); 
    } 
    // Make destination directory 
    if (!is_dir($dest)) { 
      file_prepare_directory($dest, FILE_MODIFY_PERMISSIONS | FILE_CREATE_DIRECTORY);
    }
    // Loop through the folder 
    $dir = dir($source); 
    while (false !== $entry = $dir->read()) { 
      // Skip pointers 
      if ($entry == '.' || $entry == '..') { 
        continue; 
      } 
      // Deep copy directories 
      if ($dest !== "$source/$entry") { 
        $this->assets_extras_clone("$source/$entry", "$dest/$entry"); 
      } 
    }
    // Clean up 
    $dir->close(); 
    return true; 
  }

  /**
   * Settings form
   */
  public function settings_form(&$form){

    $form['compass'] = array(
      '#type' => 'fieldset',
      '#title' => t('Compass'),
      '#collapsible' => FALSE,
      '#description' => t('Compass is a standalone Ruby script used to compile CSS from Sass stylesheets. To use it, it must be installed on your server and you need to know where it is located. If you are unsure of the exact path consult your ISP or server administrator.'),
    );
  
    $form['compass']['ambit_compass_output_style'] = array(
      '#type' => 'radios',
      '#title' => t('Output style for the compiled css'),
      '#default_value' => variable_get('ambit_compass_output_style', ':nested'),
      '#options' => array(':nested'=>'Nested', ':expanded'=>'Expanded', ':compact'=>'Compact', ':compressed'=>'Compressed')
    );
  
    $form['compass']['ambit_compass_debug'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable compass debug mode'),
      '#default_value' => variable_get('ambit_compass_debug', 0),
      '#description' => t('This will enable support for !firesass', array('!firesass'=>l('FireSass', 'https://addons.mozilla.org/en-US/firefox/addon/firesass-for-firebug/'))),
    );

  }

  /**
   * Implements hook_flush_caches
   */
  public function flush_caches(){
    //exec('drush ambit --force');
    drupal_set_message('If needed, run the following command in terminal to watch for any new SASS changes: <strong>cd "'.drupal_realpath($this->destination_uri).'"; compass watch</strong>');
    // Remove the variable that contains our import paths
    variable_del('ambit_compass_import_paths');
    // Config.rb
    file_unmanaged_delete($this->destination_uri.'/config.rb');
    // Clear images
    file_unmanaged_delete_recursive($this->destination_uri.'/'.$this->destination_images_dir);
    // Clear fonts
    file_unmanaged_delete_recursive($this->destination_uri.'/'.$this->destination_fonts_dir);
    // Clear fonts
    file_unmanaged_delete_recursive($this->destination_uri.'/'.$this->destination_sass_dir);
    return $this;
  }

  /**
   * Get the asset cache
   */
  protected function assets_cache_get(){
    $map = cache_get('ambit:map');
    $this->map = $map ? $map->data : array();
    return $this;
  }

  /**
   * Set the asset cache
   */
  protected function assets_cache_set(){
    cache_set('ambit:map', $this->map, 'cache', CACHE_TEMPORARY);
  }

}