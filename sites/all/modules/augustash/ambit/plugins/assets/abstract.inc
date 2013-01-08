<?php

/**
 * @file
 * Abstraction of the selection logic of a valet link group.
 *
 * Implementations that wish to provide an implementation of this should
 * register it using CTools' plugin system.
 */
interface Ambit_AssetsHandler {

  /**
   * Constructor for the links.
   *
   * @param $type
   *   The name of the links plugin.
   */
  public function __construct($type, $args = NULL);

  public function assets_build($items);

  public function flush_caches();

  public function settings_form(&$form);

  public function settings_get();

}

/**
 * An abstract implementation of EntityReference_LinksHandler.
 */
abstract class Ambit_AssetsHandler_Abstract implements Ambit_AssetsHandler {

  /**
   * The name of the links plugin.
   */
  protected $type;

  /**
   * The plugin definition.
   */
  protected $plugin;

  /**
   * Contains all the css/scss/other files that will be passed back to the renderer
   */
  protected $items;

  /**
   * Asset type to extract
   */
  protected $asset_types = array();

  /**
   * Contains asset files needing to be processed
   * Files are an array with key "global" or "page" depending on the context of the file
   */
  protected $asset_items = array();

  /**
   * Constructor for the links.
   *
   * @param $type
   *   The name of the links plugin.
   */
  public function __construct($type, $args = NULL){
    $this->type = $type;
    ctools_include('plugins');
    $plugin = ctools_get_plugins('ambit', 'assets', $type);
    $this->plugin = $plugin;
  }

  public function assets_build($items){
    $this->items = $items;
    // Extract all assets needing to be processed
    $this->assets_filter()->assets_process();
    return $this->items;
  }

  /**
   * Gets a list of sass files
   * 
   * @param  array $items
   *   An array of files about to be sent for processing
   *   
   * @return array $sass
   *   An array of files only containing sass files
   */
  protected function assets_filter(){
    $items = $this->items;
    $types = $this->asset_types;
    if(empty($types)) return;
    $asset_items = array();
    foreach($this->items as $path => $file){
      if ($file['type'] != 'file') {
        continue;
      }
      $parts = pathinfo($path);
      if (!isset($parts['extension'])) {
        continue;
      }
      foreach($types as $type){
        if($parts['extension'] == $type){
          $file += $parts;
          // Seperate page files into two groups
          // 1. Files loaded on EVERY PAGE
          // 2. Files loaded just on this page
          if($file['every_page'] === TRUE){
            $asset_items['global'][$path] = $file;
          }else{
            $asset_items['page'][$path] = $file;
          }
          // Remove the files from the main files list
          unset($this->items[$path]);
        }
      }
    }
    $this->asset_items = $asset_items;
    return $this;
  }

  protected function assets_process(){
    return $this;
  }

  /**
   * Implements hook_flush_caches
   */
  public function flush_caches(){
    return $this;
  }

  /**
   * Settings form
   */
  public function settings_form(&$form){}

  /**
   * Return settings
   */
  public function settings_get(){}

}

/**
 * A null implementation of Valet_LinksHandler.
 */
class Ambit_AssetsHandler_Broken extends Ambit_AssetsHandler_Abstract {

}