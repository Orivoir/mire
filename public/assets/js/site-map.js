document.addEventListener( 'DOMContentLoaded' , () => {

    async function getRoutes() {

        const response = await fetch(`/api/site-map` , {
            method: 'GET' ,
            headers: {
                'Accepts': 'application/json'
            }
        } ) ;

        return await response.json() ;
    }

    class RouteRender {

        static get container() {

            return document.querySelector('.routes-wrap') ;
        }

        get item() {

            const li = document.createElement('li') ;

            li.classList.add('routes-item') ;

            li.innerHTML = `
                <section>
                    <p>
                        <a href="${this.pathRender}">
                            ${this.nameRender}
                        </a>
                    </p>

                    <p>${this.describe}</p>
                </section>
            ` ;

            return li ;
        }

        constructor( route ) {

            this.route = route ;

            const item = this.item ;

            // console.log( item );

            RouteRender
                .container
                .querySelector('ol')
                .appendChild( item )
            ;
        }

        get describe() {

            return ({
                "home": "home page mire" ,
                "sign in": "login page for access member zone of Mire with your identifiant ( username, password )" ,
                "sign up": "register your new account for access member zone of Mire" ,
                "articles list": "list last article of chess game" ,
                "your profil": "page another user see of you" ,
                "your settings profil": "craft your account here" ,
                "your articles": "handler of your articles" ,
                "your follow subjects": "handler of your follow articles",
                "about mire": "more information about Mire" ,
                "contact mire": "give an feedback to Mire",
                "new article": "write an new article on chess game" ,
                "your messages box": "handler of your messages with another users of Mire"
            })[ this.nameRender ] ;
        }

        get nameRender() {

            return ({
                "/": "home" ,
                "/login": "sign in",
                "/register": "sign up" ,
                "/a/{page}": "articles list" ,
                "/contact": "contact mire" ,
                "/about": "about mire",
                "/u/{username}": "your profil" ,
                "/u/my/settings": "your settings profil",
                "/u/my/articles": "your articles",
                "/u/my/subjects": "your follow subjects" ,
                "/article/new": "new article" ,
                "/u/box/messages": "your messages box"
            })[ this.route.path ] ;
        }

        get pathRender() {

            if( this.route.path.indexOf('{') === -1 ) {

                return this.route.path ;
            } else {

                // path contains var.s

                if( /article_index/i.test( this.route.name ) ) {

                    return this.route.path.replace('{page}' , '1') ;

                } else if( IS_LOGGED && /user_profil/i.test( this.route.name ) ) {

                    return this.route.path.replace( '{username}' , USERNAME ) ;
                }
            }
        }

        get route() {
            return this._route ;
        }
        set route( route ) {

            this._route = ( typeof route === "object" && typeof route.name === "string" && typeof route.path === "string" ) ? route: null ;

            if( !this._route ) {

                throw "RouteRender have receveid invalid data from constructor cant build render route" ;
            }
        }

    } ;

    getRoutes().then( data => {

        RouteRender.container.classList.remove('container-load') ;

        if( data.success ) {

            data.routes.forEach( route => {

                new RouteRender( route ) ;

            } );

        } else {

            console.warn('api site success false with:' , data );
        }

    } ).catch( err => {

        console.error( err );

        throw "fail fetch get routes site map" ;

    } ) ;

} ) ;