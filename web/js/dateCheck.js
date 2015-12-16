$('#form_timeFrom').datetimepicker();
//2015-12-16T00:00:00Z
$('#form_timeTo').datetimepicker({
    useCurrent: false 
});

// sets the min & max values for the datetimepickers
$("#form_timeFrom").on("dp.change", function (e) {
    $('#form_timeTo').data("DateTimePicker").minDate(e.date);
});
$("#form_timeTo").on("dp.change", function (e) {
    $('#form_timeFrom').data("DateTimePicker").maxDate(e.date);
});