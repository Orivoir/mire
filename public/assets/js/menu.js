( () => {

    document.addEventListener('DOMContentLoaded' , () => {

        const iconSearch = document.querySelector('.search-icon i') ;
        const contentSearch = document.querySelector('#content-search') ;
        const formSearch = document.querySelector('#content-search form') ;
        const inputSearch = document.querySelector('#content-search form input#search') ;
        const buttonCloseSearch = document.querySelector('#content-search form button.red') ;
        const resultArticle = document.querySelector('#result-article') ;
        const resultUser = document.querySelector('#result-user') ;
        const contentResult = document.querySelector('#content-result') ;
        const iconSubmit = document.querySelector('#content-search form button i.search-icon') ;
        const iconLoad = document.querySelector('#content-search form button i.load-icon') ;
        const buttonSubmit = document.querySelector('#content-search form button[type="submit"]') ;

        const TIMEOUT_ANIMATION_ERROR_CSS = 330 ;

        let isLoadSearch = false ;

        buttonCloseSearch.addEventListener('click' , () => {
            window.history.pushState( {} ,  'close-search' , '/') ;
        } ) ;

        if( iconSearch instanceof Node && contentSearch instanceof Node ) {

            iconSearch.addEventListener('click' , () => {

                const state = !!contentSearch.classList.contains('open') ;

                contentSearch.classList[ !state ? 'add':'remove' ]('open') ;

                if( !state ) {

                    contentSearch.classList.remove('hide') ;
                    document.querySelector('input#search').focus() ;
                    window.history.pushState( {} ,  'search' , '/search') ;
                    inputSearch.value = "" ;
                    contentSearch.classList.remove('result') ;
                    resultArticle.innerHTML = "" ;
                    resultUser.innerHTML = "" ;

                    console.log( formSearch.offsetHeight );

                    contentResult.style.marginTop =  ( formSearch.offsetHeight ) + "px" ;
                    inputSearch.style.height = ( formSearch.offsetHeight ) + "px" ;
                }

            } ) ;
        }

        formSearch.addEventListener('submit' , function(e) {

            e.preventDefault() ;

            const searchstring = inputSearch.value ;

            if( isLoadSearch || !searchstring.length ) {

                buttonSubmit.classList.add('error') ;

                setTimeout(() => {
                    // remove class for can repeat animation
                    buttonSubmit.classList.remove('error') ;
                }, TIMEOUT_ANIMATION_ERROR_CSS );
                return ;
            }

            isLoadSearch = true ;

            iconSubmit.classList.add('hide') ;
            iconLoad.classList.add('load-icon-open') ;

            resultArticle.innerHTML = "" ;
            resultUser.innerHTML = "" ;

            window.history.pushState( {} ,  'search' , '/search?q='+searchstring) ;

            fetch(`/search/${searchstring}`)
            .then( res => (
                res.json()
            ) )
            .then( data => {

                const requestsArticle = [] ;

                data['articles-id'].forEach( id => {

                    requestsArticle.push( (
                        fetch(`/api/article/${id}`)
                    ) ) ;

                } ) ;

                Promise.all( requestsArticle )
                .then( ( responses ) => {

                    return responses.map( async(response) => {
                        return await response.json() ;
                    } ) ;

                } )
                .then( datas => {

                    Promise.all( datas )
                    .then( jsons => {

                        iconSubmit.classList.remove('hide') ;
                        iconLoad.classList.remove('load-icon-open') ;
                        contentSearch.classList.add('result') ;
                        isLoadSearch = false ;

                        jsons.forEach( json => {

                            if( json.success ) {

                                const article = JSON.parse( json.article ) ;

                                const li = document.createElement('li') ;

                                li.className = "article-item result-item" ;

                                li.innerHTML = `
                                    <section>
                                        <header>
                                            <h1>
                                                <a href="/a/${article.slug}/${article.id}">
                                                    ${article.title}
                                                </a>
                                            </h1>

                                            <h2>
                                                author
                                                <a href="/u/${article.user.username}">
                                                    ${article.user.username}
                                                </a>
                                            </h2>
                                        </header>
                                    </section>
                                ` ;

                                resultArticle.appendChild( li ) ;
                            }

                        } ) ;
                    } )

                } )
                .catch( err => {

                    console.log( "fail with get from api : " , err );

                } ) ;
            } )
            .catch( err => {

                console.error('fetch search fail with: ' , err );
                throw "fail fail" ;
            } ) ;

        } ) ;

    } ) ;

} )() ;
