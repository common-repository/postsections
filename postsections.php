<?php
/**
 * @package post-sections
 */
/*
  Plugin Name: PostSections
  Plugin URI: http://www.rachel-webcat.com/
  Description: Provides support for splitting up long posts into sections.  Set the maximum length you want to display of your posts.
 * The plugin provides xhtml Transitional compliant code with back and next navigation to scroll through your posts.  It uses jQuery in no
 * conflict mode to provide ajax functionality to speed up page load times.  Non-javascript browsers or even those that dont support jQuery
 * will load post sections through normal wordpress mechanism.
 * Currently tested in Firefox 3.6.21, IE 8
  Author: webcat
  Version: 2.25
  Author URI: http://www.rachel-webcat.com/PostSections
 */
//#################################################################
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.');}
//#################################################################
if (!class_exists('PostSections')) {
    
    /**
     * Main class for the plugin
     *
     * sets up all the links into wordpress and provides access to the output functions
     */
    class PostSections {
        /**
         * @static string used to check the version of wuwur being used
         * @todo function to update plugin
         */
        public static $PostSectionsVersion = 2.25;
        /**
         *
         * @var string
         */
        public static $PostSectionsCredits = "PostSections";
        /**
         *
         * @var string
         */
        public static $PostSectionsAuthor = "webcat";
        /**
         *
         * @var string
         */
        public static $PostSectionsURL = "http://www.rachel-webcat.com/";
        /**
         *
         */
        public static function update() {delete_option('postsectionsettings');
            if(!get_option('postsectionsettings')){
                if(get_option('postsectionlength')){ //if the old setting still exists store value and delete option
                    $l=get_option('postsectionlength');
                    if(!$l){$l=250;}
                    update_option('postsectionsettings','pre;navcontent;post;'.$l.';0;pt;12;px');
                    delete_option('postsectionlength');
                }
            }
        }
        /**
         *
         * @return string value of this plugins directory
         */
        public static function pluginDir() {
            return str_replace("postsections.php", "", __FILE__);
        }

        /**
         *
         * @return string value of this plugins url
         */
        public static function pluginURL() {
            $url = get_bloginfo('url') . '/wp-content/plugins/postsections/';
            return $url;
        }

        /**
         * @static method to get url for ajax calls
         */
        public static function ajaxURL() {
            $temp = get_bloginfo('url') . "/wp-admin/admin-ajax.php";
            return $temp;
        }
        /**
         * generic function to catch errors from missing files
         * @param <type> $path
         * @param <type> $file
         * @param <type> $require
         * @return <type>
         */
        public static function getFile($path,$file,$require=true){
                if (!file_exists(PostSections::pluginDir() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)) {
                    throw new Exception('Cannot find the file '.$file);
                } else {
                    if($require){
                        require_once(PostSections::pluginDir() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file);
                    }else{
                        include_once(PostSections::pluginDir() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file);
                    }
                }
        }
        public static function setErrors(){
            PostSections::$errors=new Errorlog();
        }
        public static $errors=null;
        protected $PostSectionsvisitor;
        protected $PostSectionsadmin;
        function __construct() {
            if(PostSections::$errors == null){PostSections::setErrors();}
            try {
                if (is_admin()) {
                    if(PostSections::$PostSectionsVersion > get_option('PostSectionsVersion')){PostSections::update();}
                    PostSections::getFile('ui', 'PostSectionsAdmin.php');
                    $this->PostSectionsadmin = new PostSectionsAdmin();
                    add_action('admin_menu', array($this, 'addMenu'));
                }
                    PostSections::getFile('ui', 'PostSectionsVisitor.php');
                    $this->PostSectionsvisitor = new PostSectionsVisitor();
            }catch (Exception $e) {
                PostSections::$errors->log($e->getMessage());
                    $current = get_settings('active_plugins');
                    array_splice($current, array_search( $_POST['plugin'], $current), 1 ); // Array-function!
                    update_option('active_plugins', $current);
                    header('Location: plugins.php?deactivate=true');
            }
        }
        function __destruct() {
            //Console::logSpeed("postsections out");
            if($this->debugMode == true) $this->profiler->display($this->db);
        }
            function addMenu(){
                if (function_exists('add_menu_page')) {
                    $this->PostSectionsadmin->addMenu();
                }
            }
    }
    class Errorlog{
        public function __construct(){  }
        public function log($msg){
            $file=fopen(PostSections::pluginDir().'errors.txt', 'a');
            fwrite($file,$msg);
            fwrite($file, ';');
            fclose($file);
        }
    }
   $PC=new PostSections();
}
?>