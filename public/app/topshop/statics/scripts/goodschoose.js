;var goodsChoose = function(options) {
    options = $.extend({
        url: '',
        getProUrl: '',
        getBrandUrl: '',
        getGoodsUrl: '',
        target: document.body,
        modalDom: '#goods_modal',
        handle: '.select-goods',
        catBox: '#goods_category',
        catlistItem: '#goods_category li',
        catDefault: '分类',
        brandBox: '#goods_brand',
        brandList: '#brand_list',
        brandListItem: '#brand_list li',
        brandDefault: '品牌',
        filterBtn: '#search_goods',
        clearFilterBtn: '#clear_filter',
        clearBrandBtn: '#clear_brand',
        submitBtn: '#choose_goods',
        checkAll: '#check_all',
        uncheckAll: '#uncheck_all',
        goodsList: '#goods_list',
        goodsListItem: '#goods_list > ul > li',
        goodsSearchKey: '#goods-search-key',
        goodsSearchBn: '#goods-search-bn',
        goodsSearchBrand: '#goods-search-brand',
        insertWhere: null,
        textcol: null,
        view: null,
        getPro: function(insertDom, getProUrl, catId, brandId, name, bn, brand, callback){
            $.ajax({
                url: getProUrl,
                type: 'POST',
                dataType: 'html',
                data: {
                    "searchname": name,
                    "searchbn": bn,
                    "searchbrand": brand,
                    "catId": catId,
                    "brandId": brandId
                },
                success: function(rs) {
                    $(insertDom).html(rs);
                    if(callback) {
                        return callback();
                    }

                }
            });
        }
        // getBrand: function(getBraUrl, catId, insertDom){
        //     if (catId != '') {
        //         $.ajax({
        //             url: getBraUrl,
        //             type: 'POST',
        //             dataType: 'json',
        //             data: {
        //                 "catId": catId
        //             },
        //             success: function(data) {
        //                 if (data) {
        //                     var result = '';
        //                     for (var i = 0; i < data.length; i++) {
        //                         result += '<li data-val="' + data[i].brand_id + '">' + data[i].brand_name + '</li>';
        //                     };
        //                     $(insertDom).empty().append(result);
        //                 }
        //             }
        //         });
        //     }
        // }

    }, options || {});

    var getProUrl,
        catId,
        brandId;

    var selectId = [];

    var url = $(options.handle).attr('data-remote') || options.url,
        editid = $(options.handle).attr('data-item_id'),
        getGoodsUrl = $(options.handle).attr('data-fetchgoods') || options.getGoodsUrl;
        insertWhere = $(options.insertWhere || '.selected-goods-list');
        limit = $(options.handle).data('limit') || options.limit;

    var data = $(options.handle).data();

    for (var i in data) {
        if(i = 'remote'){
            delete(data[i]);
        }
        if(i = 'fetchgoods'){
            delete(data[i]);
        }
        if(i = 'target'){
            delete(data[i]);
        }
        if(i = 'limit'){
            delete(data[i]);
        }
    };

    $(options.target).on('click', options.handle, function(e) {
        e.preventDefault();
        var container = $(this).parents('.select-goods-panel');

        insertWhere = $(container).find(this.getAttribute('data-insertwhere') || options.insertWhere || '.selected-goods-list');
        $(options.modalDom).modal('show');
    });

    $(function(){
        if(editid && editid.length > 0){
            $.post(getGoodsUrl, data, function(rs) {
                if(rs){
                    $(insertWhere).html(rs);
                };
            });
        }else{
            return false;
        }
    })

    $(options.modalDom).on('show.bs.modal', function() {
        selectId = [];
        var lastSelected = $(insertWhere).find('tr');
        $(lastSelected).each(function(index, el) {
            selectId.push($(el).attr('date-itemid'));
        });
        $(this).find('.modal-content').load(url);
    }).on('shown.bs.modal', function() {
        getProUrl = $(options.filterBtn).attr('data-remote') || options.getProUrl;
        options.getPro(options.goodsList, getProUrl,'','','','','',function(){
            var list = $(options.goodsList).find('li');
            for (var i = 0; i < list.length; i++) {
                var itemId = $(list[i]).attr('data-id');
                for (var j = 0; j < selectId.length; j++) {
                    if(itemId == selectId[j]){
                        $(list[i]).addClass('checked');
                    }
                };
            };
        });
    }).on('hide.bs.modal', function() {
        options.getPro(options.goodsList, getProUrl);
    }).on('click', options.catlistItem, function(e) {
        e.stopPropagation();
        var isData = $(this).attr('data-val');
        console.log($(this));
        if(isData){
            catId = $(this).attr('data-val');
            //var getBraUrl = $(options.brandBox).attr('data-remote') || options.getBrandUrl;
            var catName = $(this).children('span').text();
            //options.getBrand(getBraUrl, catId, options.brandList);
            $(this).parents('.filters-list').hide().siblings('.filter-name').text(catName);
            //$(options.brandBox).find('.filter-name').text(options.brandDefault);
        }
    }).on('click', options.brandListItem, function(){
        brandId = $(this).attr('data-val');
        var catName = $(this).text();
        $(this).parents('.filters-list').hide().siblings('.filter-name').text(catName);
    }).on('click', options.filterBtn, function() {
        var name = $(this).parent().find('input[name="item_title"]').val();
        var bn = $(this).parent().find('input[name="item_bn"]').val();
        var brand = $(this).parent().find('input[name="item_brand"]').val();
        options.getPro(options.goodsList, getProUrl, catId, brandId, name, bn, brand, function(){
            var list = $(options.goodsList).find('li');
            for (var i = 0; i < list.length; i++) {
                var itemId = $(list[i]).attr('data-id');
                for (var j = 0; j < selectId.length; j++) {
                    if(itemId == selectId[j]){
                        $(list[i]).addClass('checked');
                    }
                };
            };
        });
    }).on('click', options.goodsListItem, function(){
        $(this).toggleClass('checked');
        var dataId = $(this).data('id');

        if($(this).hasClass('checked')){
            if(limit) {
                if(typeof(limit) === 'number' && parseInt(limit) > 0){
                    limit = parseInt(limit);
                }else{
                    return
                }

                if($(options.goodsList).find('.checked').length > limit){
                    $('#messagebox').message('最多只能选择' + limit + '个商品')
                    $(this).removeClass('checked');
                    return
                }
            }
            selectId.push(dataId);
        }else{
            for (var i = 0; i < selectId.length; i++) {
                if(selectId[i] == dataId){
                    selectId.splice(i,1);
                }
            };
        }
    })
    .on('click',options.checkAll,function(){
        var list = $(options.goodsListItem);
        var thisIds = []
        list.addClass('checked')
        for (var i = 0; i < list.length; i++) {
            var ids = $(list[i]).data('id');
            thisIds.push(ids);
        };
        selectId = $.unique($.merge(selectId, thisIds));
        console.log(selectId);
    })
    .on('click',options.uncheckAll,function(){
        var list = $(options.goodsListItem);
        var thisIds = [];
        list.removeClass('checked');
        var tempRemoveIds = selectId.concat();
        for (var i = 0; i < list.length; i++) {
            var ids = $(list[i]).data('id');
            thisIds.push(ids);
        };
        for (var i = 0; i < tempRemoveIds.length; i++) {
            for (var j = 0; j < thisIds.length; j++) {
                if(tempRemoveIds[i] == thisIds[j]){
                    selectId.splice(0,1);
                    break;
                }
            }
        };
    })
    .on('mouseover mouseout', options.catBox, function(event){
        var el = $(this).children('.filters-list');

        if(event.type == "mouseover"){
          el.show()
        }else if(event.type == "mouseout"){
          el.hide()
        }
    })
    .on('mouseover mouseout', options.brandBox, function(){
        var el = $(this).children('.filters-list');

        if(event.type == "mouseover"){
          el.show()
        }else if(event.type == "mouseout"){
          el.hide()
        }
    })
    .on('mouseover mouseout', options.catlistItem, function(){
        var el = $(this).children('.child-list');

        if(!!$(el)){
            if(event.type == "mouseover"){
              el.show()
            }else if(event.type == "mouseout"){
              el.hide()
            }
        }
    })
    .on('click', options.clearFilterBtn, function(e){
        e.preventDefault();
        catId = null;
        brandId = null;
        $(options.catBox).find('.filter-name').text(options.catDefault);
        $(options.brandBox).find('.filter-name').text(options.brandDefault);
        $(options.brandList).empty();
        $(options.goodsSearchKey).val('');
        $(options.goodsSearchBn).val('');
        $(options.goodsSearchBrand).val('');
        getProUrl = $(options.filterBtn).attr('data-remote') || options.getProUrl;
        options.getPro(options.goodsList, getProUrl,'','','','',function(){
            var list = $(options.goodsList).find('li');
            for (var i = 0; i < list.length; i++) {
                var itemId = $(list[i]).attr('data-id');
                for (var j = 0; j < selectId.length; j++) {
                    if(itemId == selectId[j]){
                        $(list[i]).addClass('checked');
                    }
                };
            };
        });
    })
    .on('click', options.clearBrandBtn, function(e){
        e.preventDefault();
        brandId = null;
        $(options.brandBox).find('.filter-name').text(options.brandDefault);
    })
    .on('click', options.submitBtn, function(){
        if (selectId.length == 0) {
            $('#messagebox').message('请选择商品');
            return;
        }

        var cid = {
            'item_id' : selectId
        };
        data = $.extend(data, cid);

        $.post(getGoodsUrl, data, function(rs) {
            if (rs) {
                $(insertWhere).html(rs);
                $(options.modalDom).modal('hide');
            } else {
                $('#messagebox').message('还未添加商品');
            }
        });
    })
    .on('click', '.pagination a', function(e){
        e.preventDefault();
        if(!$(this).parent().hasClass('disabled')){
            $.post($(this).attr('href'),function(rs){
                $(options.goodsList).html(rs);
                var list = $(options.goodsList).find('li');
                for (var i = 0; i < list.length; i++) {
                    var itemId = $(list[i]).attr('data-id');
                    for (var j = 0; j < selectId.length; j++) {
                        if(itemId == selectId[j]){
                            $(list[i]).addClass('checked');
                        }
                    };
                };
            });
        }
    });
}

$(function() {
    goodsChoose();
})
