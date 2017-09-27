/**
 * Created by Ameya Joshi on 28/6/17.
 */

var AssetImageUpload = function() {
    var data =  $('input[name="_token"]').val();
    var e = function() {
            var e = new plupload.Uploader({
                max_files : 20,
                runtimes: "html5,html4",
                browse_button: document.getElementById("tab_images_uploader_pickfiles"),
                container: document.getElementById("tab_images_uploader_container"),
                url: "/asset/image-upload?_token="+data,
                async:false,
                multi_selection : true,
                filters: {
                    max_file_size: "1mb",
                    mime_types: [{
                        title: "Image files",
                        extensions: "jpg,jpeg,png"
                    }]
                },
                flash_swf_url: "/assets/global/plugins/plupload/js/Moxie.swf",
                silverlight_xap_url: "/assets/global/plugins/plupload/js/Moxie.xap",
                init: {
                    PostInit: function() {
                        $("#tab_images_uploader_filelist").html(""), $("#tab_images_uploader_uploadfiles").click(function() {
                            return e.start(), !1
                        }), $("#tab_images_uploader_filelist").on("click", ".added-files .remove", function() {
                            e.removeFile($(this).parent(".added-files").attr("id")), $(this).parent(".added-files").remove()
                        })
                    },
                    FilesAdded: function(up, files) {
                        plupload.each(files, function(e) {
                            if (up.files.length > up.settings.max_files) {
                                alert('Only 20 files are allowed');
                                var index = up.settings.max_files;
                                up.removeFile(up.files[index]);
                                return false;
                            }else{
                                $("#tab_images_uploader_filelist").append('<div class="alert alert-warning added-files" id="uploaded_file_' + e.id + '">' + e.name + "(" + plupload.formatSize(e.size) + ') <span class="status label label-info"></span>&nbsp;</div>')
                            }
                        })
                    },
                    UploadProgress: function(e, a) {
                        $("#uploaded_file_" + a.id + " > .status").html(a.percent + "%")
                    },
                    FileUploaded: function(e, a, t) {
                        var t = $.parseJSON(t.response);
                        if (t.result && "OK" == t.result) {
                            $('#path').val(t.path,e.files.length);
                            $("#path").triggerHandler("change", [t.path,e.files.length]);
                            $("#uploaded_file_" + a.id + " > .status").removeClass("label-info").addClass("label-success").html('<i class="fa fa-check"></i> Done')
                            closeInSeconds: 10
                        } else $("#uploaded_file_" + a.id + " > .status").removeClass("label-info").addClass("label-danger").html('<i class="fa fa-warning"></i> Failed'), App.alert({
                            type: "danger",
                            message: "One of uploads failed. Please retry.",
                            closeInSeconds: 10,
                            icon: "warning"
                        })
                    },
                    Error: function(e, a) {
                        App.alert({
                            type: "danger",
                            message: a.message,
                            closeInSeconds: 10,
                            icon: "warning"
                        })
                    }
                }
            });
            e.init();
            e.bind('Error', function(e, x) {
                /*  console.error(x);*/
            });
            e.bind('FilesAdded', function(uploader, file) {
                e.settings.max_files = $('#max_files_count').val();
            });
        };
    return {
        init: function() {
             e();
        }
    }
}();
jQuery(document).ready(function() {
    AssetImageUpload.init()
});