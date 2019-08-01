# jquery-uploader
基于jquery的异步上传文件插件,完整上传功能需要配合Apache或Nginx进行使用,把项目放到根目录,使用localhost访问


<input type="file" id="upload">

<input type="text" id="result">


$("#upload").uploader({
  ele:'#result'
});
