<?php
abstract class Skeleton Extends \Site\Bones {
    
    private $config;
    
    public function __construct($title='', $forceSSL=FALSE) {
        parent::__construct('elswh.re URL Shortener', $forceSSL);
        
        $this->config = new \Moshpit\Config();
        $this->config->load($_SERVER['DOCUMENT_ROOT'].'/_config/config.php');
        
        $this->setTitle($title);
        $this->setURL('elswh.re');
        $this->addHead('<!-- meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" -->');
        $this->addHead('<meta name="Author" content="Aaron Howell" />');

        //$this->protectedArea('admin', 'admin/login');
        $this->addCSS($this->datestampfile('/css/normalize.css'));
        $this->addCSS($this->datestampfile('/css/main.css'));
        $this->addCSS($this->datestampfile('/styles/site.css'));
        $this->addCSS($this->datestampfile('/styles/holygrail.css.php'));

        $this->addJavascript('/js/vendor/modernizr-2.6.2.min.js');
        
        $this->addGoogleAnalytics($this->config->google_analytics, "");
                
        //$this->addJavascript('http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ecdf2407eb6f35b');
        
        //$this->addCSS($this->datestampfile('/styles/navigation.css'));
        $menu = new \Site\MenuItem();
        
        $menu->addSubmenu('Home',          '/');
        if ($this->getAdmin()->checkAuth()) {
            $menu->addSubmenu('Logout',    '/admin/login');
        } elseif ($this->isDev()) {
            $menu->addSubmenu('Login',    '/admin/login');
        }
        
        if ($this->isDev()) {
            $menu->addSubmenu(':Prod:',    'http://'.$this->getURL().$_SERVER["REQUEST_URI"]);
        }
        
        $this->setMenu(&$menu);
    }
    
    final protected function outputHead() {
        ?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head><?php 
            foreach ($this->getHead() as $value) {
        ?>
        <?php echo $value."\n";
            }
        ?>
        
    </head>
    <body>
        <div id="main" class="container">
            <div id="header" class="container">
            <?php if ($this->isDev()) : ?>
                <div class="fonted error" style="top:0.5em;left:0.5em;font-size:large;position:absolute;">Dev</div>
                <?php if ($this->isMobile()) : ?>
                <div class="fonted error" style="top:1.5em;left:0.5em;font-size:large;position:absolute;">Mobile</div>
                <?php endif; ?>
            <?php endif; ?>                
                <div id="header_logo">
                    <a href="/">
                        <h1><?php echo $this->getTitleBase() ; ?></h1>
                        <!-- img src="/images/header_logo.png" title="<?php echo $this->getTitleBase() ; ?>" alt="<?php echo $this->getTitleBase(); ?>" / -->
                    </a>
                </div>
                    <?php $this->getMenu()->outputMenu(); ?>
                <hr />
            
                <!--[if lt IE 7]>
                    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
                <![endif]-->

                <!-- Add your site or application content here -->
                <p>Hello world! This is HTML5 Boilerplate.</p>  
            </div><!-- end header -->

<?php
    }
    
    abstract protected function outputMainColumn();
    protected function outputLeftColumn() {
        ?>
            &nbsp;
        <?php
    }
     
    protected function outputRightColumn() {
    ?>
        &nbsp;
    <?php
    }
    
    protected function outputBody() {
        ?>
             <div id="body" class="container">
                <div id="colmask">
                    <div id="colmid">
                        <div id="colleft">
                            <div id="mid_feed">
                            <?php
                                $this->outputMainColumn();
                            ?>
                            </div>
                            <div id="left_feed">                
                            <?php
                                $this->outputLeftColumn();
                            ?>
                            </div>
                            <div id="right_feed">                
                            <?php
                                $this->outputRightColumn();
                            ?>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        $this->outputAddThis();
    }

    final protected function outputFooter() {
?>
            <div id="footer" class="container">
                <span id="copyright">
                    &copy; 20<?php if (date('y') != 12) { echo '12-'; } echo date('y'); ?> 
                    <?php echo $this->getTitleBase() ; ?>
                </span>
            </div><!-- end footer -->
        </div><!-- end main -->
        <div id="footer-space-hack">&nbsp;</div>
        <!-- H5BP Start -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.8.3.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <script>
            var _gaq=[['_setAccount','<?php echo $this->config->google_analytics; ?>'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
        <!-- H5BP End -->
    </body>
</html> <!-- -->
<?php
    }
    
    protected function outputAddThis() {
        return TRUE;
        if (!$this->getAdmin()->checkAuth()) {
            ?>
            <div id="addthis">
                <div class="addthis_toolbox addthis_default_style">
                    <p style="clear:both;">
                        <a 
                            class="addthis_button_facebook_like" 
                            fb:like:layout="button_count" 
                            fb:like:href="https://www.facebook.com/<?php echo $this->config->facebook->user; ?>"></a>
                    </p>
                    <p style="clear:both;">
                        <a class="addthis_button_tweet" 
                           tw:via="<?php echo $this->config->twitter->user; ?>" 
                           tw:url="http://<?php echo $this->getURL(); ?>" 
                           tw:text="It's not wrong to be strong!"></a>
                    </p>
                    <p style="clear:both;">
                        <a href="https://twitter.com/<?php echo $this->config->twitter->user; ?>" 
                        class="addthis_button_twitter_follow_native addthis_bubble_style" 
                        tf:screen_name="<?php echo $this->config->twitter->user; ?>" 
                        tf:show-screen-name="false"></a>
                    </p>
                    <p style="clear:both;">
                        <a class="addthis_button_google_plusone" 
                           g:plusone:size="small" 
                           g:plusone:href="http://<?php echo $this->getURL(); ?>"></a>
                    </p>
                    <p style="clear:both;">
                        <a class="addthis_button_pinterest"></a>
                    </p>
                    <p style="clear:both;">
                        <a class="addthis_button_compact"></a>
                    </p>
                </div>
            </div>
            <?php
        }
    }
   
}
?>