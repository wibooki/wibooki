$(function(){
    $("#profile-upload-link").on('click', function(e){
        e.preventDefault();
        $("#profile-upload:hidden").trigger('click');
    });
});