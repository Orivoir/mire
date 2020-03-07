// add global function for inrement/decrement count new message


let countNewMessageEl = null ;

document.addEventListener('DOMContentLoaded' , () => {

    countNewMessageEl = document.querySelector('.new-messages-count') ;
} ) ;

function getCountNewMessage() {

    return parseInt( countNewMessageEl.textContent ) ;
}

function newCountNewMessage( count ) {

    if(
        typeof count != "number" ||
        isNaN( count )
    ) {
        return false ;
    }

    count = count < 0 ? 0: count ;

    countNewMessageEl.textContent = count > 0 ? count: "" ;

    return true ;
}
