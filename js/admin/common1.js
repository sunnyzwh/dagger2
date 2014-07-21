/*
 * @autohor        :  wangfeng
 * @des            : cms system basic library,depends on jquery javascript library
 * @date           : 2012/01/11
 */





/*
 * some code test
 */

function bindOptIn() {
    var obj = $(this);
    var opt_type = obj.data('opt_type');
    if(opt_type == 'order'){
        obj.find(".sortremove").show();
        
    }
}

function bindOptOut() {
    var obj = $(this);
    var opt_type = obj.data('opt_type');
    if(opt_type == 'order'){
        obj.find(".sortremove").hide();
    }
}

/*
 * simple plugins contains loading and tooltip
 */
;(function() {
    var plugins = {};

    /*
     * simple loading;
     */
    var loading = function() {
        this.content = $('<div class="ui-loading"></div>').bgIframe();
    };
    $.extend(loading.prototype, {
        show : function(context) {
            context = $(context);
            this.context = context;
            context.append(this.content);
        },
        hidden : function() {
            this.content.remove();
        }
    });
    
    /*
     * simple tooltip
     */
    var toolTip = function() {
        var temp = ['<table id="testtable" style="font-size: 12px; position: absolute; z-index: 2000; display: none; opacity: 1; " border="0" cellpadding="0" cellspacing="0">',
                        '<tbody>',
                            '<tr>',
                                '<td style="width:19px;height:15px;background:url(http://news.sina.com.cn/deco/2010/0318/tips.png);"></td>',
                                '<td style="height:15px;background:url(http://news.sina.com.cn/deco/2010/0318/tips-x.png) repeat-x;"></td>',
                                '<td style="width:19px;height:15px;background:url(http://news.sina.com.cn/deco/2010/0318/tips.png) -19px 0;"></td>',
                            '</tr>',
                            '<tr>',
                                '<td style="width:19px;background:url(http://news.sina.com.cn/deco/2010/0318/tips-y.png) repeat-y;"></td>',
                                '<td style="height:30px;background-color:#FFF;" id="tipContent"></td>',
                                '<td style="width:19px;background:url(http://news.sina.com.cn/deco/2010/0318/tips-y.png) -9px 0 repeat-y;"></td>',
                            '</tr>',
                            '<tr>',
                                '<td style="width:19px;height:29px;background:url(http://news.sina.com.cn/deco/2010/0318/tips.png) 0 -15px;"></td>',
                                '<td style="height:29px;background:url(http://news.sina.com.cn/deco/2010/0318/tips-x.png) 0 -6px repeat-x;text-align:left;"><img src="http://news.sina.com.cn/deco/2010/0318/tips-d.png" style="display:inline;"></td>',
                                '<td style="width:19px;height:29px;background:url(http://news.sina.com.cn/deco/2010/0318/tips.png) -19px -15px;"></td>',
                            '</tr>',
                        '</tbody>',
                    '</table>'].join(''),
            tipContainer = $(temp);
        $('body').prepend(tipContainer);
        this.tipContainer = tipContainer;
        this.tipContent = tipContainer.find('#tipContent');
    };
    $.extend(toolTip.prototype, {
        show : function(content, context) {
            var left = $(context).offset().left - 20,
                  top  = $(context).offset().top,
                  showTop = top - 45;
                  me = this;
            this.tipContent.html(content || '');
            this.tipContainer.css({
                'left' : (left + 'px'),
                'top'  : (showTop + 'px'),
                'display' : 'block',
                'opacity' : 0
            });
             this.tipContainer.animate({'top': (showTop - 25) + 'px','opacity': 1}, 250, 'swing', function(){
                    setTimeout(function(){me.hidden()},3000);
             });
        },
        hidden : function() {
            this.tipContainer.fadeOut('slow');
        }
    });
    plugins.toolTip = new toolTip();
    plugins.loading = new loading();
    window.plugins = plugins;
})();


/*
 * callbacks of event
 */

