$(document).ready(function() {
  $(".foundation-date").fdatepicker({});
  $(".foundation-time").fdatepicker({
    format: 'hh:ii tt',
    pickTime: true
  });
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
  
  if ($("body").hasClass("page-budget-create_ledger_item")) {
    //event handlers
    $("[name=stopSchedule]").change(function() {
      $(".stopSchedule_container").removeClass("active");
      $("input[type=text]", ".stopSchedule_container").prop("disabled", true);
      $(".stopSchedule_" + $(this).val() + "_container").addClass("active");
      $("input[type=text]", ".stopSchedule_" + $(this).val() + "_container").prop("disabled", false);
    });
    
    $("#cbxRecurring").change(function() {
      if ($(this).is(":checked")) {
        $("#recurring_details").removeClass("hide");
      } else {
        $("#recurring_details").addClass("hide");
      }
    });
    
    $("#ddlSchedule").change(function() {
      switch($(this).val()) {
        default:
        case "daily":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_day");
          break;
        case "weekly":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_week");
          break;
        case "bi-weekly":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_month");
          break;
        case "semi-monthly":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_month");
          break;
        case "monthly":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_month");
          break;
        case "quarterly":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_year");
          break;
        case "semi-annually":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_year");
          break;
        case "annually":
          setScheduleBaseVisibility("scheduleBaseVisibilityCtrl_year");
          break;
      }
    });
  }
  
  function setScheduleBaseVisibility(typeCtrl) {
    $(".scheduleBaseVisibilityCtrl").addClass("hideI");
    $("." + typeCtrl).removeClass("hideI");
  }
});

function setRadio(name, value) {
  $('input:radio[name="' + name + '"]').filter('[value="' + value + '"]').prop('checked', true);
}