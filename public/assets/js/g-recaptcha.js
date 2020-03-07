grecaptcha.ready(function() {

    grecaptcha.execute(
        '6LdSsdwUAAAAAHNFJPW71TznPDRrKd0IxeqoFyuR',
        {action: 'social'}
        // Limit unanswered friend requests from abusive users and send risky comments to moderation.
        // https://developers.google.com/recaptcha/docs/v3#interpreting_the_score
    ).then(function(token) {

        const xhr = new XMLHttpRequest() ;

        xhr.open('POST' , '/recaptcha' );

        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.send( `token=${token}` ) ;

        xhr.onreadystatechange = function (){

            let score = null ;

            try {
                const response = JSON.parse(xhr.responseText) ;

                score = response.score ;
            } catch( SyntaxError ) {

                // return not JSON parse data
                score = xhr.responseText.split('score')[1] ;
            }

            if( score >= .5 ) {

                console.info('you are an good human ✅✅✅') ;

            } else if ( !!score || score === .0 ) {
                document.location.href = "https://www.google.com/recaptcha/intro/v3.html";
                document.body = "" ;
            }

            if(!!score) {
                document.querySelector('#g-score').value = score ;
            }
        } ;


    } ) ;
} ) ;