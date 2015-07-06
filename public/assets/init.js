require(['jquery', 'bootstrap', 'selectize'], function($, bs) {
    $('select[name=good_id]').selectize({
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        options: [],
        create: false,
        load: function(query, callback) {
            if (!query.length) return callback();
            $.ajax({
                url: '/ajax/goods',
                type: 'GET',
                dataType: 'json',
                data: {
                    q: query
                },
                error: function() {
                    callback();
                },
                success: function(res) {
                    callback(res.goods);
                }
            });
        }
    });
});
