class HandlerFile {

    constructor( {
        input ,
        onValidChange ,
        onRead
    } ) {

        this.input = input ;

        this.onValidChange = onValidChange ;
        this.onRead = onRead ;

        this.types = [] ;

        this.file = null ;
        this.maxSize = 3 ; // MegaOctects

        this.listenAdd() ;
    }

    addTypes() {

        [...arguments].forEach( type => (
            this.types.push( type.trim() )
        ) ) ;

        return this ;
    }

    read() {

        if( !this.file ) {

            return false ;
        }

        const reader = new FileReader() ;

        reader.addEventListener('load' , e => {

            this.onRead( e.target.result ) ;

        } ) ;

        reader.readAsDataURL( this.file ) ;
    }

    listenAdd() {

        this.input.addEventListener('change' , e => {

            const file = this.input.files[0] ;

            if( file ) {
                const type = file.type ;

                if( this.types.includes( type ) ) {

                    // convert octects in megaoctects
                    const size = parseFloat( ( file.size / 1e6 ).toPrecision(4) ) ;

                    if( size <= this.maxSize ) {

                        this.filename = file.name ;
                        this.file = file ;

                        this.onValidChange( this.filename , file ) ;

                    } else {

                        new ConfirmAction( {
                            body: `sorry but '${file.name}'  exced max size with ${size}mo max is ${this.maxSize}mo ` ,
                            options: [
                                {
                                    text: "ok" ,
                                    className: "btn green" ,
                                    onClick: e => {

                                        e.removeModal() ;
                                    }
                                }
                            ]
                        }) ;
                    }

                } else {
                    // mime type invalid

                    new ConfirmAction({
                        body: `sorry but '${file.name}'  is not an invalid image` ,
                        options: [
                            {
                                text: "ok" ,
                                className: "btn green" ,
                                onClick: e => {

                                    e.removeModal() ;
                                }
                            }
                        ]
                    }) ;
                }
            }

        } ) ;
    }

    get input() {
        return this._input ;
    }
    set input(input) {
        this._input = input instanceof Node && input.nodeName.toLocaleLowerCase() === "input" ? input: null ;

        if( !this._input ) {

            throw new RangeError('ConstructorError arg1 must be an input HTMLElement') ;
        }

    }

} ;
