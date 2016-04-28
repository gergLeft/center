$(document).ready(function() {
  if ($("body").hasClass("page-dashboard")) {
    $("#dashboard_configuration_switch").click(function() {
      $("#dashboard_configuration").removeClass("hide");
      $("#dashboard_configuration_switch_container").addClass("hide");
    });
    $("#dashboard_configuration_close").click(function() {
      $("#dashboard_configuration").addClass("hide");
      $("#dashboard_configuration_switch_container").removeClass("hide");
    });
  }
});