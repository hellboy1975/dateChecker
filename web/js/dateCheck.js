$('#form_timeFrom').datetimepicker();
$('#form_timeTo').datetimepicker({
    useCurrent: false //Important! See issue #1075
});

// sets the min & max values for the datetimepickers
$("#form_timeFrom").on("dp.change", function (e) {
    $('#form_timeTo').data("DateTimePicker").minDate(e.date);
});
$("#form_timeTo").on("dp.change", function (e) {
    $('#form_timeFrom').data("DateTimePicker").maxDate(e.date);
});