jQuery(document).ready(function(){
    jQuery( "#accordion-wp" ).accordion();
    jQuery( "#custom_date1" ).datepicker({beforeShowDay: function(date) {
        return [date.getDate() == 1, ''];
    }});

    jQuery( "#custom_date2" ).datepicker({beforeShowDay: function(date) {
        return [date.getDay() == 3, ''];
    }});
});


