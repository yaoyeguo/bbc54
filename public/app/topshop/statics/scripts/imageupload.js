var imageUpload = function(options) {

    options = $.extend({
        url: '',
        target: document.body,
        fileName: 'upload_files',
        inputName: 'images[]',
        size: 500 * 1024,
        width: 50,
        height: 50,
        multiple: true,
        isModal: false,
        limit: 0,
        handle: '.action-upload',
        file_input: '.action-file-input',
        insertWhere: null,
        callback: null
    }, options || {});

    // var compos = '<div class="choose-image"><span class="image-box"></span><b class="action-upload" title="选择图片"><i class="icon-arrow-right-b"></i></b></div>';

    // var file_input_old = $(options.target).find('input[type=file]' + options.file_input);
    // var file_input = file_input_old.clone(true, true).attr('name', '');

    // compos = $(compos).insertAfter(file_input_old).prepend(file_input);
    // file_input_old.remove();


    var button, limit, isMultiple, name, uploadItem, hasImgNum;
    var selectId = [];

    $(options.target).on('click', options.handle, function(e) {
        var handle = $(this);
        var container = handle.parent();
        var file_input = container.find(options.file_input);
        file_input.click();
    })
    .on('change', options.file_input, function(e) {
        var file_input = $(this);
        var container = file_input.parent();
        var handle = container.find(options.handle);

        var fileName = this.getAttribute('data-filename') || options.fileName;
        var size = this.getAttribute('data-size');
        size = Number(size) || options.size;


        var insertWhere = container.find(this.getAttribute('data-insertwhere') || options.insertWhere || '.image-box');

        var multiple = this.multiple;
        var isModal = this.getAttribute('data-ismodal');
        var url = this.getAttribute('data-remote') || options.url;
        var limit = this.getAttribute('data-max') || options.limit;
        var inputName = this.getAttribute('name') || options.inputName;
        var callback = this.getAttribute('data-callback') || file_input.data('callback') || options.callback;

        var data = new FormData();
        var files = this.files;

        if (limit) {
            var length = container.find('.img-thumbnail:not(.action-upload)').length,
                filelen;
            if(multiple && files) {
                filelen = files.length;
            }
            else {
                filelen = 1;
            }
            if(length + filelen > limit) {
                alert('超出限制，最多上传' + limit + '张。');
                return false;
            }
        }

        if(!files || !Array.prototype.slice.call(files).every(function (file, i) {
            if(file.size > size) {
                $('#messagebox').message('抱歉，上传图片 "' + file.name + '" 须小于' + size / 1024 + 'kB!');
                file_input.val('');
                return false;
            }
            if(multiple) {
                data.append(fileName + '[]', file);
            }
            else {
                data.append(fileName, file);
            }
            return true;
        })) return false;

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (rs) {
                try {
                    rs = JSON.parse(rs);
                }
                catch (e) {}
                if(rs.success && rs.data) {
                    if(callback) {
                        return callback(rs);
                    }
                    var html = '';
                    if(multiple) {
                        if(isModal) {
                            $('#areaselect_modal').modal('hide');

                            var type = $('.nav-tabs .active').attr('data-type');
                            if($('.has-searched')){
                                var name = $('.has-searched').val();
                            }else{
                                var name = '';
                            }
                            if($('.gallery-condition .active').hasClass('time')){
                                var orderBy = $('.gallery-condition .active').attr('data-order') + ' ' + $('.gallery-condition .active').attr('data-sort');
                            }else{
                                var orderBy = $('.gallery-condition .active').attr('data-order');
                            }
                            $.each(rs.data, function (k, data) {
                                if(data.url) {
                                    var item = {}
                                    item.img_id = data.image_id;
                                    item.img_src = data.t_url;
                                    selectId.push(item);
                                    if(isMultiple && isMultiple == 'true') {
                                        html += '<div class="col-sm-3"><div class="thumbnail checked"><div class="img-show"><a href='+ data.url +'"><img src="' + data.t_url +'"></a></div><div class="caption" data-name="'+ data.url +'" data-url="'+ data.url +'"><p class="image-name">'+ data.image_name +'</p></div></div></div>';
                                    }
                                }
                            });
                            
                            $(html).insertBefore($('.gallery > div:first-child'));
                        }else{
                            insertWhere = handle;
                            $.each(rs.data, function (k, data) {
                                if(data.url) {
                                    html += '<div class="handle img-thumbnail"><i class="icon-close-b" onclick="$(this).parent().remove();"></i><img src="' + data.url +'"><input type="hidden" name="' + inputName + '" value="' + data.image_id +'"></div>';
                                }
                            });
                            $(html).insertBefore(insertWhere);
                        }
                    }else{
                        var data = rs.data[fileName];
                        if(data.url) {
                            html = '<img src="' + data.url +'"><input type="hidden" name="' + inputName + '" value="' + data.image_id +'">';
                        }
                        $(insertWhere).html(html);
                    }
                }else if(rs.message) {
                    $('#messagebox').message(rs.message);
                    file_input.val('');
                }
            },
            error: function () {
                $('#messagebox').message('上传出错，请重试');
            }
        });
    });

    $('#gallery_modal').on('shown.bs.modal', function(event) {
        selectId = [];
        var that = $(this);
        button = $(event.relatedTarget);
        limit = button.attr('data-limit');
        isMultiple = button.attr('data-isMultiple');
        name = button.attr('data-name');
        uploadItem = button.parents('.multiple-upload').find('.multiple-item');
        hasImgNum = button.parent().find('.multiple-item');
        $('#gallery_modal').attr('data-ids',selectId)
        if (hasImgNum) {
            $(hasImgNum).each(function(index, el) {
                var item = {}
                item.img_id = $(el).find('input[name^="list"]').val();
                item.img_src = $(el).find('img').attr('src');
                selectId.push(item);
            })
            isChecked()
        }
        $(this).on('click','.gallery-modal-tabs li a', function(e) {
            e.preventDefault();
            $('.gallery-modal-tabs li').removeClass('active');
            $(this).parent().addClass('active');
            var urlData = $(this).attr('href');
            $.post(urlData, function(data) {
                $('.gallery-modal-content').empty().append(data);
                isChecked()
            });
        })
    })
    .on('click','.action-save',function(){
        if(isMultiple && isMultiple == 'true') {
            var multipleList = '';
            for (var i = 0; i < selectId.length; i++) {
                multipleList +=  '<div class="multiple-item">'
                                + '<div class="multiple-del glyphicon glyphicon-remove-circle"></div>'
                                + '<a class="select-image">'
                                + '<input type="hidden" name="'+ name +'" value="'+ selectId[i].img_id +'">'
                                + '<div class="img-put">'
                                + '<img src="'+ selectId[i].img_src +'">'
                                + '<i class="glyphicon glyphicon-picture"></i>'
                                + '</div>'
                                + '</a>'
                                + '</div>';
            };
            if(selectId.length == 0){
                $('#messagebox').message('请选择图片!');
                return;
            }
            if(selectId.length > limit){
                $('#messagebox').message('您选择得图片数量超出最大限制!');
                return;
            }
            button.parents('.multiple-upload').find('.multiple-item').remove();
            $('#gallery_modal').modal('hide');
            button.before(multipleList);
        }else{
            var imgList = $('#gallery_modal').find('.checked');
            var imgsrc = imgList.find('img').attr('src');
            var url = imgList.find('.caption').attr('data-name');
            var img = '<img src="' + imgsrc + '">';
            if(imgList.length>0){
                button.find('.img-put').empty().append(img);
                button.find('input').val(url);
                $('#gallery_modal').modal('hide');
            }else{
                $('#messagebox').message('请选择图片!');
            }
        }
    })
    .on('hide.bs.modal', function (e) {
        $(this).removeData('bs.modal');
    })
    .on('click','.thumbnail',function(e){
        e.preventDefault();
        var item = {};
        item.img_id = $(this).find('.caption').attr('data-name')
        item.img_src = $(this).find('.caption').attr('data-url')
        if(isMultiple && isMultiple == 'true'){
            $(this).toggleClass('checked');
            if($(this).hasClass('checked')){
                selectId.push(item);
            }else{
                for (var i = 0; i < selectId.length; i++) {
                    if(selectId[i].img_id == item.img_id){
                        selectId.splice(i,1);
                    }
                };
            }
        }else{
            $(this).parent().siblings().find('.thumbnail').removeClass('checked');
            $(this).addClass('checked');
        }
    }).on('click', '.pagination a', function(e){
        e.preventDefault();
        if(!$(this).parent().hasClass('disabled')){
            $.post($(this).attr('href'),function(rs){
                setTimeout(function(){
                    var list = $('.img-show a');
                    if(hasImgNum){
                        for (var j = 0; j < list.length; j++) {
                            for (var i = 0; i < selectId.length; i++) {
                                if(selectId[i] == $(list[j]).attr('href')){
                                    $(list[j]).parent().parent().addClass('checked');
                                }
                            };
                        };
                    }
                },100)
            });
        }
    });

    function isChecked() {
        var list = $('.img-show a');
        for (var j = 0; j < list.length; j++) {
            for (var i = 0; i < selectId.length; i++) {
                if (selectId[i].img_id == $(list[j]).attr('href')) {
                    $(list[j]).parent().parent().addClass('checked');
                }
            };
        };
    }

    $('.note-image-dialog').on('click','.action-save',function(){
        var imgList = $('.note-image-dialog').find('.checked');
        var imgsrc = imgList.find('img').attr('src');

        if(imgList.length>0){
            $('.note-image-dialog').modal('hide');
        }else{
            $('#messagebox').message('请选择图片!');
        }
    })
    .on('click','.thumbnail',function(){
        $(this).parent().parent().find('.thumbnail').removeClass('checked');
        $(this).addClass('checked');
        var imgList = $('.note-image-dialog').find('.checked');
        var url = imgList.find('.caption').attr('data-url');
        $(this).parents('.modal-body').find('.note-image-url').val(url);
    });

    $('.multiple-upload').on('click','.multiple-del',function(){
        $(this).parents('.multiple-upload').find('.multiple-add').show();
        $(this).parent().remove();
    })

    function getList(type,orderBy,name) {
        $.post('<{url action=topshop_ctl_shop_image@search imageModal=true}>', {'img_type': type, 'orderBy': orderBy, 'image_name': name}, function(data) {
          $('.gallery-modal-content').empty().append(data);
        });
    }
}

jQuery(function(){
    imageUpload();
});
