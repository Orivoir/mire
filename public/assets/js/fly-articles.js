/**
 * @author Samuel Gaborieau
 *
 * fly-articles client app
 * slider last articles while user not logged
 */
( () => {

    let sliderID = null ;
    let index = 0 ;

    const CSS_ANIMATION_SLIDER_TIMEOUT = 4200 ;

    const onSlide = () => {

        // for force re start css animation slider
        FlyArticle.containerHTML.classList.remove('start') ;

        // switch item

        const current = FlyArticle.current ;

        current.classList.remove( 'current' ) ;

        const items = FlyArticle.items ;

        index++ ;

        if( index >= MAX_ARTICLES ) {

            index = 0;
        }

        items[ index ].classList.add('current') ;


        FlyArticle.containerHTML.classList.add('start') ;

    } ;

    class FlyArticle {

        /**
         * @warn never wait dom load \
         * but AJAX request before select DOM
         */
        static get containerHTML() {

            const container = document.querySelector('#fly-articles') ;

            if( container instanceof Node ) {

                return container ;
            } else {

                throw "document do not contains container of FlyArticle '#fly-articles' not exists/found" ;
            }
        }

        static currentCls( cls ) {

            const items = FlyArticle.items ;

            items.forEach( item => (
                item.classList.remove( cls )
            ) ) ;

            items[0].classList.add( cls ) ;

        }

        get time() {

            const date = new Date(this.article.createAt) ;

            return `${date.getHours()}:${date.getMinutes()}` ;
        }

        get item() {

            const li = document.createElement('li') ;

            li.innerHTML = `
                <section>
                    <a href="${this.URL}">
                        <p class="content-fly-article">
                            ${this.article.title.slice( 0 , 15 )}...
                        </p>

                        <p class="time-fly-article">
                            ${this.time}
                        </p>

                        <p class="author-fly hide">
                            ${this.article.user.username}
                        </p>
                    </a>
                </section>
            ` ;

            li.classList.add('fly-articles-item') ;

            return li ;
        }

        static get items() {

            return FlyArticle.containerHTML.querySelectorAll('ul li') ;
        }

        static get current() {

            return [...FlyArticle.items].find( item => (
                item.classList.contains( 'current' )
            )  ) || null ;
        }

        constructor( article ) {

            this.article = article ;

            const item = this.item ;

            FlyArticle
                .containerHTML
                .querySelector('ul')
                .appendChild( item )
            ;

            FlyArticle.currentCls( 'current' ) ;

            const author = item.querySelector('.author-fly') ;


            item.addEventListener('mouseenter' , () => {

                clearInterval( sliderID ) ;

                FlyArticle.containerHTML.classList.remove('start') ;

                author.classList.remove('hide') ;

            } ) ;

            item.addEventListener('mouseleave' , () => {

                sliderID = setInterval(
                    onSlide ,
                    CSS_ANIMATION_SLIDER_TIMEOUT
                );

                FlyArticle.containerHTML.classList.add('start') ;

                author.classList.add('hide') ;

            } ) ;

        }

        get URL() {

            return `/a/${this.article.slug}/${this.article.id}` ;
        }

        get article() {
            return this._article;
        }
        set article(article) {

            this._article = ( typeof article === "object" && typeof article.slug === "string" && typeof article.title === "string" && typeof article.id === "number" ) ? article: null;

            if( !this._article ) {

                throw "invalid data constructor for FlyArticle cant build slider" ;
            }
        }

    } ;

    if( !window.fetch ) {

        throw "your browser do not support Fetch API" ;
    }

    const MAX_ARTICLES = 3 ;

    async function getLastArticles( many ) {

        const response = await fetch(`/api/last-articles/${many}` , {
            method: 'GET' ,
            headers: {
                'Accepts': 'application/json'
            }
        } ) ;

        const data = await response.json() ;

        if( data.success ) {

            const articles = [] ;

            data.articles.forEach( article => {
                articles.push( JSON.parse( article ) )
            } ) ;

            articles.forEach( article => {

                new FlyArticle( article ) ;

            } ) ;

            FlyArticle.containerHTML.classList.add( 'start' ) ;
            FlyArticle.containerHTML.classList.remove('container-load') ;

            sliderID = setInterval( onSlide , CSS_ANIMATION_SLIDER_TIMEOUT );

        } else {

            console.warn( "success false fly articles" );
            console.log( data );
        }
    }

    getLastArticles( MAX_ARTICLES ) ;


} )() ;
