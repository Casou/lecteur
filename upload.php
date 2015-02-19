<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

// ******* /!\ augmenter le upload_max_filesize et le post_max_size du php.ini à 8000M /!\ *******
// ini_set("upload_max_filesize", "8000M");
?>

<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<meta name="viewport" content="width=620">
<title>HTML5 Demo: Drag and drop, automatic upload</title>
<link rel="stylesheet" href="style/html5demos.css">
<script src="js/h5utils.js"></script></head>
<body>
-->

<div id="title">
	<h1>Uploader des vidéos</h1>
</div>

<section id="wrapper">


<style>
#holder { border: 10px dashed #ccc; width: 300px; min-height: 300px; margin: 20px auto;}
#holder.hover { border: 10px dashed #0c0; }
#holder img { display: block; margin: 10px auto; }
#holder p { margin: 10px; font-size: 14px; }
progress { width: 100%; }
progress:after { content: '%'; }
.fail { background: #c00; padding: 2px; color: #fff; }
.hidden { display: none !important;}
</style>
<article>
  <div id="holder">Insérez un fichier ici par glissé-déposé.
  </div> 
  <p id="upload" class="hidden"><label>Drag & drop not supported, but you can still upload via this input field:<br><input type="file"></label></p>
  <p id="filereader">File API & FileReader API not supported</p>
  <p id="formdata">XHR2's FormData is not supported</p>
  <p id="progress">XHR2's upload progress isn't supported</p>
  <p><b>Progression : </b><progress id="uploadprogress" min="0" max="100" value="0">0</progress></p>
  
</article>

<script>
var holder = document.getElementById('holder'),
    tests = {
      filereader: typeof FileReader != 'undefined',
      dnd: 'draggable' in document.createElement('span'),
      formdata: !!window.FormData,
      progress: "upload" in new XMLHttpRequest
    }, 
    support = {
      filereader: document.getElementById('filereader'),
      formdata: document.getElementById('formdata'),
      progress: document.getElementById('progress')
    },
    /*
    acceptedTypes = {
      'video/mp4': true,
      'video/ogv': true,
      'video/avi': true,
      'video/ogg': true,
      'video/webm': true,
      'video/mov': true
    },
    */
    progress = document.getElementById('uploadprogress'),
    fileupload = document.getElementById('upload');

"filereader formdata progress".split(' ').forEach(function (api) {
  if (tests[api] === false) {
    support[api].className = 'fail';
  } else {
    // FFS. I could have done el.hidden = true, but IE doesn't support
    // hidden, so I tried to create a polyfill that would extend the
    // Element.prototype, but then IE10 doesn't even give me access
    // to the Element object. Brilliant.
    support[api].className = 'hidden';
  }
});

function previewfile(file) {
  if (tests.filereader === true && acceptedTypes[file.type] === true) {
    var reader = new FileReader();
    reader.onload = function (event) {
      var image = new Image();
      image.src = event.target.result;
      image.width = 250; // a fake resize
      holder.appendChild(image);
    };

    reader.readAsDataURL(file);
  }  else {
    holder.innerHTML += '<p>Uploaded ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '');
    console.log(file);
  }
}


function readfiles(files) {
    // debugger;
    var formData = tests.formdata ? new FormData() : null;
    for (var i = 0; i < files.length; i++) {
      if (tests.formdata) formData.append('file', files[i]);
      // previewfile(files[i]);
    }

    // now post a new XHR request
    if (tests.formdata) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'uploadVideo.php');
      xhr.onload = function() {
        progress.value = progress.innerHTML = 100;
      };

      xhr.onreadystatechange = function () {
          if(xhr.readyState == 4) {
              if (xhr.status != 200 && xhr.status != 0) {
	        	  alert(xhr.responseText);
	              // alert('callback');
	              // callback(xhr.responseXML);
	              xhr.abort();
              }
              
              redirect("upload.php");
          }
          
      };

      if (tests.progress) {
        xhr.upload.onprogress = function (event) {
          if (event.lengthComputable) {
            var complete = (event.loaded / event.total * 100 | 0);
            progress.value = progress.innerHTML = complete;
          }
        }
      }

      xhr.send(formData);
    }
}

if (tests.dnd) { 
  holder.ondragover = function () { this.className = 'hover'; return false; };
  holder.ondragend = function () { this.className = ''; return false; };
  holder.ondrop = function (e) {
    this.className = '';
    e.preventDefault();
    readfiles(e.dataTransfer.files);
  };
} else {
  fileupload.className = 'hidden';
  fileupload.querySelector('input').onchange = function () {
    readfiles(this.files);
  };
}

</script>

<!-- 
</body>
</html>
-->

<?php
include_once $pathToPhpRoot."pied.php";
?>