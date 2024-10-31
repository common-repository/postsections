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
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    die('You are not allowed to call this page directly.');
}
//#################################################################
if (!class_exists('PostSectionsAdmin')) {

    /**
     * Main class for the plugin
     *
     * sets up all the links into wordpress and provides access to the output functions
     */
    class PostSectionsAdmin {
        protected $contentID;
        protected $leftID;
        protected $rightID;
        protected $postsectionlength;
        protected $postsectionheight;
        protected $posttextsize;
        protected $checker;
        function  __construct() {
            //Console::logSpeed("Admin panel in");
            //Console::logMemory($this,"Post Admin");
            //add_action('admin_enqueue_scripts', array($this, 'scripts'));
            PostSections::getFile('helper', 'PostSectionCheck.php');
            $this->checker=new PostSectionCheck();
            add_action('admin_head', array($this, 'addHeadLinks'), 12);
            update_option('PostSectionsVersion', PostSections::$PostSectionsVersion);
            if(!get_option('postsectionsettings')){
                update_option('postsectionsettings','pre;navcontent;post;250;0;em;12;px');
                }
            else{
                $this->init();
            }
            add_action('media_buttons', array($this, 'add_sections'), 11);
            add_action('admin_head', array($this, 'add_button_js'));
        }
        /**
         *
         * @return <type> 
         */
        private function init(){
            $settings=explode(';', get_option('postsectionsettings'));
            if($settings){
                $this->leftID=$settings[0];
                $this->contentID=$settings[1];
                $this->rightID=$settings[2];
                $this->postsectionlength=$settings[3];
                $this->postsectionheight=$settings[4].':'.$settings[5];
                $this->posttextsize=$settings[6].':'.$settings[7];
                return true;
            }
        }
        public function addMenu(){
            add_submenu_page('tools.php', 'PostSections Admin', 'PostSections Config', 'manage_options', 'PostSections', array($this, 'adminPanel'));
        }
        
        /**
         * Adds a shortcode selection to the WP editor
         * Thanks to Kevin Chard for the tidy bit of shortcode code
         * Adapted from:
         * @author Kevin Chard
         * @link http://wpsnipp.com/index.php/functions-php/update-automatically-create-media_buttons-for-shortcode-selection/
         */
        function add_sections() {
            echo '<input id="section_display" type="button" value="Section Display">';
        }
        /**
         * Adds javascript to update posts with selected shortcode
         * Thanks to Kevin Chard for the tidy bit of shortcode code
         * Adapted from:
         * @author Kevin Chard
         * @link http://wpsnipp.com/index.php/functions-php/update-automatically-create-media_buttons-for-shortcode-selection/
         */
        function add_button_js() { {
                echo '<script type="text/javascript">
                        jQuery(document).ready(function(){
                        jQuery("#section_display").click(function() {
                                var value="[sections]";
                                        send_to_editor(value);
                                          return false;
                                });
                        });
                    </script>';
                }
        }
        function adminPanel(){
            echo $this->output();
        }
        /**
         * Provides links to help and advice elswhere on www
         * @param string $section left or right
         * @return string html
         * @todo implement
         */
        function help($section){
            switch($section){
                case 'left':
                    return "";
                case 'right':
                    return "";
            }
        }
        /**
         * Compiles admin panel
         * @return html
         */
        function output(){
            if(isset($_POST['sectionlength'])){
                $content= $this->setLength($this->checker->check($_POST['setsectionlength']));
            }
            if(isset($_POST['sectionnames'])){
                $content= $this->setNames();
            }
            if(isset($_POST['sectionheight'])){
                $content=$this->setHeight($this->checker->check($_POST['setsectionheight']));
            }
            if(!(isset($_POST['sectionlength'])|| isset($_POST['sectionnames'])|| isset($_POST['sectionheight']))){
            $content=$this->getForm();
            }
            $output="<div id='leftads'>".$this->help('left')."</div><div id='center'>";
            $output.="<div id='postsectionsadmin'><a href='tools.php?page=PostSections' title='' >PostSections Config</a>";
            $output.= "<p>Best advice regarding blog posts is to have about 400 words per post.  This is because attention is lower and time is shorter for web surfers than typically when reading a book.</p><br/>";
            $output.= "<p>You may want even shorter sections.  Between 250 to 500 words may be optimum.  Please do give feedback on what works for you.</p><br/>";
            $output.= "<p>Simply enter a figure below and the plugin will help wordpress do the rest</p><br/>";
            $output.=$content;
            $output.="</div>";
            $output.="</div><div id='rightads' >".$this->help('right')."</div>";
            return $output;
        }
        /**
         * Compiles form
         * @return string
         */
        function getForm(){
            $output.= "<br/><form name='sectionnamesform' action='tools.php?page=PostSections' method='POST'>";
            $output.= "<label class='left' for='setcontentid'>Set content id</label><input class='right'  type='text' name='setcontentid' value='".$this->contentID."'/><br/><br/>";
            $output.= "<label class='left' for='setleftid'>Set previous section button id</label><input class='right'  type='text' name='setleftid' value='".$this->leftID."'/><br/><br/>";
            $output.= "<label class='left' for='setrightid'>Set next section button id</label><input class='right'  type='text' name='setrightid' value='".$this->rightID."'/><div class='clear'></div>";
            $output.= "<input type='submit' name='sectionnames' class='right psbutton' value='Set Section Style Names' /></form><br/><br/>";
            $output.= "<br/><hr /><form name='sectionlengthform' action='tools.php?page=PostSections' method='POST'>";
            $output.= "<label class='left' for='setsectionlength'>Set section length in words</label><input type='text' class='right' name='setsectionlength' value='".$this->postsectionlength."'/>";
            $output.= "<div class='clear'></div><input name='sectionlength' type='submit' class='right psbutton' value='Set Section Length' /></form><br/><br/>";//OR<hr /><br/><br/>";
            /**$output.= "<form name='sectionheightform' action='tools.php?page=PostSections' method='POST'>";
            for($o=1;$o<=2;$o++){
                ($o==1)? $name="set_area_height" : $name="set_text_size";
                $output.= "<label class='left' for='".$name."'>". ucfirst(str_replace('_', ' ', $name))."</label>";
                for($j=0;$j<=3;$j++){
                $output.= "<div class='right' ><select class='left' name='".$name.$j."' >";
                    for($i=0;$i<=9;$i++){
                        $output.="<option value='".$i."'>".$i."</option>";
                    }
                $output.="</select><div class='clear'></div></div>";
                }
                ($o==1)? $output.= "<br /><br/>
                    <label class='left' for='setsizetype'>Set Area Measurement Unit</label>
                    <div class='right' ><select class='right' name='setsizearea' >"
                :
                         $output.="<br/><br/>
                             <label class='left' for='setsizetype'>Set Text Measurement Unit</label>
                            <div class='right' ><select class='left' name='setsizetype' >";
            $output.= "
                    <option value='em'>em</option>
                    <option value='pt'>pt</option>
                    <option value='px'>px</option>
                    <div class='clear'></div></select><div class='clear'></div></div><br /><br/>";
            }
            $output.= "<input name='sectionheight' type='submit' class='right psbutton' value='Set Text Size' /><br/></form><br/><hr />";
            **/return $output;
        }
        function updateSettings(){
            $settings=$this->leftID.';'.  $this->contentID.';'.  $this->rightID.';'.  $this->postsectionlength.';'.  $this->postsectionheight.';'.  $this->posttextsize;
            update_option('postsectionsettings',$settings);
            return $this->init();
        }
        /**
         * Process request to change length
         * @param int $length
         * @return string
         */
        function setLength($length){
            if(intval($length)>=1 && intval($length)<=1000){
                $this->postsectionlength=$length;
                $this->postsectionheight='0:em';
                $this->posttextsize='0:px';
                if($this->updateSettings(3,$length)){
                    return "<p class='result'>You have successfully set your section wordcount to: ".$length."</p>";
                }else{
                    return "<p class='result'>The wordcount could not be set to".$length."</p>";
                }
            }else{
                return "<p class='result'>".$length." may be too long or not a valid value</p>";
            }
        }
        /**
         * Process request to change length
         * @param int $length
         * @return string
         */
        function setNames(){
            $this->contentID=$this->checker->clean($_POST['setcontentid'],'ID');
            $this->leftID=$this->checker->clean($_POST['setleftid'],'ID');
            $this->rightID=$this->checker->clean($_POST['setrightid'],'ID');
            if($this->updateSettings()){
                    return "<p class='result'>You have successfully set your ids to:<br />
                        Content class is ".$this->contentID."<br />
                        Left class is ".$this->leftID."<br />
                        Right class is ".$this->rightID."<br /> </p>";
            }else{
                return "<p class='result'>The ids could not be set to </p>";
                }
        }
        
        /**
         *
         * @param int $l length of sections
         * @return boolean if update is successful
         */
        function setOption($option,$value){
            update_option($option,$value);
            if(get_option($option)==$value){
                return true;
            }
            return false;
        }
        function addHeadLinks(){
            echo '<link rel="stylesheet" type="text/css" href="' . PostSections::pluginURL() . 'styles.css" />';
        }
    }
}
?>