// get count new messages
function CountMessageBox( onResponse ) {

    fetch("/u/count/new-messages" , {
        headers: {
            "Content-Type": "application/json"
        }
    } )
    .then( res => res.json() )
    .then( data => {

        onResponse instanceof Function ? onResponse( data ) : ( () => {
            console.warn("count message box have not receveid an callback onResponse in arg1") ;

        } ) ;

    } ) ;

}