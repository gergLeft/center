<?php
  $page_title = 'Dashboard';
  $page_tag = 'dashboard';
  include 'functions.php';

  //page postback actions
?>
  <?php //TODO: vet styles, stylesheets & JS files; integrate whats needed, drop whats not ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <style>
  .column {
    width: 170px;
    float: left;
    padding-bottom: 75px;
  }
  .portlet {
    margin: 0 1em 1em 0;
    padding: 0.3em;
  }
  .portlet-header {
    padding: 0.2em 0.3em;
    margin-bottom: 0.5em;
    position: relative;
  }
  .portlet-toggle {
    position: absolute;
    top: 50%;
    right: 0;
    margin-top: -8px;
  }
  .portlet-content {
    padding: 0.4em;
  }
  .portlet-placeholder {
    border: 1px dotted black;
    margin: 0 1em 1em 0;
    height: 50px;
  }
  </style>
  <script>
  $(function() {
    $( ".column" ).sortable({
      connectWith: ".column",
      handle: ".portlet-header",
      cancel: ".portlet-toggle",
      placeholder: "portlet-placeholder ui-corner-all",
      update: function(event, ui) {
        //TODO: write the logic to update widget location
      }
    });
 
    $( ".portlet" )
      .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
      .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");
 
    $( ".portlet-toggle" ).click(function() {
      var icon = $( this );
      icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
      icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
      //TODO: write the logic to update widget state
    });
  });
  </script>
  
<?php include 'components/global/header.php'; ?>
  
  <?php /* Dashboard Widgets */?>
  <div class="row">
  <?php
    $columns = array();
    $homepageModules = (0 === sizeof($currentUserInfo->active_homepage_modules)) ? $defaultHomepageModules : $currentUserInfo->active_homepage_modules;
    define('COLUMNS', 3);
    //TODO: This currently sorts everything into 3 columns in order...
    //      need to add feature to allow disproportionate counts between columns (
    //      4-2-1 or 2-5-3, etc)
    for($b = 0; $b < sizeof($homepageModules); $b++) : 
      $columns[floor($b/COLUMNS)][] = $homepageModules[$b];
    endfor;
  ?>
  <?php foreach ($columns as $column) : ?>
  <div class="column small-12 large-4">
  <?php foreach ($column as $block) : ?>
    <div class="portlet">
      <div class="portlet-header"><?= $block["title"] ?></div>
      <div class="portlet-content"><?php include("components/widgets/" . $block['slug'] . ".php"); ?></div>
    </div>
  <?php endforeach;?>
    </div>
  <?php endforeach; ?>
  </div>
  <?php /* End Dashboard Widgets */?>
  
  <?php /* Dashboard Configuration Controls */ ?>
  <div id="dashboard_configuration_switch_container" class="row">
    <div class="column medium-1"><a id="dashboard_configuration_switch" href="#" class="button tiny">Open Toolbox</a></div>
  </div>
  <div id="dashboard_configuration" class="row hide">
    <div class="column small-12">
      <div id="dashboard_configuration_close" class="right ui-icon ui-icon-closethick"></div>
      <div class="controlboard-title"><h5>Available Modules</h5></controlboard-title>
      <div class="row">
        <?php
          $columns = array();
          $inctiveModules = (0 === sizeof($currentUserInfo->inactive_homepage_modules)) ? $defaultInactiveModules : $currentUserInfo->inactive_homepage_modules;
          define('INACTIVE_MOD_COLUMNS', 3);
          //TODO: This currently sorts everything into 3 columns in order...
          //      need to add feature to allow disproportionate counts between columns (
          //      4-2-1 or 2-5-3, etc)
          for($b = 0; $b < sizeof($inctiveModules); $b++) : 
            $columns[floor($b/INACTIVE_MOD_COLUMNS)][] = $inctiveModules[$b];
          endfor;
        ?>
        <?php foreach ($columns as $column) : ?>
        <div class="column small-12 large-4">
        <?php foreach ($column as $block) : ?>
          <div class="portlet">
            <div class="portlet-header"><?= $block["title"] ?></div>
            <div class="portlet-content"><?php include("components/widgets/" . $block['slug'] . ".php"); ?></div>
          </div>
        <?php endforeach;?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php /* End Dashboard Configuration Controls */ ?>
  
<?php include "components/global/footer.php"; ?>
  