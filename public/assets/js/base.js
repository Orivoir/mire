/**
 * @classdesc open an moda for ask an **synchrone** confirm to user
 *
 * @constructor \
 *      object: \
 *          body: string , -- content text of modal default: *are you sure about this action ?*
 *          options: array , -- contains choice confirm **as** : `[ { text: string, className: string , onClick: () => void } ]`
 */
class ConfirmAction {

    static get idWrapConfirmAction() {

        return `wrap-confirm-action` ;
    }

    constructor( {
        options,
        body
    } ) {

        this.options = options ;
        this.body = body ;

        this.inject() ;
    }

    inject() {

        this.remove() ;

        const wrap = ConfirmAction.wrap ;
        wrap.innerHTML = this.innerHTML ;

        document.body.appendChild( wrap ) ;

        wrap.querySelectorAll('button').forEach( (button,key) => {

            const fxClick = this.options[ key ].onClick ;

            button.addEventListener('click' , fxClick instanceof Function ? ( event ) => {
                event.removeModal = this.remove ;

                fxClick.bind( event.target ) ;

                fxClick( event ) ;
            } : () => {
                console.warn("you have not define an callback for click on option : " , this.options[ key ].text );
            } ) ;

        } ) ;
    }

    get innerHTML() {

        return `
        <section>
            <header>
                <p>
                    ${this.body}
                </p>
            </header>

            <aside>

                <!-- options list -->
                <ul>
                    ${
                        this.options.map( (option,key) => {

                            const li = document.createElement('li') ;
                            li.setAttribute(`data-key` , key) ;

                            const button = document.createElement('button') ;

                            button.setAttribute( "data-button-confirm" , key ) ;

                            li.appendChild( button ) ;
                            button.className = option.className ;
                            button.textContent = option.text ;

                            return li.outerHTML ;
                        } )
                    }
                </ul>

            </aside>
        </section>
        ` ;
    }

    static get wrap() {

        const wrap = document.createElement('section') ;

        wrap.id = ConfirmAction.idWrapConfirmAction ;

        return wrap ;
    }

    get options() {
        return this._options ;
    }
    set options(options) {

        this._options = options instanceof Array ? options : null;

        this._options = this._options.filter( option => (
            typeof option === "object"
        ) ) ;

        if( !this._options.length )
            this._options = null;

        if( !this._options ) {

            throw RangeError('ConstructorError arg1 must be an options array as [ { text: string , className: string , onClick: () => void } , ... ]') ;
        }
    }

    get body() {
        return this._body;
    }
    set body(body) {
        this._body = typeof body === "string" ? body: "are you sure about this action ?" ;
    }

    remove() {

        const modal = document.querySelector( "#" + ConfirmAction.idWrapConfirmAction ) ;

        if( modal instanceof Node ) {

            modal.parentNode.removeChild( modal ) ;
        }

    }

} ;
// e.g use:
//
// new ConfirmAction( {
//     options: [ {
//         text: 'yes' ,
//         className: 'btn blue' ,
//         onClick: () => {
//             console.log("have click on yes");
//         }
//     } , {
//         text: 'no' ,
//         className: 'btn red' ,
//         onClick: () => {
//             console.log("have click on no");
//         }
//     } ]
// } ) ;

// client handler input/textarea
class HandlerInput {

    static callbackName( eventName ) {

        return `on${eventName.charAt(0).toUpperCase() + eventName.slice( 1 , )}` ;
    }

    static get eventsName() {

        return ['input','keydown','blur','focus'] ;
    }

    constructor( input , callbacks ) {

        this.input = input ;

        this.require = false ;

        this.listen() ;

        this.callbacks = callbacks ;
    }

    get callbacks() {
        return this._callbacks;
    }
    set callbacks(callbacks) {

        if( typeof callbacks !== "object" ) {
            throw new RangeError('ConstructorError arg2 must be an object of callbacks for input events') ;
        }

        // bind callbacks with input
        Object.keys(callbacks).forEach(  attr => {

            if( callbacks[attr] instanceof Function ) {

                callbacks[attr].bind( this.input ) ;
            } else {
                callbacks[attr] = false ;
            }
        } ) ;

        this._callbacks = callbacks ;
    }

    set require(require) {
        this._require = !!require ;
    }
    get require() {
        return this._require ;
    }

    get input() {
        return this._input;
    }
    set input(input) {

        this._input = input instanceof Node && /input|textarea/i.test(input.nodeName.toLocaleLowerCase()) ? input: null;

         if( !this._input ) {

            throw new RangeError('ConstructorError arg1 must be an input HTMLElement') ;
         }
    }

    listen() {

        this.input.addEventListener('keydown' , e => this.onKeydown( e ) ) ;

        this.input.addEventListener( 'input' , e => this.onInput( e ) ) ;

        this.input.addEventListener('blur' , () => this.onBlur() ) ;

        this.input.addEventListener('focus' , () => this.onFocus() ) ;
    }

    onFocus() {

        const fxOnEvent = this.callbacks.onEvent || ( () => {} ) ;
        const fxOnFocus = this.callbacks.onFocus || ( () => {} ) ;

        fxOnEvent( this.input ) ;
        fxOnFocus( this.input ) ;
    }

