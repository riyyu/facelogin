<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>
    <div class="row py-3">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="d-flex justify-content-center">
                <video autoplay="true" id="video-webcam">
                    Browsermu tidak mendukung bro, upgrade donk!
                </video>
            </div>
            <div class="my-3">
                <input type="text" class="form-control" name="name" id="name" placeholder="Full Name">
            </div>
            <div class="my-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
            </div>
            <div class="d-grid">
                <button class="btn btn-primary" type="button" id="submit">Register</button>
            </div>
            <div class="my-3 text-center">
                <a href="{{ url('login') }}" class="link-primary">login</a>
            </div>
        </div>
        <div class="col-3"></div>
    </div>

    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        const submitButton = document.getElementById('submit');
        const nameForm = document.getElementById('name');
        const emailForm = document.getElementById('email');

        function initVideo() {
            // seleksi elemen video
            var video = document.querySelector("#video-webcam");

            // minta izin user
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia ||
                navigator.msGetUserMedia || navigator.oGetUserMedia;

            // jika user memberikan izin
            if (navigator.getUserMedia) {
                // jalankan fungsi handleVideo, dan videoError jika izin ditolak
                navigator.getUserMedia({
                    video: true
                }, handleVideo, videoError);
            }

            // fungsi ini akan dieksekusi jika  izin telah diberikan
            function handleVideo(stream) {
                video.srcObject = stream;
            }

            // fungsi ini akan dieksekusi kalau user menolak izin
            function videoError(e) {
                // do something
                alert("Izinkan menggunakan webcam untuk demo!")
            }

            return video;
        }

        var video = initVideo();

        submitButton.addEventListener('click', submitRegister);

        async function submitRegister() {
            var context;

            // ambil ukuran video
            var width = video.offsetWidth,
                height = video.offsetHeight;

            // buat elemen canvas
            canvas = document.createElement("canvas");
            canvas.width = width;
            canvas.height = height;

            canvas
                .getContext("2d")
                .drawImage(video, 0, 0, canvas.width, canvas.height);
            const image_data_url = canvas.toDataURL("image/jpeg");

            const res = await fetch(image_data_url);
            const blob = await res.blob();

            const headers = {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }

            const token = '{{ csrf_token() }}'

            const body = {
                name: nameForm.value,
                email: emailForm.value,
                image: blob
            }

            const data = new FormData();
            data.append('name', body.name);
            data.append('email', body.email);
            data.append('image', blob);

            fetch("{{ url('register') }}", {
                    headers: {
                        "X-CSRF-TOKEN": token
                    },
                    method: 'post',
                    // credentials: "same-origin",
                    body: data
                })
                .then(res => {
                    if (res.status == 201) {
                        return Swal.fire({
                            icon: 'success',
                            text: 'Register Succeed!',
                        })
                    }
                    return Swal.fire({
                        icon: 'error',
                        text: 'Register Failed!',
                    })
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        text: 'Register Failed!',
                    })
                })
        }
    </script>
</body>

</html>
