/**
 * @author Samuel Gaborieau
 *
 * "/register" - Form Client App
 */
document.addEventListener('DOMContentLoaded' , () => {

    // handler input register form
    const usernameInput = document.querySelector('input#registration_form_username') ;
    const passwordInput = document.querySelector('input#registration_form_plainPassword') ;
    const passwordConfirmInput = document.querySelector('input#registration_form_password') ;

    // callbacks event inputs
    const onFieldRequireError = function( input ) {

        input.classList.add('error') ;
    } ;

    const onFieldRequireSuccess = function( input ) {

        input.classList.remove('error') ;
    } ;

    const onChangeText = function(text, lastCharEnter , input ) {
        // if not change text with STEPS_CHECK_USERNAME timeout
        // check currently username

        iconLoaderUsername.classList.remove('hide') ;
        iconErrorUsername.classList.add('hide') ;
        iconSuccessUsername.classList.add('hide') ;

        if( checkIsEngaged ) {

            clearTimeout( checkUsernameID ) ;

            checkIsEngaged = false ;

        } else {

            checkUsernameID = setTimeout(() => {

                if( usernameInput.value ) {

                    checkUsername( text ) ;
                }

            }, STEPS_CHECK_USERNAME) ;

            checkIsEngaged = true ;
        }


    } ;

    // icon check if username is valid or already use
    const iconLoaderUsername = document.querySelector('.load-username-icon') ;
    const iconSuccessUsername = document.querySelector('.success-icon') ;
    const iconErrorUsername = document.querySelector('.error-icon') ;

    // callbacks groups for handlers inputs
    const callbacks = {

        onRequireError: onFieldRequireError ,
        onRequireSuccess: onFieldRequireSuccess ,
        onFocus: function( input ) {

            if( /username/i.test(input.id) ) {

                iconLoaderUsername.classList.remove('hide') ;
                iconErrorUsername.classList.add('hide') ;
                iconSuccessUsername.classList.add('hide') ;
            }
        } ,
        onBlur: function( input ) {

            if( /username/i.test(input.id) ) {

                iconLoaderUsername.classList.add('hide') ;
            }
        } ,
    } ;

    // fx GET fetch for check if username is valid or already use
    function checkUsername( username )  {

        fetch( `/is-valid/${username}` , {
            method: 'GET' ,
            headers: {
                'Accepts': 'application/json'
            }
        } ).then( response => response.json() )
        .then( data => {

            iconLoaderUsername.classList.add('hide') ;
            iconErrorUsername.classList.add('hide') ;
            iconSuccessUsername.classList.add('hide') ;

            if( data.success ) {

                iconSuccessUsername.classList.remove('hide') ;

            } else {
                iconErrorUsername.classList.remove('hide') ;
            }

        } ) ;
    }

    let checkIsEngaged = false ;
    let checkUsernameID = null ;
    const STEPS_CHECK_USERNAME = 275 ; // ms

    // create handlers inputs

    const handlerUsernameInput = new HandlerInput( usernameInput , {...callbacks ,
        onChangeText: onChangeText
    } ) ;
    const handlerPasswordInput = new HandlerInput( passwordInput , callbacks ) ;
    const handlerPasswordConfirmInput = new HandlerInput( passwordConfirmInput , callbacks ) ;

    // add require client for handlers inputs ( fire on blur )
    handlerUsernameInput.require = true ;
    handlerPasswordInput.require = true ;
    handlerPasswordConfirmInput.require = true ;

} ) ;
