<?php
//+----------------------------------------------------------------+
//| CONTACT.PHP
//+----------------------------------------------------------------+

session_start();
$response=array();

//+----------------------------------------------------------------+
//| email settings
//+----------------------------------------------------------------+

$to = "your@email.com"; /* you email address */
$subject ="Contact form message"; /* email subject */
$message ="You received a mail via your website contact form\n\n"; /* email messege prefix */



//+----------------------------------------------------------------+
//| post data validation
//+----------------------------------------------------------------+

if ($_POST) {
    
    if($_POST['form_type'] === 'quote') {
        $_SESSION['form_type'] = 'quote';
    } else {
        $_SESSION['form_type'] = 'contact';
    }
    
    /* clean input & escape the special chars */
    foreach($_POST as $key=>$value) {
        if(ini_get('magic_quotes_gpc')) { $_POST[$key]=stripslashes($_POST[$key]); }
        $_POST[$key]=htmlspecialchars(strip_tags(trim($_POST[$key])), ENT_QUOTES);
    }
    
    /* check name */
    if (!strlen($_POST['name'])) {
        $response['message']['name']="Field <b>Name</b> is required.";
    }
    /* check email */
    if (!strlen($_POST['email'])) {
        $response['message']['email']="Field <b>Email</b> is required.";
    } elseif (!preg_match("/^[\w-]+(\.[\w-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i", $_POST['email'])) {
        $response['message']['email']="Invalid e-mail address."; 
    }
    /* check website (if given) */
    if (strlen($_POST['website']) && !filter_var($_POST['website'], FILTER_VALIDATE_URL)) {
        $response['message']['website']="Invalid <b>Website</b> URL.";
    }
    
              
    
    // additional conditions if sending a quote
    if( $_POST['form_type'] === 'quote' ) {
        // phone
        if (!strlen($_POST['phone'])) {
            $response['message']['phone']="Field <b>Phone</b> is required.";
        }
        // project
        if (!strlen($_POST['project'])) {
            $response['message']['project']="Field <b>Project</b> is required.";
        }
        
        // service
        if ($_POST['serviceRequired'] == '-1') {
            $response['message']['serviceRequired']="Select a <b>Service</b>.";
        }
    }
    
    
    /* check message */
    if (!strlen($_POST['message'])) {
        $response['message']['message']="Field <b>Message</b> is required.";
    } 
     
    //* check captcha */
    if (!strlen($_POST['captcha'])) {
        $response['message']['captcha']="Field <b>Captcha</b> is required.";
    } elseif ($_POST['captcha']!=$_SESSION['captcha']) {
        $response['message']['captcha']="Invalid captcha.";  
    }    
    
    /* if no error */
    if (!isset($response['message'])) {
         $response['result']='success'; 
    } else {
         $response['result']='error';
    }
        
}
    

//+----------------------------------------------------------------+
//| send the email
//+----------------------------------------------------------------+

if (@$response['result']) {
    if ($response['result']=='success') {
        
        /* build the email message body */
        $message.= 'Sender name: '.$_POST['name']."\n";
        $message.= 'Sender email: '.$_POST['email']."\n";
        $message.= strlen($_POST['website']) ? 'Sender website: '.$_POST['website']."\n" : "Sender website: -\n";
        
        // if sending quote additional fields
        if( $_SESSION['form_type'] === 'quote' ) {
            $message.= 'Sender phone: '.$_POST['phone']."\n";
            $message.= 'Project name: '.$_POST['project']."\n";
            $message.= 'Sender requires service: '.$_POST['serviceRequired']."\n";
            $message.= 'Budget: '.$_POST['budget']."\n";
        }
            
        
        $message.= "\nMessage: \n".$_POST['message'];
        
        /* send the mail */
        if(mail($to, $subject,$message)){
            $response['message']['mail_sent']='Your <b>Message</b> has been sent successfully.';
        } else{
            $response['result']='error';
            $response['message']['mail_sent']='Something went wrong, please try again later.';
        }
    }
    /* if ajax request */
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') { 
        print json_encode($response);
        exit;
    } 
    /* if reqular http request */
    else {
        $_SESSION['reponse']=$response;
        $_SESSION['postdata']=$_POST;
        header('location: '.$_SERVER['PHP_SELF']); 
        exit;
    }
}

