<?php
/**
 * @package post-sections
 */
//#################################################################
// Stop direct call
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.');}
//#################################################################
if (!class_exists('PostSectionsVisitor')) {
    /**
     * Main class for the plugin
     *
     * sets up all the links into wordpress and provides access to the output functions
     */
    class PostSectionsVisitor {
        protected $contentID;
        protected $leftID;
        protected $rightID;
        protected $sectionlength;
        protected $postsectionheight;
        protected $posttextsize;
        function __construct() {
            add_action('wp_enqueue_scripts', array($this, 'scripts'));
            add_action('wp_ajax_nopriv_postSection', array($this, 'postSection'));
            add_action('wp_ajax_postSection', array($this, 'postSection'));
            add_action('wp_ajax_nopriv_bookmark', array($this, 'bookmark'));
            add_action('wp_ajax_bookmark', array($this, 'bookmark'));
            add_shortcode('sections', array($this, 'addsection_func'));
                $settings=explode(';', get_option('postsectionsettings'));
                $this->leftID=$settings[0];
                $this->contentID=$settings[1];
                $this->rightID=$settings[2];
                $this->sectionlength=$settings[3];
                $this->postsectionheight=$settings[4].':'.$settings[5];
                $this->posttextsize=$settings[6].':'.$settings[7];
                //Console::logSpeed("Visitor in");
            //Console::logMemory($this,"Post Sections Visitor");
        }
        /**
         *  Links filter to shortcode
         * @param <type> $atts
         * @param <type> $content
         */
        function addsection_func($atts, $content = null){
            //Console::logMemory($content,"content");
           add_filter( 'the_content', array($this,'getSection' ),999);
        }
        /*
         * Calls the wordpress script loader to make sure it's available when the scripts run
         */
        function scripts() {
            try{
            wp_register_script( 'jQuery1-6-2', PostSections::pluginURL()."ui/jQuery1-6-2.js");
            wp_register_script( 'relay', PostSections::pluginURL()."ui/relay.js");
            wp_enqueue_script('jQuery1-6-2', PostSections::pluginURL()."ui/jQuery1-6-2.js", array('jQuery'), '1.6-2');
            wp_enqueue_script('relay', PostSections::pluginURL()."ui/relay.js", array('jQuery1-6-2'), PostSections::$PostSectionsVersion);
            }catch(Exception $e){
                                PostSections::$errors->log($e->getMessage());}

        }
        /**
         * Function to check for linebreaks that would add to space needed in display
         * @param <type> $content 
         */
        function getLineBreaks($content){}
        /**
         *
         * @param string $content
         * @return int number of sections depending on the maximum wordcount set by the admin
         */
        function getMaxParas($content){
            $count=str_word_count($content);
            if(($count) > $this->sectionlength){
                return ceil($count/$this->sectionlength);
            }else{
                return 1;
            }
        }
        function bookmark(){
            die("You will soon be able to add this bookmark to a reading list on this blog if you are a registerd user and logged in.");
        }
        /**
         *  Make sure wordpress shortcodes function does its thing
         * @param <type> $content
         * @return <type>
         */
        function pshtmlentities($content){
            return do_shortcode($content);
        }
        /**
         *
         * @global <type> $id
         * @global string $permalink
         * @global string $the_title
         * @param string $content
         * @param string $charlength
         * @param int $maxParas
         * @return string html to browser
         */
        function getSection(){
                //if(!ob_start("ob_gzhandler")) ob_start();
                if(isset($_GET['section'])&& isset($_GET['wppost']) && isset($_GET['wppostsections']) && ($_GET['section'])!=0){
                    echo $this->theSection(intval($_GET['wppost']),true,intval($_GET['section']),intval($_GET['wppostsections']));
                }else{
                     echo $this->theSection(0,false);
                }
            //if(!ob_get_contents()){echo($this->theSection(0,false));}
            //die();
        }
        /**
         *
         * @global <type> $post
         * @global <type> $shortcode_tags
         * @param int $sec section
         * @param  $idm post id
         * @return string 
         */
       function theSection($idm,$set,$sec=1,$sections=1){
            //Console::logSpeed("theSection in");
            global $post,$shortcode_tags;
            $after=2;$prev=1;$end="";$start="";
            if(!$set){
                $content=$post->post_content;
                $id=$post->ID;
            }else{
                $id=$idm;
                $post=get_post($id);
                $content=$post->post_content;
            }
            //$maxParas=1;
            if(is_numeric($sections)&&$sections===1){$maxParas=$this->getMaxParas($content);}else{$maxParas=$sections;}
                $content=$this->getSubstring($content, $sec, $maxParas);
                $permalink=get_permalink($id);
                $content=str_replace(']]>', ']]&gt;',$content);
                //$charlen=round((strlen($content))/$maxParas);
                $current=$sec;
                        if($sec<=$maxParas ){
                            if($maxParas===1){$start=" start";}else{
                                $start="";}
                            if($sec==$maxParas){
                                $after=$maxParas;
                                $prev=$sec-1;
                                $end=" end";
                                $content.="<br /><br /><a class='returntostart' href='".$permalink."' >Return to START</a>";
                            }else{
                                $after=$sec+1;
                                $prev=$sec-1;
                                if($prev===0||$prev=='0'){
                                    $prev=1;
                                    $end="";
                                    $start=" start";
                                }
                            }
                        }
                        $stags="a.".$this->leftID.", a.".$this->rightID;
                        $div=$this->contentID;
                $return = "<script type='text/javascript' >var postsectionsurl=\"".get_option('siteurl')."/wp-admin/admin-ajax.php\";var postsectiontags=\"".$stags."\";var postsectiondiv=\"#post-".$id." .entry\"; var psloc=\"".$permalink."?section=".$current."%26wppost=".$id."\";var pstitle = \"Part ".$current." of ".$post->post_title."\";</script>";
                $bookmark="<em class='bookmark'>Part ".$current." of this post -<a id='postsectionbookmark' href='".$permalink."?section=".$current."&amp;wppost=".$id."' > Bookmark.</a></em><br />";
                $return.="<div class='prev section'><a class='". $this->leftID." custompostsectionnav".$start."' href='".$permalink."?section=".$prev."&amp;wppost=".$id."&amp;wppostsections=".$maxParas."' title='previous section of this post' ><span>Before</span></a></div><div class='center'><div class='". $this->contentID."' >".$this->pshtmlentities($bookmark.$content)."</div></div><div class='after section'><a  class='". $this->rightID." custompostsectionnav".$end."' href='".$permalink."?section=".$after."&amp;wppost=".$id."&amp;wppostsections=".$maxParas."' title='next section of this post' ><span>After</span></a></div><div class='clear'></div>";
                //Console::logSpeed("theSection out");
                return $return;
       }
        /**
         * Gets the relevant post and returns html to the calling ajax function
         * @uses $wpdb
         */
        function postSection(){
            if(!ob_start("ob_gzhandler")) ob_start();
            if(isset($_POST['section'])&& isset($_POST['wppost']) && isset($_POST['wppostsections']) && ($_POST['section'])!=0){
                echo $this->theSection(intval($_POST['wppost']),true,intval($_POST['section']),intval($_POST['wppostsections']));
            }else{
                echo $this->theSection(0,false);
            }
                //header('Content-Type: text/html');
            if(!ob_get_contents()){die($this->theSection(0,false));}
            die();
        }
       /**
         * Gets the content of the requested section from the content
         * @param string $content
         * @param int $section from the browser request
         * @param int $chrs calculated
         * @param int $paras
         * @return string
         */
        function getSubstring($content,$s,$paras){
            //$keep count of words per sentence
            $paraArr=preg_split('(\.(\s){1,2})', $content,null);//array of sentences
            $section="";$temp="";
            $startkey=$this->sectionlength*($s-1);
            foreach ($paraArr as $key => $value) {
                if(str_word_count($temp)<=$startkey && str_word_count($temp.$value)<$startkey){
                    $temp.=$value.".  ";
                }else{
                    if(str_word_count($section)< $this->sectionlength && str_word_count($section.$value)< $this->sectionlength ){
                        $section.=$value.".  ";
                    }
                }
            }
            return $section;
        }
    }
}
?>