jQuery(function ($) {
    loadPreviewImages(false);
    $('.preparser-form-input').click(function () {
        $(this).select();
    });
    $("#preparser-form").submit(function (e) {
        e.preventDefault();
        loadPreviewImages(true);
    });
    function loadPreviewImages(ifFullUrlReceived) {
        ifFullUrlReceived = typeof ifFullUrlReceived !== 'undefined' ? ifFullUrlReceived : true;
        var ajaxurl = sa_ytp.ajax_url;
        var uriRaw = $(".preparser-form-input").val();
        var uriDef = $(".preparser-form-input").attr('data-def');
        if (!(uriRaw)) {
            uriRaw = uriDef;
        }
        var setLoader = new Promise(function (resolve, reject) {
            $('.preparser-result-wr').css({'height': $('.preparser-result-wr').height()});
            $('.preparser-result-wr').addClass('waiting');
            resolve('set loading');
        });
        var getPreviewImg = [];
        getPreviewImg.push(
            new Promise(
                function (resolve, reject) {
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'get_preview_img',
                            url: uriRaw,
                            if_full_url_received: ifFullUrlReceived,
                            type: 'small'
                        },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                $('.js-result-small-wr').show();
                                $('.js-result-small').html(response.data.html);
                            } else {
                                $('.js-result-small-wr').hide();
                            }
                            resolve('small img');
                        }
                    })
                }
            )
        );
        getPreviewImg.push(
            new Promise(
                function (resolve, reject) {
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'get_preview_img',
                            url: uriRaw,
                            if_full_url_received: ifFullUrlReceived,
                            type: 'medium'
                        },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                $('.js-result-medium-wr').show();
                                $('.js-result-medium').html(response.data.html);
                            } else {
                                $('.js-result-medium-wr').hide();
                            }
                            resolve('medium img');
                        }
                    })
                }
            )
        );
        getPreviewImg.push(
            new Promise(
                function (resolve, reject) {
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'get_preview_img',
                            url: uriRaw,
                            if_full_url_received: ifFullUrlReceived,
                            type: 'full'
                        },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                $('.js-result-full-wr').show();
                                $('.js-result-full').html(response.data.html);
                            } else {
                                $('.js-result-full-wr').hide();
                            }
                            resolve('full img');
                        }
                    })
                }
            )
        );
        getPreviewImg.push(
            new Promise(
                function (resolve, reject) {
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            action: 'get_preview_img',
                            url: uriRaw,
                            if_full_url_received: ifFullUrlReceived,
                            type: 'iframe'
                        },
                        success: function (response) {
                            console.log(response);
                            if (response.success == true) {
                                //$('.js-result-iframe').html(response.data);
                                $('.js-result-iframe iframe').attr('src', response.data.url);

                            }
                            resolve('iframe');
                        }
                    })
                }
            )
        );
        var removeLoader = new Promise(function (resolve, reject) {
            $('.preparser-result-wr').removeClass('waiting');
            $('.preparser-result-wr').css({'height': ''});
            if (uriRaw == uriDef) {
                window.history.pushState('', '', location.protocol + '//' + location.host + location.pathname);
            } else {
                window.history.pushState('', '', location.protocol + '//' + location.host + location.pathname + '?url=' + uriRaw);
            }
            resolve('remove loader');
        });

        setLoader
            .then($.apply($, getPreviewImg))
            .then(removeLoader)
    }
});