    onBlur() {

        const fxOnEvent = this.callbacks.onEvent || ( () => {} ) ;
        const fxOnBlur = this.callbacks.onBlur || ( () => {} ) ;
        const fxRequireError = this.callbacks.onRequireError || ( () => {} ) ;
        const fxRequireSuccess = this.callbacks.onRequireSuccess || ( () => {} ) ;

        if( this.require ) {

            if( !this.input.value.length ) {

                fxRequireError( this.input ) ;
            } else {

                fxRequireSuccess( this.input ) ;
            }
        }

        fxOnEvent( this.input ) ;
        fxOnBlur( this.input ) ;
    }

    onInput( e ) {

        const fxOnEvent = this.callbacks.onEvent || ( () => {} ) ;

        const fxOnChangeText = this.callbacks.onChangeText ;

        fxOnChangeText ? fxOnChangeText( this.input.value , e.data , this.input ) : null ;

        fxOnEvent() ;
    }

    onKeydown( e ) {

        const fxOnEvent = this.callbacks.onEvent || ( () => {} ) ;

        const key = e.key ;

        const val = this.input.value ;

        fxOnEvent() ;

        if( /^enter$/i.test(key) ) {

            const fxonEnter = this.callbacks.onEnter ;

            fxonEnter ? fxonEnter( val ) : null ;
        }

    }

    off( eventName ) {

        if( typeof eventName !== "string" ) return false ;

        eventName = eventName.trim().toLocaleLowerCase() ;

        if( !HandlerInput.eventsName.includes( eventName ) ) return false ;

        const callbackName = HandlerInput.callbackName( eventName ) ;

        this.input.removeEventListener( eventName , this[ callbackName ] ) ;

        return true ;
    }

    offAll() {

        HandlerInput.eventsName.forEach( eventName => {

            const callbackName = HandlerInput.callbackName( eventName ) ;

            this.input.removeEventListener( eventName , this[ callbackName ] ) ;

        } ) ;

        return this ;
    }

} ;

document.addEventListener('DOMContentLoaded' , () => {

    document.querySelectorAll('.hamburger-menu').forEach( menuEl => {

        menuEl.addEventListener('click' , function() {

            const status = !!this.classList.contains( 'toggle' ) ;

            this.classList[ status ? "remove": "add" ]( 'toggle' ) ;

            const targetSel = this.getAttribute( 'data-open' ) ;

            const target2open = document.querySelector( targetSel ) ;

            if( target2open instanceof Node ) {

                target2open.classList[ status ? "remove": "add" ]( 'open' ) ;
            }
        } ) ;

    } ) ;

    window.addEventListener('resize' , () => {

        if( window.matchMedia("(min-width: 1100px)").matches ) {

            document.querySelector('#header-menu-list').classList.remove('open') ;

            document.querySelector('button[data-open="#header-menu-list"]').classList.remove('toggle') ;
        }

    } ) ;

    // resolve propagation click while define data attributes
    ( () => {

        const clickWithPropagation = document.querySelectorAll('[data-propagation-click]') ;

        clickWithPropagation.forEach( current => {

            current.addEventListener('click' , function() {

                const targetSelector = this.getAttribute('data-propagation-click') ;

                const targetEl = document.querySelector( targetSelector ) ;

                if( targetEl instanceof Node ) {

                    targetEl.click() ;
                }

            } ) ;

        } ) ;

    } )() ;

    // resolve input inner form with ".form-container"
    ( () => {

        // get label from an input
        const getLabel = input => {

            const parent = input.parentNode ;

            return parent.querySelector('label') ;
        }

        // resolve real wrap field from an input
        const getWrap = input => {

            let current = null ;
            let work = input ;

            do {

                work = work.parentNode ;
                current = work ;

            } while( !current.classList.contains('form-group') )

            return current ;

        } ;

        const inputFormGroup = [...document.querySelectorAll('form div.form-group input'),...document.querySelectorAll('form div.form-group textarea')] ;


        // resolve focus on input inner form with ".form-container"
        ( () => {

            inputFormGroup.forEach( inputEl => {

                const toggleCls = ( input, type ) => {

                    [ getLabel( input ) , getWrap( input ) ]
                    .forEach( current => {

                        if( current instanceof Node ) {

                            current.classList[type]('focus') ;
                        }

                    } ) ;

                } ;

                inputEl.addEventListener( 'focus' , function() {

                    toggleCls( this , "add" ) ;

                    const wrap = getWrap( this ) ;

                    wrap.classList.remove('contains') ;


                } ) ;

                inputEl.addEventListener( 'blur' , function() {

                    toggleCls( this , "remove" ) ;

                    const wrap = getWrap( this ) ;

                    if( !!this.value.length ) {

                        wrap.classList.add('contains') ;
                    } else {
                        wrap.classList.remove('contains') ;
                    }

                } ) ;

            } ) ;

        } )() ;

        // resolve error input inner form with ".form-container"
        ( () => {

            inputFormGroup.forEach( input => {

                input.addEventListener('blur' , () => {

                    const wrap = getWrap( input ) ;
                    const method = input.classList.contains('error') ? "add": "remove" ;

                    wrap.classList[ method ]( 'error' ) ;

                } ) ;

            } ) ;

        } )() ;

    } )() ;

    // resolve send mail activate buttons with ".mail-activate"

} ) ;

