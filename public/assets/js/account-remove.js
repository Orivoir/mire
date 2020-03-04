( () => {

    const buttonRemove = document.querySelector('button#delete-user') ;

    async function onConfirmRemove() {

        const response = await fetch( buttonRemove.getAttribute('data-target').trim()  , {
            method: buttonRemove.getAttribute('data-method') ,
            headers: {
                'Accepts': 'application/json'
            }
        } ) ;

        return await response.json() ;
    }

    if( buttonRemove instanceof Node ) {

        buttonRemove.addEventListener('click' , () => {

            new ConfirmAction( {
                body: "are you sure want remove your account ? this action is not reversible." ,
                options: [
                    {
                        text: 'no' ,
                        className: 'btn blue' ,
                        onClick: e => {

                            e.removeModal() ;
                        }
                    } , {
                        text: 'yes' ,
                        className: "btn red" ,
                        onClick: e => {

                            console.log('@TODO: implements an start load function');

                            onConfirmRemove().then( data => {

                                e.removeModal() ;

                                console.log('TODO: implements an stop load function');

                                if( data.success ) {
                                    // disconnect after validate remove
                                    document.location.href = "/logout" ;
                                }

                            } ).catch( err => {
                                e.removeModal() ;

                                console.error("fetch remove user fail");

                                throw "fetch fail" ;

                            } ) ;

                        }
                    } ,
                ]
            } ) ;

        } ) ;

    }

} )() ;