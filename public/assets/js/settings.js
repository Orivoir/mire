document.addEventListener('DOMContentLoaded' , () => {

    const avatarHandler = new HandlerFile({

        input: document.querySelector('#settings_form_avatar') ,

        onValidChange: function(filename , file) {

            avatarHandler.read() ;
        } ,

        onRead: function( blob ) {

            document.querySelector('.avatar-change').src = blob ;
        }
    }) ;

    avatarHandler.addTypes(
        "image/png" ,
        "image/svg+xml" ,
        "image/jpeg" ,
        "image/gif"
    ) ;

    avatarHandler.maxSize = 12.5;//MO

    if( ACCOUNT_IS_VALID ) {

        const inputEmail = document.querySelector('#settings_form_email') ;
        inputEmail.parentNode.parentNode.parentNode.removeChild( inputEmail.parentNode.parentNode ) ;
    }

} ) ;
