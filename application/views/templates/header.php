<?php
    $base_url = $this->config->item('base_url');
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<title>Character Generator - ' . $title . '</title>'; ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/normalize.css" />
    <!--<link rel="stylesheet" href="<?php //echo $base_url; ?>/assets/css/jquery-ui.css" />-->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css" />
    <!--<link rel="stylesheet" href="<?php //echo $base_url; ?>/assets/css/bootstrap-modal-bs3patch.css" />-->
    <!--<link rel="stylesheet" href="<?php //echo $base_url; ?>/assets/css/bootstrap-modal.css" />-->
    <!--<link rel="stylesheet" href="<?php //echo $base_url; ?>/assets/css/bootstrap-responsive.css" />-->
    <!--<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/font-awesome-4.0.3/css/font-awesome.min.css">-->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css" />
    <?php
        if($title == "Level Up" || $title == "Character Generator") {
            echo '<link rel="stylesheet" href="' . $base_url . '/assets/js/vendor/select2-3.4.3/select2.css" />';
        }
    ?>
    <script>
        /*less = {
            env: "development", // or "production"
            async: false,       // load imports async
            fileAsync: false,   // load imports async when in a page under
            // a file protocol
            poll: 1000,         // when in watch mode, time in ms between polls
            functions: {},      // user functions, keyed by name
            dumpLineNumbers: "comments", // or "mediaQuery" or "all"
            relativeUrls: false,// whether to adjust url's to be relative
            // if false, url's are already relative to the
            // entry less file
            rootpath: ":/a.com/"// a path to add on to the start of every url
            //resource
        };*/
    </script>
    <?php //echo '<script src="' . $base_url . '/assets/js/vendor/less-1.3.3.min.js"></script>'; ?>
</head>

<body>
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Be sure to leave the brand out there if you want it shown -->
            <div class="navbar-header">
                <?php echo anchor('home', 'D&D Character Generator', 'class="navbar-brand"'); ?>
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Everything you want hidden at 940px or less, place within here -->
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">
                    <li><?php echo anchor('home', 'Home'); ?></li>
                    <li><a href="../../../../about.php" class="topnav">About</a></li>
                    <li><a href="../../../../contact.php" class="topnav">Contact</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    if(isset($this->session->userdata['logged_in']['username'])) {
                        echo '<li>' . anchor('user/yourCharacters', 'Character(s)') . '</li>';
                        echo '<li>' . anchor('user/logout', 'Logout') . '</li>';
                    }
                    else {
                        echo '<li>' . anchor('user/login', 'Login') . '</li>';
                        //echo '<li><a href="" data-target="#loginModal" data-toggle="modal">Login</a></li>';
                        echo '<li>' . anchor('user/register', 'Create Account') . '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>