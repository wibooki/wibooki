function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // Loop through the FileList and render image files as thumbnails.
    for (var i = 0, f; f = files[i]; i++) {

        // Only process image files.
        if (!f.type.match('image.*')) {
            continue;
        }

        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function() {
            return function(e) {
                document.getElementById('configuration-image-upload').src=e.target.result;
            };
        })();

        // Read in the image file as a data URL.
        reader.readAsDataURL(f);
    }
}

document.getElementById('profile-upload').addEventListener('change', handleFileSelect, false);