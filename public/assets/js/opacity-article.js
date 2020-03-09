const callbacksonMousemove = [] ;
const callbackonPointerUp = [] ;

let articlesOpacity = localStorage.getItem('articles-opacity') ;

class OpacityArticle {

    static getInnerBar( articleID ) {

        return document
            .querySelector(`#wrap-article-${articleID}`)
            .querySelector(`.opacity-bar-outer .opacity-bar-inner`)
        ;
    }

    static getOuterBar( articleID ) {

        return document
            .querySelector(`#wrap-article-${articleID}`)
            .querySelector(`.opacity-bar-outer`)
        ;
    }

    constructor( article ) {

        this.article = article ;

        this.isDrag = false ;

        this.outerBar.addEventListener('pointerdown' , e => {

            const posY = e.offsetY ;

            this.innerBar.style.top = (posY ) + "px" ;

            this.isDrag = true ;

            this.article.classList.add('drag') ;

            this.opacity = this.percent / 100 ;

            this.sectionBackgroundColor.style.backgroundColor = `rgba(42,42,42,${this.percent/100} )` ;

        } ) ;

        callbackonPointerUp.push( () => {

            this.isDrag = false ;

            this.article.classList.remove('drag') ;

        } ) ;

        const MARGIN_FRAME = 10 ;

        callbacksonMousemove.push( e => {

            if( this.isDrag ) {

                if(
                    ( this.innerBar.offsetTop + this.innerBar.offsetHeight + MARGIN_FRAME ) < this.outerBar.offsetHeight &&
                    this.innerBar.offsetTop > 0
                ) {

                    if( e.offsetY <= MARGIN_FRAME ) return ;

                    this.innerBar.style.top = ( e.offsetY ) + "px" ;

                    // persist base 1 for style opacity
                    this.opacity = this.percent / 100 ;

                    this.sectionBackgroundColor.style.backgroundColor = `rgba(42,42,42,${this.percent/100} )` ;
                }
            }

        } ) ;
    }

    get sectionBackgroundColor() {

        return this.article.querySelector(`section#article-${this.articleID}`) ;
    }

    get percent() {

        const totalHeight = ( this.outerBar.offsetHeight - this.innerBar.offsetHeight ) ;

        const posY = this.innerBar.offsetTop ;

        return ( ( posY / totalHeight ) * 100).toPrecision( 2 ) ;
    }

    set opacity( opacity ) {

        if( !!this.isExists ) {

            articlesOpacity = articlesOpacity.map( item => {

                if( item['article-id'] == this.articleID ) {

                    item['opacity'] = opacity ;

                }

                return item ;

            } ) ;

        } else {

            articlesOpacity.push( {
                "article-id": this.articleID ,
                "opacity": opacity
            } ) ;
        }

        localStorage.setItem('articles-opacity' , JSON.stringify(
            articlesOpacity
        ) ) ;
    }

    get currentOpacity() {

        return articlesOpacity.find( item => {

            return item['article-id'] == this.articleID ;

        } ) || .5 ;
    }

    get isExists() {

        if( !articlesOpacity.length ) return false ;

        return !!articlesOpacity.find( item => {

            return item['article-id'] == this.articleID ;

        } ) ;
    }

    get articleID() {

        const id = this.article.id ;

        return id.split('-').pop() ;
    }

    get outerBar() {

        return this.article.querySelector(".opacity-bar-outer") ;
    }

    get innerBar() {

        return this.outerBar.querySelector('.opacity-bar-inner') ;
    }

    get article() {

        return this._article ;
    }
    set article(article) {

        this._article = ( article instanceof Node ) ? article: null ;
    }

} ;

document.addEventListener('DOMContentLoaded' , () => {

    if( !!articlesOpacity ) {

        articlesOpacity = JSON.parse( articlesOpacity ) ;

        articlesOpacity.forEach( item => {

            const article = document.querySelector(`section#article-${item["article-id"]}`) ;

            article
                .style
                .backgroundColor = `rgba(42,42,42,${item.opacity})`
            ;

            OpacityArticle
                .getInnerBar( item['article-id'] )
                .style
                .top = ( item.opacity * 100 ) + `%`
            ;

        } ) ;
    } else {

        articlesOpacity = [] ;
    }

    document.addEventListener('mousemove' , e => {

        callbacksonMousemove.forEach( callback => (
            callback instanceof Function ? callback( e ) : null
        ) )

    } ) ;

    document.addEventListener('pointerup' , e => {

        callbackonPointerUp.forEach( callback => (
            callback instanceof Function ? callback( e ) : null
        ) ) ;

    } ) ;

    const articlesList = document.querySelectorAll('.article-item.background-custom') ;

    articlesList.forEach( article => (

        new OpacityArticle( article )

    ) ) ;

} ) ;
