
$(document).ready(function() {
    $(".bookfacet").hover(function() {
        var src = $(this).find("img").attr("orig");
        var bookface = $(this).parent().parent().find(".bookface");
        bookface.attr("src", src);
        bookface.removeClass("hidden");
        console.debug(src);
    }, function() {
        $(this).parent().parent().find(".bookface").addClass("hidden");
    });
});

