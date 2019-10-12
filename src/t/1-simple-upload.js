window.addEventListener("load", function(){
  // GET THE DROP ZONE
  var uploader = document.getElementById('uploader');

  // STOP THE DEFAULT BROWSER ACTION FROM OPENING THE FILE
  uploader.addEventListener("dragover", function (e) {
    e.preventDefault();
    e.stopPropagation();
  });

  // ADD OUR OWN UPLOAD ACTION
  uploader.addEventListener("drop", function (e) {
    e.preventDefault();
    e.stopPropagation();

    // RUN THROUGH THE DROPPED FILES + AJAX UPLOAD
    for (var i = 0; i < e.dataTransfer.files.length; i++) {
      var xhr = new XMLHttpRequest(),
          data = new FormData();
      data.append('file-upload', e.dataTransfer.files[i]);
      xhr.open('POST', 'simple-upload.php', true);
      xhr.onload = function (e) {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            // OK - Do something
            // console.log(xhr.responseText);
            alert("Upload OK!");
          } else {
            // ERROR - Do something
            // console.error(xhr.statusText);
            alert("Upload error!");
          }
        }
      };
      xhr.onerror = function (e) {
        // ERROR - Do something
        // console.error(xhr.statusText);
        alert("Upload error!");
      };
      xhr.send(data);
    }
  });
});