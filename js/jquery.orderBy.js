(function($){
    var stack = {"length_@":0};
    $.orderBy = {
       get: function(url){
                var query = {"order":{}};
                for(s in stack){
                    if(s === "length_@"){
                        continue;
                    }
                    query['order'][s] = stack[s].order;
                }
                reloadList(url, query);   
            },
       asc: function(obj){
                obj.removeClass("sortable").removeClass("sorted descending").addClass("sorted ascending");
                if(typeof(stack[obj.data('opt_name')]) === 'undefined'){
                    stack[obj.data('opt_name')] = {};
                    stack[obj.data('opt_name')].seq = ++stack['length_@'];
                }
                stack[obj.data('opt_name')].order = 'asc';
                obj.find(".sortoptions").html("<a title='Remove from sorting' class='sortremove'></a><span id='order_"+obj.data('opt_name')+"' class='sortpriority'>"+stack[obj.data('opt_name')].seq+"</span><a title='Toggle sorting' class='ascending'></a>");
                obj.find(".sortremove").bind({
                    mouseover:function(){obj.addClass("sortremove");},
                    mouseout:function(){obj.removeClass("sortremove");}
                })
                this.get(obj.attr("url"));
            },
      desc: function(obj){
                stack[obj.data('opt_name')].order = 'desc';
                obj.removeClass("sorted ascending").addClass("sorted descending");
                obj.find(".sortoptions").html("<a title='Remove from sorting' class='sortremove'></a><span id='order_"+obj.data('opt_name')+"' class='sortpriority'>"+stack[obj.data('opt_name')].seq+"</span><a title='Toggle sorting' class='descending'></a>");
                this.get(obj.attr("url"));
            },
    remove: function(obj){
                obj.removeClass("sortremove").removeClass("sorted").removeClass("ascending").removeClass("decending").addClass("sortable");
                obj.find(".sortoptions").html("");
                for(var o in stack){
                    if(o === 'length_@'){
                        continue;
                    }
                    if(parseInt(stack[o].seq) > parseInt(stack[obj.data('opt_name')].seq)){
                        --stack[o].seq;
                        $(".sortoptions").find("#order_"+o).html(stack[o].seq);
                    }
                }
                --stack['length_@'];
                delete stack[obj.data('opt_name')];
                this.get(obj.attr("url"));
            }
    }
})(jQuery)
