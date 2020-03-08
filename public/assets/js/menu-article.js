document.addEventListener('DOMContentLoaded' , () => {

    async function actionRemove( id ) {

        const response = await fetch(`/article/${TOKEN_USER}/${id}` , {
            method: 'DELETE'
        } ) ;

        return await response.json() ;
    }

    document.querySelectorAll('[data-remove]').forEach( btnRemove => {

        btnRemove.addEventListener('click' , function(e) {

            e.preventDefault() ;

            const id = this.getAttribute( 'data-remove' ) ;

            actionRemove( id )
            .then( data => {

                if( data.success ) {

                    const wrapArticle = document.querySelector(`#wrap-article-${id}`) ;

                    wrapArticle.parentNode.removeChild( wrapArticle ) ;
                }

            } ) ;

        } ) ;

    }  ) ;

} ) ;