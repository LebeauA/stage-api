Dropzone.options.dropzoneProject = {
  uploadMultiple: true,
  init:function(){
    this.on("queuecomplete", function(file,response){
      window.location.reload();
    })
  }
};
