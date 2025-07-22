$(document).ready(function(){

    // 
    // REGLEX
    //
    $("#btnupload").prop("disabled",true);

    // File upload via Ajax
    $("#fileform").on('submit', function(e){
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: 'inc/file.php',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(xhr){
                $('#loader').show();
            },
            error:function(){
                $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
            },
            success: function(resp){
                $('#divanalyse').attr( "style", "display: flex;" );
                $('#divexemple').hide();
                $('#jptext').html(resp);
            }
        }).done(function() {
            $('#loader').hide();
        });
    });
	
    // File type validation
    $("#fileinput").change(function(){
        var allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        var file = this.files[0];
        var fileType = file.type;
        if(!allowedTypes.includes(fileType)){
            alert('Merci de sélectionner un format de fichier approprié (PDF/DOCX).');
            $("#fileinput").val('');
            return false;
        }
        $("#btnupload").prop("disabled",false);
    });
});