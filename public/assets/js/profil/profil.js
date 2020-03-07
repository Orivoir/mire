document.addEventListener('DOMContentLoaded' ,() => {

    const sendPrivateMssgBtn = document.querySelector('.send-private-mssg') ;
    const wrapPrivateMssg = document.querySelector('.form-private-message') ;

    sendPrivateMssgBtn.addEventListener('click' , () => {

        wrapPrivateMssg.classList.toggle( 'hide-o' ) ;
    } ) ;


} ) ;