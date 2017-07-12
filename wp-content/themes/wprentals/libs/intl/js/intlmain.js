jQuery(document).ready(function($){
    // get the country data from the plugin
    var countryData = $(this).intlTelInput.getCountryData(),
           telInput = $(".intl-phone");
    
    // init plugin
    telInput.intlTelInput({initialCountry:'th'});
    telInput.val("+66");
    telInput.on("countrychange", function(e, countryData) {
      telInput.val("+" + countryData.dialCode);
    });
});