<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <meta http-equiv="refresh" content="10"/> -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QrCode</title>

    <!-- Customized Bootstrap Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style type="text/css">
        .container {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .wrapper {
            width: 420px;
        }

        video {
            border: 1px solid #bdc3c7;
            box-shadow: 1px 1px 2px #95a5a6;
            width: 420px;
            height: 340px;
        }

        .button-group {
            text-align: center;
            margin: 10px;
        }

        #response {
            padding: 10px 0px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3 mb-2" href="#">
                <img src="{{ asset('kemenag.png') }}" alt="Logo 1" height="40">
                <img src="{{ asset('pusaka.png') }}" alt="Logo 2" height="40">
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('presensi.index') }}?sesi=Pembukaan">Pembukaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('presensi.index') }}?sesi=Sesi%20Siang">Sesi Siang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('presensi.index') }}?sesi=Sesi%20Malam">Sesi Malam</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="wrapper">
            <video id="previewKamera"></video>

            <div class="button-group">
                <div style="margin-bottom:10px;">
                    <button class="btn btn-primary" id="openCamera" type="button">Open</button>
                    <button class="btn btn-outline-dark" id="closeCamera" type="button">Close</button>
                </div>
                <select class="form-select" id="pilihKamera"></select>
            </div>

            <div class="form-group mt-4 mb-2">
                <label for="sesi">Sesi</label>
                <input type="text" class="form-control" id="sesi" disabled
                    value="{{ request()->get('sesi', 'Pembukaan') }}">
            </div>

            <div class="form-group position-relative">
                <label for="nip">NIP</label>
                <input type="text" class="form-control" id="nip" placeholder="Scan / Ketik NIP atau Nama">
                <div id="suggestion-box" class="list-group position-absolute w-100" style="z-index:1000;"></div>
            </div>

            <div id="response"></div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script>
        let selectedDeviceId = null;
        const codeReader = new ZXing.BrowserMultiFormatReader();
        const sourceSelect = $("#pilihKamera");

        function submitQRCode(nip) {
            $.ajax({
                url: "{{ route('presensi.store') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sesi: $('#sesi').val(),
                    nip: nip
                },
                success: function(response) {
                    $('#nip').val("");
                    $('#response').html(
                        `<div class="alert alert-success" role="alert">${response.message}</div>`
                    );
                },
                error: function(xhr, status, error) {
                    $('#nip').val("");
                    if (status < 500) {
                        const json = $.parseJSON(xhr.responseText)
                        $('#response').html(
                            `<div class="alert alert-danger" role="alert">${json.message}</div>`);
                    } else {
                        const json = $.parseJSON(xhr.responseText)
                        $('#response').html(
                            `<div class="alert alert-danger" role="alert">${json.message}</div>`
                        );
                    }
                }
            });
        }

        function initScanner() {
            codeReader
                .listVideoInputDevices()
                .then(videoInputDevices => {
                    videoInputDevices.forEach(device =>
                        console.log(`${device.label}, ${device.deviceId}`)
                    );

                    if (videoInputDevices.length > 0) {

                        if (selectedDeviceId == null) {
                            if (videoInputDevices.length > 1) {
                                selectedDeviceId = videoInputDevices[1].deviceId
                            } else {
                                selectedDeviceId = videoInputDevices[0].deviceId
                            }
                        }


                        if (videoInputDevices.length >= 1) {
                            sourceSelect.html('');
                            videoInputDevices.forEach((element) => {
                                const sourceOption = document.createElement('option')
                                sourceOption.text = element.label
                                sourceOption.value = element.deviceId
                                if (element.deviceId == selectedDeviceId) {
                                    sourceOption.selected = 'selected';
                                }
                                sourceSelect.append(sourceOption)
                            })

                        }

                        codeReader
                            .decodeOnceFromVideoDevice(selectedDeviceId, 'previewKamera')
                            .then(result => {
                                $("#nip").val(result.text).trigger('change');
                                submitQRCode(result.text);
                                if (codeReader) {
                                    codeReader.reset();
                                    initScanner();
                                }
                            })
                            .catch(err => console.error(err));

                    } else {
                        alert("Camera not found!")
                    }
                })
                .catch(err => console.error(err));
        }

        $(document).on('change', '#pilihKamera', function() {
            selectedDeviceId = $(this).val();
            if (codeReader) {
                codeReader.reset()
                initScanner()
            }
        });

        if (navigator.mediaDevices) {
            initScanner();
        } else {
            alert('Cannot access camera.');
        }


        $("#openCamera").click(function() {
            initScanner()
        });

        $("#closeCamera").click(function() {
            codeReader.reset();
        });

        $("#nip").change(function() {
            const nip = $(this).val();
            // submitQRCode(nip);
        });

        $("#nip").on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                const nip = $(this).val();
                submitQRCode(nip);
            }
        });
    </script>

    <script>
        const input = document.getElementById('nip');
        const suggestionBox = document.getElementById('suggestion-box');

        let timeout = null;

        input.addEventListener('keyup', function() {

            clearTimeout(timeout);

            let query = this.value;
            console.log(query);

            timeout = setTimeout(() => {

                if (query.length < 2) {
                    suggestionBox.innerHTML = '';
                    return;
                }

                fetch(`{{ route('peserta.autocomplete') }}?nip=${query}`)
                    .then(res => res.json())
                    .then(data => {

                        console.log(data);
                        suggestionBox.innerHTML = '';

                        if (data.length === 0) {
                            suggestionBox.innerHTML =
                                `<div class="list-group-item">Tidak ditemukan</div>`;
                            return;
                        }

                        data.forEach(item => {
                            suggestionBox.innerHTML += `
                        <a href="#" class="list-group-item list-group-item-action"
                           data-nip="${item.nip}">
                           ${item.nip} - ${item.nama}
                        </a>
                    `;
                        });
                    });

            }, 300); // delay 300ms

        });

        suggestionBox.addEventListener('click', function(e) {
            if (e.target.classList.contains('list-group-item')) {
                input.value = e.target.dataset.nip;
                suggestionBox.innerHTML = '';
            }
            // autofocus to input
            input.focus();
        });
    </script>
</body>

</html>
