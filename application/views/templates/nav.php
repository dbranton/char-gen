<!--<body>
	<a name="top"></a>
<header>
    <a href="../index.php">
        <h1 class="logo">D&D Next</h1>
    </a>
</header> -->

<div id="wrap" class="container">
  <div class="row">
    <div id="sidebarmenu" class="col-md-3">
        <aside class="affix">
            <ul id="sidebar" class="nav nav-pills nav-stacked">
                <!--<li class="nav-header">Character Info</li>-->
                <!--<li <?php //if ($this->uri->uri_string() == 'basics') echo 'class="active"'; ?>><?php echo anchor('basics', 'Basics'); ?></li>-->
                <li <?php if ($this->uri->uri_string() == 'races') echo 'class="active"'; ?>><?php echo anchor('races', 'Races'); ?></li>
                <li <?php if ($this->uri->uri_string() == 'classes') echo 'class="active"'; ?>><?php echo anchor('classes', 'Classes'); ?></li>
                <li <?php if ($this->uri->uri_string() == 'backgrounds') echo 'class="active"'; ?>><?php echo anchor('backgrounds', 'Backgrounds'); ?></li>
                <li <?php if ($this->uri->uri_string() == 'feats') echo 'class="active"'; ?>><?php echo anchor('feats', 'Feats'); ?></li>
                <li <?php if ($this->uri->uri_string() == 'equipment') echo 'class="active"'; ?>><?php echo anchor('equipment', 'Equipment'); ?></li>
                <li <?php if ($this->uri->uri_string() == 'spells') echo 'class="active"'; ?>><?php echo anchor('spells', 'Spells'); ?></li>
                <li <?php if ($this->uri->uri_string() == 'character_generator') echo 'class="active"'; ?>><?php echo anchor('character_generator', 'Character Generator'); ?></li>
            </ul> <!-- end sidebar -->
        </aside>
    </div>	<!-- ends the sidebarmenu div -->
    <div id="subwrap" class="col-md-9">
        <div class="page-header">
            <?php echo '<h2 class="text-capitalize">' . $title . '</h2>'; ?>
        </div>