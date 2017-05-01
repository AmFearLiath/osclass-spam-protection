/*
$(document).ready(function() {
    
    var body = $("body").html();
    var search = /(\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)/g;
    var match = search.exec(body);
    var search = match[1];    
    var mail = search.split('@');
    var domain = mail[1].split('.');

    var replaced = $("body").html().replace(search,'<a class="mail" href="" data-u="'+mail[0]+'" data-d="'+domain[0]+'" data-e="'+domain[1]+'">Kontakt</a>');
    $("body").html(replaced);
    $('a.mail').hideMyEmail();

});
*/