//+----------------------------------------------------------------+
//| functions
//+----------------------------------------------------------------+

function check_the_field( $name ) {
    return @($_SESSION['reponse']['result']=='error' && ( isset($_SESSION['reponse']['message'][$name]) || $name==='captcha' ));
}
function error_class( $name ) {
    if ( check_the_field($name) ) {
        echo ' error';
    }
}
function field_val( $name ) {
    if ( ! check_the_field( $name )) {
        $val = @$_SESSION['postdata'][$name];
        echo ' value="' . $val . '" ';
    }
}
function attr_checked( $name, $value, $default = false) {
    $val = @$_SESSION['postdata'][$name];
    if( $val == $value || ( ( empty($_SESSION['postdata']) || $_SESSION['form_type'] != 'quote' ) && $default ) ) {
        echo ' checked';
    }
}
function attr_selected( $name, $value, $default = false) {
    $val = @$_SESSION['postdata'][$name];
    if( $val == $value || ( ( empty($_SESSION['postdata']) || $_SESSION['form_type'] != 'quote' ) && $default ) ) {
        echo ' selected';
    }
}


//+----------------------------------------------------------------+
//| create session data
//+----------------------------------------------------------------+

$_SESSION['no1'] = rand(1,10);  /* first number */
$_SESSION['no2'] = rand(1,10);  /* second number */
$_SESSION['captcha'] = $_SESSION['no1']+$_SESSION['no2'];   /* captcha data */
if( empty( $_SESSION['form_type'] ) ) {
    $_SESSION['form_type'] = 'contact';
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
    <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <title>W Balls HTML Template</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="stylesheets/bootstrap.css" rel="stylesheet">
        <link href="stylesheets/responsive.css" rel="stylesheet">
        <link href="js/rs-plugin/css/settings.css" rel="stylesheet">
        <link href="stylesheets/main.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/apple-touch-icon/114x114.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/apple-touch-icon/114x114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/apple-touch-icon/72x72.png">
        <link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon/57x57.png">
        <link rel="shortcut icon" href="favicon.ico">
    </head>

    <body>

        

        <div class="boxed-container">

            <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-head">
                    <div class="container">
                        <div class="header-padding" id="shrinkableHeader">
                            <div class="row">
                                <div class="span6">
                                    <a class="brand" href="index.html" title=""><img src="images/logo.gif" alt=""></a>
                                </div>
                                <div class="span6">
                                    <div class="pull-right">
                                        <div class="call-us">
                                            CALL US: (811) 108-4000
                                        </div>
                                        <div class="social">
                                            <span><a href="#" class="social-icon-rss"></a></span>
                                            <span><a href="#" class="social-icon-twitter"></a></span>
                                            <span><a href="#" class="social-icon-linkedin"></a></span>
                                            <span><a href="#" class="social-icon-facebook"></a></span>
                                            <span><a href="#" class="social-icon-pinterest"></a></span>
                                            <span><a href="#" class="social-icon-youtube"></a></span>
                                            <span><a href="#" class="social-icon-vimeo"></a></span>
                                            <span><a href="#" class="social-icon-flickr"></a></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="navbar-menu-line">
                    <div class="container">
                        <button type="button" class="btn btn-navbar btn-green" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="nav-collapse collapse">
                            <ul class="nav" id="mainNavigation">
                                <li class="divider-vertical"></li>
                                <li class="dropdown">
                                    <a href="#"><span>Home</span><small>slogan here</small></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="index.html">Home - Original</a>
                                        </li>
                                        <li>
                                            <a href="index-blue.html">Home - Blue</a>
                                        </li>
                                        <li>
                                            <a href="index-orange.html">Home - Orange</a>
                                        </li>
                                        <li>
                                            <a href="index-red.html">Home - Red</a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <li class="divider-vertical"></li>
                                <li>
                                    <a href="about.html"><span>About</span><small>slogan here</small></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li class="dropdown">
                                    <a href="#"><span>Features</span><small>slogan here</small></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="styles.html">Styles</a>
                                        </li>
                                        <li>
                                            <a href="shortcodes.html">Shortcodes</a>
                                        </li>
                                        <li>
                                            <a href="pricing.html">Pricing Tables</a>
                                        </li>
                                        <li>
                                            <a href="404.html">404 Error Page</a>
                                        </li>
                                        <li>
                                            <a href="search-results-2.html">Search with Results</a>
                                        </li>
                                        <li>
                                            <a href="search-results-1.html">Search without Results</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="divider-vertical"></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle"><span>Portfolio</span><small>slogan here</small></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="portfolio-2-columns.html">Portfolio 2 Columns</a>
                                        </li>
                                        <li>
                                            <a href="portfolio-3-columns.html">Portfolio 3 Columns</a>
                                        </li>
                                        <li>
                                            <a href="portfolio-4-columns.html">Portfolio 4 Columns</a>
                                        </li>
                                        <li>
                                            <a href="portfolio-single-project.html">Portfolio Single Project</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="divider-vertical"></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle"><span>Blog</span><small>slogan here</small></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="blog.html">Blog</a>
                                        </li>
                                        <li>
                                            <a href="blog-single-post.html">Blog Single</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="divider-vertical"></li>
                                <li class="active mobile-active">
                                    <a href="contact.php"><span>Contact</span><small>slogan here</small></a>
                                </li>
                            </ul>
                            <form class="navbar-form pull-right" action="search-results-2.html">
                                <div class="search-form">
                                    <input class="search-input input-block-level" type="text" placeholder="Search...">
                                    <button type="submit" class="btn btn-search"></button>
                                </div>
                            </form>
                        </div><!--/.nav-collapse -->
                    </div>
                </div>
            </div>

            <div class="fullwidthbanner-subpage-container google-map">

                <div class="parallax-slider">
                	<div id="gmap"></div>
                	
                	<div class="container">
                	    <div class="slide slide-map">
                	        <h1>Contact</h1>
                	        <div class="clearfix"></div>
                	        <h2>Pri ad ipsum altera, quo natum</h2>
                	        <div class="clearfix"></div>
                	        <h2>bonorum inermis at.</h2>
                	    </div>
                	</div>
                </div>

            </div>
            <!-- /fullwidthbanner-container -->

            <div class="container content">

                <div class="row no-bottom">
                    <div class="span9">

                        <div class="row">

                            <div class="span9">
                                <div class="page-header arrow-grey">
                                    <h2 id="moveToForm">Get in touch!</h2>
                                </div>
                            </div>

                            <div class="span9">
                                <p class="push-down-30">
                                    Splendide philosophia et est, cum at probo minimum omnesque, falli libris has id. Ad facer pertinax vel, eum nevemu molestie euripidis consectetuer. Tale noluisse signiferumque te vix, graecis evertitur temporibus his ut, vis nesi nulla nemore splendide. Salutandi scribentur efficiantur ad his, aliquam deleniti salutandi ius id.
                                </p>
                            </div>

                        </div>

                        <div class="row">
                            
                            <div class="span9">
                                <?php
                                if (isset($_SESSION['reponse']) && $_SESSION['reponse']['result']=='success') { ?>
                                <p>
                                    <div class="alert alert-success">
                                        <button class="close smooth-close-parent" type="button">
                                            <i class="icon-close-dark"></i>
                                        </button>
                                        <?php echo $_SESSION['reponse']['message']['mail_sent']; ?>
                                    </div>
                                </p>
                                <?php
                                    unset($_SESSION['reponse']);
                                    unset($_SESSION['postdata']);
                                ?>
                                <?php } elseif (@$_SESSION['reponse']['result']=='error' ) { ?>
                                    <?php foreach( $_SESSION['reponse']['message'] as $msg ) { ?>
                                        <div class="alert alert-error">
                                            <button class="close smooth-close-parent" type="button">
                                                <i class="icon-close-dark"></i>
                                            </button>
                                            <?php echo $msg; ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            
                            <div class="span9 contact-form">

                                <div class="page-header arrow-grey">
                                    <h2>Contact and Quote form</h2>
                                </div>

                                <ul class="nav nav-tabs" id="contactFormSlider">
                                    <li<?php echo $_SESSION['form_type'] === 'contact' ? ' class="active"' : ''; ?>>
                                        <a href="#first" data-toggle="tab">Contact</a>
                                    </li>
                                    <li<?php echo $_SESSION['form_type'] === 'quote' ? ' class="active"' : ''; ?>>
                                        <a href="#second" data-toggle="tab">Request a Quote</a>
                                    </li>
                                </ul>

                                <div class="tab-content mobile-spacing-10" id="contactFormsContainer">
                                    <div class="inner-slide-pane"<?php echo $_SESSION['form_type'] === 'quote' ? ' style="margin-left: -100%;"' : ''; ?>>
                                        <div class="slide-pane">
                                            <form method="post" action="contact.php#moveToForm" novalidate>
                                                <input type="hidden" name="form_type" value="contact" />
                                                <div class="controls controls-row row">
                                                    <div class="span3 control-group<?php error_class('name'); ?>">
                                                        <label for="name" class="control-label">Name <span class="green">*</span></label>
                                                        <input id="name" name="name" type="text" class="span3" <?php field_val('name'); ?>>
                                                    </div>
                                                    <div class="span3 control-group<?php error_class('email'); ?>">
                                                        <label for="email" class="control-label">E-mail <span class="grey">(will not be published)</span> <span class="green">*</span></label>
                                                        <input id="email" name="email" type="text"" class="span3" <?php field_val('email'); ?>>
                                                    </div>
                                                    <div class="span3 control-group<?php error_class('website'); ?>">
                                                        <label for="website" class="control-label">Website <span class="grey">(e.g. http://website.com)</span></label>
                                                        <input id="website" name="website" type="text" class="span3" <?php field_val('website'); ?>>
                                                    </div>
                                                </div>
                                                <div class="controls control-group<?php error_class('message'); ?>">
                                                    <label for="message" class="control-label">Your Message <span class="green">*</span></label>
                                                    <textarea id="message" name="message" class="span9" rows="8"><?php echo @$_SESSION['postdata']['message']; ?></textarea>
                                                </div>

                                                <div class="controls">
                                                    <button id="contact-submit-2" type="submit" class="btn btn-green pull-left move-9">
                                                        Send E-mail
                                                    </button>
                                                    <div class="pull-right control-group<?php error_class('captcha'); ?>">
                                                        <span class="are-you-label control-label">Are you human? <?php echo $_SESSION['no1']; ?> + <?php echo $_SESSION['no2']; ?> = </span>
                                                        <input name="captcha" type="text" class="input-are-you-human">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="slide-pane">
                                            <form method="post" action="contact.php#moveToForm" novalidate>
                                                <input type="hidden" name="form_type" value="quote" />
                                                <div class="controls controls-row row">
                                                    <div class="span3 control-group<?php error_class('name'); ?>">
                                                        <label for="name2">Name <span class="green">*</span></label>
                                                        <input id="name2" name="name" type="text" class="span3" <?php field_val('name'); ?>>
                                                    </div>
                                                    <div class="span3 control-group<?php error_class('email'); ?>">
                                                        <label for="email2">E-mail <span class="grey">(will not be published)</span> <span class="green">*</span></label>
                                                        <input id="email2" name="email" type="text"" class="span3" <?php field_val('email'); ?>>
                                                    </div>
                                                    <div class="span3 control-group<?php error_class('website'); ?>">
                                                        <label for="website2">Website <span class="grey">(e.g. http://website.com)</span></label>
                                                        <input id="website2" name="website" type="text" class="span3" <?php field_val('website'); ?>>
                                                    </div>
                                                </div>
                                                <div class="controls controls-row row">
                                                    <div class="span3 control-group<?php error_class('phone'); ?>">
                                                        <label for="phone">Phone <span class="green">*</span></label>
                                                        <input id="phone" name="phone" type="text" class="span3" <?php field_val('phone'); ?>>
                                                    </div>
                                                    <div class="span3 control-group<?php error_class('project'); ?>">
                                                        <label for="projectName">Project Name <span class="green">*</span></label>
                                                        <input id="projectName" name="project" type="text" class="span3" <?php field_val('project'); ?>>
                                                    </div>
                                                    <div class="span3 control-group<?php error_class('serviceRequired'); ?>">
                                                        <label for="selectService">Require Service <span class="green">*</span></label>
                                                        <select title='Select a Service...' class="input-block-level" id="selectService" name="serviceRequired">
                                                            <option value="-1"<?php attr_selected('serviceRequired', -1, true); ?>>Select a service ...</option>
                                                            <option value="Service One"<?php attr_selected('serviceRequired', 'Service One'); ?>>Service One</option>
                                                            <option value="Service Two"<?php attr_selected('serviceRequired', 'Service Two'); ?>>Service Two</option>
                                                            <option value="Service Three"<?php attr_selected('serviceRequired', 'Service Three'); ?>>Service Three</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="controls controls-row row">
                                                    <div class="span9 require-budget">
                                                        Require Budget <span class="green">*</span>
                                                        <br />
                                                        <label class="radio inline">
                                                            <input type="radio" value="500" name="budget"<?php attr_checked('budget', 500, true); ?>>
                                                            &lt; $500 </label>
                                                        <label class="radio inline">
                                                            <input type="radio" value="1000" name="budget"<?php attr_checked('budget', 1000); ?>>
                                                            &lt; $1000 </label>
                                                        <label class="radio inline">
                                                            <input type="radio" value="1500" name="budget"<?php attr_checked('budget', 1500); ?>>
                                                            &lt; $1500 </label>
                                                        <label class="radio inline">
                                                            <input type="radio" value="2000" name="budget"<?php attr_checked('budget', 2000); ?>>
                                                            $2000+ </label>
                                                    </div>
                                                </div>
                                                <div class="controls control-group<?php error_class('message'); ?>">
                                                    <label for="message2">Your Message <span class="green">*</span></label>
                                                    <textarea id="message2" name="message" class="span9" rows="8"><?php echo @$_SESSION['postdata']['message']; ?></textarea>
                                                </div>

                                                <div class="controls">
                                                    <button id="contact-submit-1" type="submit" class="btn btn-green pull-left move-9">
                                                        Send E-mail
                                                    </button>
                                                    <div class="pull-right control-group<?php error_class('captcha'); ?>">
                                                        <span class="are-you-label control-label">Are you human? <?php echo $_SESSION['no1']; ?> + <?php echo $_SESSION['no2']; ?> = </span>
                                                        <input name="captcha" type="text" class="input-are-you-human">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    unset($_SESSION['form_type']);
                                    unset($_SESSION['reponse']);
                                    unset($_SESSION['postdata']);
                                ?>
                            </div>

                        </div>
                        <!-- end row -->

                    </div>

                    <!-- Right Column -->

                    <div class="span3 right-column">

                        <div class="row">

                            <div class="span3">
                                <div class="page-header arrow-grey">
                                    <h2>Address Info</h2>
                                </div>
                            </div>

                            <div class="span3">

                                <address>
                                    1234 Address City, TS 56789
                                    <br>
                                    City, Country
                                </address>

                                <address>
                                    Tel: +01 2345 678
                                    <br>
                                    Fax: +01 2345 679
                                    <br>
                                    <a href="mailto:#">hello@yoursite.com</a>
                                </address>

                            </div>

                        </div>
                        <!-- end row -->

                        <div class="row">

                            <div class="span3">
                                <div class="page-header arrow-grey">
                                    <div class="row">
                                        <div class="span2">
                                            <h2>Popular Posts</h2>
                                        </div>

                                        <div class="span1">
                                            <div class="pull-right nav-sidebar">
                                                <a href="#" class="nav-left" id="popularPostsLeft"></a><a href="#" class="nav-right" id="popularPostsRight"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="carouFredSel-vertical" data-nav="popularPosts">
                                <div class="span3 sidebar-post">
                                    <div class="picture">
                                        <a href="blog-single-post.html"> <img src="images/dummy/blog/popular_2.jpg" alt=""> <span class="img-overlay"> <span class="plus"><i class="icon-plus"></i></span> </span> </a>
                                    </div>
                                    <p>
                                        <a href="blog-single-post.html">Post with great lorem...</a>
                                    </p>
                                    <p>
                                        <small>4 days ago</small>
                                    </p>
                                </div><!-- end post -->

                                <div class="span3 sidebar-post">
                                    <div class="picture">
                                        <a href="blog-single-post.html"> <img src="images/dummy/blog/popular_3.jpg" alt=""> <span class="img-overlay"> <span class="plus"><i class="icon-plus"></i></span> </span> </a>
                                    </div>
                                    <p>
                                        <a href="blog-single-post.html">Et pri iisque apeirian come plectitur, site veri...</a>
                                    </p>
                                    <p>
                                        <small>39 days ago</small>
                                    </p>
                                </div><!-- end post -->

                                <div class="span3 sidebar-post">
                                    <div class="picture">
                                        <a href="blog-single-post.html"> <img src="images/dummy/blog/popular_4.jpg" alt=""> <span class="img-overlay"> <span class="plus"><i class="icon-plus"></i></span> </span> </a>
                                    </div>
                                    <p>
                                        <a href="blog-single-post.html">Id qui labitur feugiat verun atomorum, ex eos doming disputando...</a>
                                    </p>
                                    <p>
                                        <small>72 days ago</small>
                                    </p>
                                </div><!-- end post -->
                                <div class="span3 sidebar-post">
                                    <div class="picture">
                                        <a href="blog-single-post.html"> <img src="images/dummy/blog/popular_1.jpg" alt=""> <span class="img-overlay"> <span class="plus"><i class="icon-plus"></i></span> </span> </a>
                                    </div>
                                    <p>
                                        <a href="blog-single-post.html">Tale noluisse signiferu que te vix, graecis everti turei temporibus...</a>
                                    </p>
                                    <p>
                                        <small>27 days ago</small>
                                    </p>
                                </div><!-- end post -->
                            </div>

                        </div>
                        <!-- end row -->

                        <div class="row">

                            <div class="span3">
                                <div class="page-header arrow-grey">
                                    <h2>Work with Us</h2>
                                </div>
                            </div>

                            <div class="span3">

                                <p><img src="images/dummy/about/testimonial_2.jpg" style="width: 50px; height: 50po" alt="" class="slidebar-small-img pull-left"> Mazim vitae postulan cun nam, esse nonumy sed ex, at lorem reprin etus iquen suscipiantur his. Eum ea tatva sature dolores inciderint, intun trion habemus apeirian minimel.
                                </p>

                                <a href="contact.html" class="btn btn-small btn-green" type="button">
                                    Drop us a line
                                </a>

                            </div>

                        </div>
                        <!-- end row -->

                    </div>

                    <!-- end Right Column -->

                </div>
                <!-- end row -->

            </div>
            <!-- /container -->

            <div class="foot">
                <div class="container">

                    <div class="row">
                        <div class="span3 foot-item foot-item-green">
                            <div class="foot-item-green-inside">
                                <img src="images/footer-logo.png" alt="">
                                <span class="tel-text">(811) 108-4000</span>
                                <span class="support-text">24/7 Global Support</span>
                                <p>
                                    Scripta appellantur ullamcorer ut sit, ei vis adhuc caua opor. Ex est elitr indoctum eu.
                                </p>
                            </div>
                        </div>

                        <div class="span3 foot-item">
                            <h4>Newsletter</h4>
                            <p>
                                Subscribe to our newsletter and get latest news and exclusive offers straight to your inbox.
                            </p>
                            <form class="navbar-form">
                                <div class="subscribe-form">
                                    <input class="subscribe-input" type="text" placeholder="Subscribe...">
                                    <button type="submit" class="btn btn-subscribe">
                                        GO
                                    </button>
                                </div>
                            </form>
                            <p>
                                <small>No spam, we promise. For more info read <a href="#">PRIVACY POLICY</a>.</small>
                            </p>
                        </div>

                        <div class="span3 foot-item">
                            <h4>Vimeo</h4>
                            <p>
                                <iframe src="http://player.vimeo.com/video/24381927?title=0&amp;byline=0&amp;portrait=0&amp;color=15b994" width="226" height="127" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                            </p>
                            <p>
                                <small>Pro probo habeo debitis ei, ei dicit matic ponderum eos, ei forensibus</small>
                            </p>
                        </div>

                        <div class="span3 foot-item">
                            <h4 class="pull-left">News</h4>
                            <div class="clearfix"></div>
                            <ul class="tweet_list">
                                <li class="tweet_first tweet_odd">
                                    <span class="tweet_text">Just over 200 copies sold of Hairpress HTML <a href="http://themeforest.net/item/hairpress-html-template-for-hair-salons/3803346">themeforest.net/item/hairpress…</a> New theme coming sooooon!</span>
                                </li>
                                <li class="tweet_even">
                                    <span class="tweet_text">Everyone is talking about a hot new app called PRISM. I went to download it, and it turns out I already have it installed!</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <footer>
                <div class="container">
                    <div class="row">
                        <div class="span4">
                            &copy; 2012 W BALLS - Theme for You
                        </div>
                        <div class="span4 terms-privacy-links">
                            <a href="#">TERMS OF USE</a> | <a href="#">PRIVACY POLICY</a>
                        </div>
                        <div class="span4">
                            <div class="pull-right">
                                <div class="social">
                                    <a href="#" class="social-icon-rss"></a>
                                    <a href="#" class="social-icon-twitter"></a>
                                    <a href="#" class="social-icon-linkedin"></a>
                                    <a href="#" class="social-icon-facebook"></a>
                                    <a href="#" class="social-icon-pinterest"></a>
                                    <a href="#" class="social-icon-youtube"></a>
                                    <a href="#" class="social-icon-vimeo"></a>
                                    <a href="#" class="social-icon-flickr"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <a id="tothetop" href="#"> </a>
        </div>

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<!--  ==========  -->
<!--  = Isotope JS =  -->
<!--  ==========  -->
<script src="js/isotope/jquery.isotope.min.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = Slider Revolution =  -->
<!--  ==========  -->
<script src="js/rs-plugin/pluginsources/jquery.themepunch.plugins.min.js" type="text/javascript"></script>
<script src="js/rs-plugin/js/jquery.themepunch.revolution.min.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = Media Element and mp3 player =  -->
<!--  ==========  -->
<script src="js/mediaelementjs-skin/lib/mediaelement.js" type="text/javascript"></script>
<script src="js/mediaelementjs-skin/lib/mediaelementplayer.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = Carousel CarouFredSel =  -->
<!--  ==========  -->
<script src="js/carouFredSel-6.2.1/jquery.carouFredSel-6.2.1-packed.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = prettyPhoto lightbox =  -->
<!--  ==========  -->
<script src="js/prettyPhoto/js/jquery.prettyPhoto.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = custom styled select dropdown =  -->
<!--  ==========  -->
<script src="js/custom-select-menu.jquery.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = Flickr Feed =  -->
<!--  ==========  -->
<script src="js/jflickrfeed/jflickrfeed.min.js" type="text/javascript"></script>

<!--  ==========  -->
<!--  = Google Maps API =  -->
<!--  ==========  -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script src="js/gomap.min.js" type="text/javascript"></script>
<!--  ==========  -->

<!--  ==========  -->
<!--  = Custom JS =  -->
<!--  ==========  -->
<script src="js/custom.js" type="text/javascript" charset="utf-8"></script>

    </body>
</html>
