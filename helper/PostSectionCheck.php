<?php
/**
 * @package post-sections
 */
/*
  Plugin Name: PostSections
  Plugin URI: http://www.rachel-webcat.com/
  Description: Simple plugin that allows wordpress administrators to store parameters for multiple googlemaps on their wordpress blog
  and easily use those maps in posts/allow users to use the stored maps
  Author: webcat
  Version: 5.5
  Author URI: http://www.rachel-webcat.com/
 */
//#################################################################
// Stop direct call
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {    die('You are not allowed to call this page directly.');}
//#################################################################
if (!class_exists('PostSectionsCheck')) {

    class PostSectionCheck{

        function  __construct() {

        }
        function clean($data,$type=null){
            switch($type) {
                case 'ID':
                    $return=  preg_replace('/ /', '_', $data);
                    return  preg_replace('/([^A-Za-z-_]+)+/', '', $return);
            }
            //return $data;
        }
        function check($data){
            return $data;
        }
    }
}