(function(){
    
    var loading = plugins.loading,
        toolTip = plugins.toolTip,
        buttonOptionsSelecter = '.opt',
        buttonFormSubmit = '#submit',
        optionButtonTypes = 'refresh, add, delete, update, search, sortable'.split(", "),
        beforeSubmitSuccess = "处理中...",
        aferSubmitSuccess = "提交成功",
        refreshContentSelecter = ".ui-list-table",
        tabSelecterContent = ".main-content",
        validation = function(){
            //add some code
            return true;
        }
        
        optionsBtnEventSys = function(){
            this.init();
        };
        $.extend(optionsBtnEventSys.prototype, {
            init : function(){
                var me = this;
                this._resetElms();
                $(buttonOptionsSelecter).live({
                    click : function(){me.clickCallback(this)},
                    mouseenter : bindOptIn,
                    mouseleave : bindOptOut
                });
            },
            _resetElms : function(){
                this.dom = null;
                this.type = '';
                this.wrapper = null;
                this.title   = "";
                this.action  = "";
                this.width   = 50;
                this.editorId = '';
                this.end = false;
                this.url = "";
            },
            clickCallback : function(that){
                var me = this,
                    dom = $(that);
                // show loading 
                    me._resetElms();
                    me.dom = dom;
                    me.type   = dom.data('opt_type');
                    me.wrapper = $("#"+dom.data('opt_id'));
                    me.url = dom.data('opt_url');
                    me.action = dom.data('opt_action');
                    if( typeof (beginOpt) == 'function') {
                        if(!beginOpt(me.dom)) {
                            return false;
                        }
                    }

                    
                    /*
                     *  todo   add some code for begin options
                     */                
                    switch(me.type){
                        case "add":
                            me.addORupdateCallback(false);
                            break;
                        case 'delete' :
                            me.deleteCallback();
                            break;
                        case 'update' :
                            me.addORupdateCallback(true);
                            break;
                        case 'search' :
                            me.refreshORsearchCallback(true);
                            break;
                        case 'order' :
                            me.sortCallback();
                            break;
                        case 'refresh' :
                            me.refreshORsearchCallback(false);
                            break;
                        default:
                            me.defaultCallback();
                    };
            },
            sortCallback : function(){
                var currentClass = this.dom.attr('class'),
                    orderAttr    = this.dom.data('opt_name');
                if(currentClass.indexOf("sortremove") !== -1){
                    $.orderBy.remove(this.dom);
                }else if(currentClass.indexOf("ascending") !== -1){
                    $.orderBy.desc(this.dom);
                }else if(currentClass.indexOf("sortable") !== -1 || currentClass.indexOf("descending") !== -1){
                    $.orderBy.asc(this.dom);
                }
            },        
            refreshORsearchCallback : function(search,data, url) {
                var rhasParam = /\?/g,
                    that = this,
                    hasParams = !!rhasParam.test(this.url),
                    value = this.dom.val(),
                    wrapperId = "#"+this.dom.data("opt_id"),
                    containner = this.wrapper.children().get(0) ? this.wrapper.children().get(0).nodeName : '',
                    form =  search ? (this.dom.parents('form')) : '',
                    action = search ? (this.url + (hasParams ? '&' : '?') + form.formSerialize()) : url ? url : this.dom.data("opt_url"),
                    width = this.wrapper.width(),
                    cssStr = !search ? (' position:relative; left:' + (width/2 - 65) + 'px') : (''),
                    me = this.dom,
                    firstSelecter = search ? ':first' : '';
                !this.url && (this.url = (url || location.href));
                this.wrapper.html('<div style="text-align:center;padding-top:20px;' + cssStr + '"><div><img src="http://news.sina.com.cn/deco/2010/0309/20070311141345452.gif" /></div><div>数据载入中...</div></div>');
                
                setTimeout(function(){
                    that.dom.data('opt_disabled', true);
                    that.wrapper.load(action + ' ' + wrapperId + ' ' + containner + firstSelecter ,data, function(){
                        me.data('opt_disalbed', false);
                        if( typeof (endReload) == "function") {
                            endReload();
                        }
                        return true;
                    });
                },0)
            },

            deleteCallback : function(){
                var time = (new Date()).getTime(),
                    postArr,
                    _post,
                    postdata,
                    me = this;
                postdata = 'format=json&flushaaa=' + time;
                if(typeof(me.dom.data('opt_post')) !== "undefined") {
                    postdata = postdata + '&' + me.dom.data('opt_post');
                }
            
                $.post(this.url,postdata,function(json){
                    var data = json.result.status.msg;
                    if (typeof(endOpt) == 'function') {
                        if (!endOpt(me.dom, json)){
                            return false;
                        }
                    }
                    data && me.showMsg(data,me.dom)
                });
            },
            searchCallback : function(){
                
                var submitBtn = $('input[type="submit"]'),
                    btnValue  = submitBtn.val(),
                    dataList  = $('.ui-list-table'),
                    me        = $(this.dom),
                    rhasParam = /\?/g,
                    action    = me.attr('action'),
                    hasParams = !!rhasParam.test(action);
                    
                action    = action + (hasParams ? '&' : '?') +  me.formSerialize();
                submitBtn.val(btnValue + '...');
                submitBtn.data('opt_disalbed', true);
                dataList.empty();
                dataList.load(action + '.ui-list-table', function(){
                    submitBtn.val(btnValue.replace(/\.\.\./g, ''));
                    submitBtn.data('opt_disalbed', false);
                })
                return false;
                
            },
            defaultCallback : function(){
                 // this.loading.hidden();
                  this.showMsg('无操作', this.dom);
                  return false;
            },
            addORupdateCallback : function(update){
                var that = this;
                loading.show(document.body);
                setTimeout(function(){
                    var wrapper = $(that.wrapper),
                        form    = wrapper.find('form'),
                        dom     = that.dom,
                        editor,
                        me = that,
                        action,
                        title,
                        postArr,
                        width;
                    that.editorId = editor = dom.data('opt_editor');
                    that.title   = title = dom.attr('title');
                    that.action  = action = dom.data('opt_action');
                    that.width   = width = dom.data('opt_width');
                    that.url = dom.data('opt_url');
                    that.form = form;
                    
                    if(typeof(dom.data('opt_post')) !== "undefined") {
                        postArr = dom.data('opt_post').split("&");
                        for ( var i=0 ; i < postArr.length ; ++i ) {
                            var _post = postArr[i].split("=");
                            form.append("<input type='hidden' data-opt_post_name="+_post[0]+" name='"+_post[0]+"' value='"+_post[1]+"'>"); 
                        }
                    }
                    form && form.resetForm(editor);
                    
                    form && form.submit(function(){
                        me.formSubmit.call(me);
                        return false;
                    });
                    /*
                     *  add some code edit multiplites
                     */
                    
                    action && form.attr('action', action);
                    
                    //initialize dialog
                    that.width = width ? width : wrapper.width() + 50;
                    
                    //todo loading hidden
                    that._dialogConfig();
                    
                    //initialize editor
                    editor && that._initEditor(editor);
                    
                    if(typeof(multiselectors) !== "undefined"){
                        for(var ms in multiselectors){
                            multiselectors[ms].lozengeGroup.removeAllItems();
                        }
                    }

                    update && that._getData();
                    
                    /*
                     * ui checkbox
                     */
                    $('.ui-checkboxes').each(function(){
                        $(this).buttonset();
                    });
                    /*
                     * ui radio
                     */
                    $('.ui-radioes').each(function(){
                        $(this).buttonset();
                    });
                    //this.loading.hidden();
                    loading.hidden();
                
                    that.openDialog(wrapper);
                    
                    
                    
                },0)
                
            },
            closeCallback : function(){
                this.form.find("input[data-opt_post_name]").remove();
                // todo add some code
            },
            _dialogConfig : function(){
                var me = this;
                this.wrapper.dialog({
                    title: this.title,
                                    autoOpen: false,
                                    minHeight: 100,
                                    minWidth: this.width,
                                    modal: true,
                                    close : function(){me.closeCallback();me.form.unbind('submit');}
                })
            },
            _initEditor : function(name){
                    if(editors[name]){
                    return;
                    }
                        },
               _getData : function(){
                   var url = this.url,
                       method = this.method || 'get',
                       me = this;
                   $.ajax({
                       url: url, 
                    dataType: 'json',
                    method : method,
                    async: false, 
                    data: {format: 'json', flushaaa: (new Date).getTime()},
                    success : function(data){me.onGetSuccess.call(me, data) }
                   })    
               },
               onGetSuccess : function(data){
                   var code = data.result.status.code,
                       results = data.result.data,
                       msg = data.result.status.msg,
                       name,
                       temp,
                       // multiselectors,
                       multiElm;
                if(code != '0'){
                    this.end = true;
                    this.showMsg(msg, this.dom);
                    return;
                }                       
                       
                   for(name in results){
                       try{
                           temp = String(results[name]);
                           if(/^SELECTED:\[\'.*\'\]$/.test(temp)){
                               temp = temp.replace(/^SELECTED:\[\'(.*)\'\]/g, "$1");
                            temp = eval("['" + temp + "']");//转为数组，兼顾checkbox，radio    
                            if(multiselectors && multiselectors[name]){
                                 for(multiElm in temp){
                                     var option = $('select[name="'+name+'[]"] option[value="'+temp[multiElm]+'"]');
                                     
                                     multiselectors[name]._addItem({value:option.attr("value"), label:option.text()});
                                 }
                            } else {
                                $("[name='" + name + "[]']", this.form).val(temp);
                            }

                           }else if(/^IMG:\[\'.*\'\]$/.test(temp)) {
                               temp = temp.replace(/^IMG:\[\'(.*)\'\]/g, "$1");
                               $("#show_img_" + name).attr('src', temp);
                               $("[name='" + name + "']", this.form).val(temp);
                           }else if(typeof(ueditors) !== "undefined" && typeof(ueditors[name]) !== "undefined") {
                            ueditors[name].setContent(temp)
                        }else{ 
                            // temp = temp.replace(/\'/g, "\\'").replace(/\n/g, "\\n").replace(/\r/g, "\\r");
                            // temp = eval("['" + temp + "']");
                            //转为数组，兼顾checkbox，radio
                            temp = new Array(temp);
                            $("[name='" + name + "']", this.form).val(temp);
                        }
                           
                       }catch(e){
                           throw e;
                       }
                   }
                if (typeof(endGetData) == 'function') {
                    if (!endGetData(this.dom, data)){
                        return false;
                    }
                }
               },
               showMsg : function(content, context){
                   toolTip.show.call(toolTip, content, context);
               },
               formcloseCallback : function(dom){
                   this.closeDialog(dom);
               },
               submitCallback : function(json, status, xhr, form){
                    var btnSubmit = this.form.find("input[type='submit']"),
                       me = this,
                       code = json.result.status.code,
                       data = json.result.status.msg;
                    btnSubmit.attr('disabled', false);
                    btnSubmit.val('提交');
                    (this.dom.data('opt_disabled') != 'true') && (this.dom.data('opt_disabled', false));
                    if( typeof (endOpt) == 'function') {
                        if(!endOpt(me.dom, json)) {
                            return false;
                        }
                    }
                    if(code == '0'){
                        this.closeCallback();
                        this.formcloseCallback(this.wrapper);
                        this.showMsg(data, this.dom);
                        return false;
                    }
                    
                    /*
                    * some code for fail submit
                    */
                    !$('#ui-fail-msg').get(0) && (this.failMsg = $('<div class="ui-fail-msg" id="ui-fail-msg">' + json.result.status.msg + '</div>'));
                    this.form.prepend(this.failMsg);
                    var input = this.form.find("input[name='" + json.result.data.field + "'],textarea[name='" + json.result.data.field + "'],select[name='" + json.result.data.field + "'],radio[name='" + json.result.data.field + "'],checkbox[name='" + json.result.data.field + "']");
                    var whenInput = function(){
                        me.failMsg.fadeOut('slow', function(){
                            me.failMsg.remove();
                            this.failMsg = null;
                        });
                        input.removeClass('ui-fail-field');
                    };
                    input.keyup(function() {
                    whenInput();
                    });
                    input.mouseup(function(){
                        whenInput();
                    })
                    //input.css("backgroundColor","#FB5454");
                    input.addClass('ui-fail-field');
                    for (var i = 0; i < 3; i++) {
                        input.animate({opacity:'0.3'},500);
                        input.animate({opacity:'1'},500);
                    }
                    //this.showMsg(msg, form.find("input[name='" + json.result.data.field + "']"));
                    input.focus();

               },
               openDialog : function(dom){
                   dom.dialog('open');
               },
               closeDialog : function(dom){
                   // dom.form.find("input[data-opt_post_name]").remove();
                   dom.dialog('close');
               },
               formSubmit : function(){
                   var that = this, 
                       options = {
                    //target:'#data_list',
                        success: function(data){that.submitCallback(data)},        //回调
                        dataType:'json',   
                        data:{format:'json'},
                        beforeSubmit:validation            //提交前验证表单，暂不使用
                    },
                    me = this.form,
                    submitBtn = me.find("input[type='submit']");
                submitBtn.attr('disabled', true);//disabled提交按钮
                submitBtn.val('处理中...');//提交按钮
                //errorMessage.remove();
                if(typeof(ueditors) !== 'undefined'){
                            for(var ed in ueditors){
                                ueditors[ed].sync(); 
                            }
                        }
                me.ajaxSubmit(options);
                return false;
               }
        });
        window.sys = new optionsBtnEventSys();
})();
