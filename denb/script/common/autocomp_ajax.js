$( '.search-company' ).autocomplete({   
    source: function(req, resp){
                $.ajax({
                    url: "../../functions/common/autocomplete.php",
                    type: "POST",
                    cache: false,
                    dataType: "json",
                    data: {
                    param1: req.term
                    },
                    success: function(o){
                        resp(o);
                    },
                    error: function(xhr, ts, err){
                        resp(['']);
                    }
                });
            }
});
