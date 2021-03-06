require.config({
    "baseUrl": "/",
    "paths": {
        "jquery": "assets/jquery",
        "bootstrap": "assets/bootstrap/js/bootstrap.min",
        "selectize": "assets/selectize/selectize"
    },
    "shim": {
        "bootstrap": {
            "deps": [
                "jquery"
            ]
        },
        "selectize": {
            "deps": [
                "jquery"
            ]
        }
    }
});

require(['jquery', 'bootstrap', 'selectize'], function($, bs) {
    $('select[name=good_id]').add('select[name=parent_id]').selectize({
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        options: [],
        create: false,
        load: function(query, callback) {
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

    $('select[name=shop_id]').selectize({
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        options: [],
        create: false,
        load: function(query, callback) {
            $.ajax({
                url: '/ajax/shops',
                type: 'GET',
                dataType: 'json',
                data: {
                    q: query
                },
                error: function() {
                    callback();
                },
                success: function(res) {
                    callback(res.shops);
                }
            });
        }
    });
});
