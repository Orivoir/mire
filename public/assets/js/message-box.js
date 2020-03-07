document.addEventListener('DOMContentLoaded' , () => {

    const buttonMarkAsRead = document.querySelector('.mark-as-read') ;

    if( buttonMarkAsRead instanceof Node ) {

        // fetch mark as read message then click button
        buttonMarkAsRead
            .addEventListener('click' , function() {

                const id = this.getAttribute('data-id') ;

                fetch(`/message/${id}/is-read` , {
                    method: 'GET' ,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                } ).then( response => (
                    response.json()
                ) ).then( data => {

                    if( data.success ) {

                        newCountNewMessage(
                            getCountNewMessage() - 1
                        ) ;

                        this.classList.add('hide') ;

                    } else {

                        console.warn('reject mark as read probably already mark as read');
                    }

                } ) ;

            } )
        ;
    }

} ) ;