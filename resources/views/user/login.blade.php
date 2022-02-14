@extends('layouts.app')

@section('content')
    <div class="row py-3">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="d-flex justify-content-center">
                <video autoplay="true" id="video-webcam">
                    Browsermu tidak mendukung bro, upgrade donk!
                </video>
            </div>
            <div class="my-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
            </div>
            <div class="d-grid">
                <button class="btn btn-primary" type="button" id="submit">Login</button>
            </div>
            <div class="my-3 text-center">
                <a href="{{ url('register') }}" class="link-primary">register</a>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        const emailForm = document.getElementById('email');
        const buttonSubmit = document.getElementById('submit');

        Promise.all([
            faceapi.nets.faceRecognitionNet.loadFromUri("/models"),
            faceapi.nets.faceLandmark68Net.loadFromUri("/models"),
            faceapi.nets.ssdMobilenetv1.loadFromUri("/models"),
        ]).then(() => {

            function initVideo() {

                // seleksi elemen video
                var video = document.querySelector("#video-webcam");

                // minta izin user
                navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator
                    .mozGetUserMedia ||
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

            const video = initVideo();

            buttonSubmit.addEventListener('click', submit)

            async function submit() {
                const data = new FormData();
                data.append('email', emailForm.value)
                const res = await fetch(`{{ url('user') }}`, {
                    headers: {
                        "X-CSRF-TOKEN": `{{ csrf_token() }}`,
                    },
                    method: 'post',
                    body: data
                });

                const json = await res.json();
                const isLoginSucced = await login(json);

                if (isLoginSucced) {
                    const res = await fetch(`{{ url('login') }}`, {
                        headers: {
                            "X-CSRF-TOKEN": `{{ csrf_token() }}`,
                        },
                        method: 'post',
                        body: data
                    });

                    location.href = `{{ url('/') }}`

                } else {
                    alert('login failed')
                }

                console.log(isLoginSucced);
                console.log(json);
            }

            async function login(json) {
                const userImage = `{{ url('images') }}` + '/' + json.image_url;
                console.log(userImage);

                var width = video.offsetWidth,
                    height = video.offsetHeight;

                // buat elemen canvas
                canvas = document.createElement("canvas");
                canvas.width = width;
                canvas.height = height;

                // ambil gambar dari video dan masukan
                // ke dalam canvas
                canvas
                    .getContext("2d")
                    .drawImage(video, 0, 0, canvas.width, canvas.height);
                const image_data_url = canvas.toDataURL("image/jpeg");

                const blobRes = await fetch(image_data_url);

                const blob = await blobRes.blob();

                const image = await faceapi.bufferToImage(blob);
                const imageDetections = await faceapi
                    .detectSingleFace(image)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                console.log(imageDetections);
                if (!imageDetections) {
                    return alert("Wajah Tidak Ditemukan");
                }

                const imageMatcher = await faceapi.fetchImage(
                    userImage
                );
                const imageMatcherDetections = await faceapi
                    .detectSingleFace(imageMatcher)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                const faceMatcher = new faceapi.FaceMatcher(
                    imageMatcherDetections
                );
                const result = faceMatcher.findBestMatch(
                    imageDetections.descriptor,
                    0.55
                );

                const isValid = result.label !== "unknown";
                return isValid;
            }
        });
    </script>
@endsection
