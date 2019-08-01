(function($){
    /*
    *  $(ele).uploader({
           ele:'',		//接收返回值的Dom
           formate:[],	//文件格式约束 array
           path:'',		//接口地址
           clear:true, //上传成功后是否清除Input值  dafault true;
       })
    *
    */

    $.fn.uploader=function(options){

        if(typeof options!=='undefined' && typeof options !=='object'){ throw new Error('config error');}

        var _Default_responseEle=this;

        var _Default_formate=['jpg','jpeg','png','gif'];		//默认上传格式限制

        var _Default_path='./upload/?m=fileupload';				//默认接口地址

        var _Default_clear=true;

        var _resonseEle=typeof options==='undefined' || typeof options.ele =='undefined' ? _Default_responseEle : options.ele;

        var _formate=typeof options ==='undefined' || typeof options.formate=='undefined' || typeof options.formate!=='object' ? _Default_formate :options.formate;

        var _path=typeof options==='undefined' || typeof options.path=='undefined' || options.path=='' ? _Default_path : options.path;

        var _clear=typeof options==='undefined' || typeof options.clear=='undefined' || options.clear=='' ? _Default_clear : options.clear;


        var ajaxUploader={
            config:{
                formate:_formate,
                path:_path
            },
            formate:function(ele){
                var uploader=this,
                    tmp=$(ele).val(),
                    name=tmp.replace(/^.+?\\([^\\]+?)(\.[^\.\\]*?)?$/gi, "$1"),
                    suffix = tmp.replace(/.+\./, "").toLocaleLowerCase();
                return uploader.config.formate.indexOf(suffix)<0?false:true;
            },
            init:function(ele,valele){
                var uploader=this;
                $(ele).change("propertychange", function() {
                    var formData = new FormData();
                        formData.append('file', $(this)[0].files[0]);
                    uploader.formate(ele)?
                        $.ajax({
                            url:uploader.config.path,
                            type:'post',
                            cache: false,
                            processData: false,
                            contentType: false,
                            data:formData,
                            beforeSend:function(){
                                uploader.before();
                            }}).then(function(res){
                            uploader.response(res,ele,valele);
                        },function(){
                            uploader.error();
                        })
                        :uploader.warning();
                });
            },
            response:function(obj,e,valele){
                var o=typeof obj !=='object' ? JSON.parse(obj) :obj,uploader=this;
                return o.errcode=='0'? uploader.success(e,o.imgpath,valele) : uploader.error(o.errortype);
            },
            before:function(){
                return toastr.info("文件上传中,请稍后");
            },
            success:function(e,v,ele){
                return toastr.success("文件上传成功") && $(ele).val(v) && this.empty(e);
            },
            error:function(t){
                return toastr.error("上传失败"+typeof t =='undefined' ? '':'原因'+t);
            },
            empty:function(e){
                _clear? $(e).val(''): '';
            },
            warning:function(){
                var msg=function(){
                    var t='';
                    for(var i in _formate){
                        t+=_formate[i]+','
                    }
                    return t.substring(0,t.length-1);
                }
                toastr.warning('上传文件格式有误,只支持'+msg()+'格式')
            }
        };
        ajaxUploader.init(this,_resonseEle);

    }
})(jQuery);
