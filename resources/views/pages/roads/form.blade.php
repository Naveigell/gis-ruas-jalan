@extends('layouts.app')

@push('style')
    <style>
        textarea {
            height: 200px;
            resize: none;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
          integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
          crossorigin=""/>
    <style>
        #mapid { min-height: 500px; }
        .leaflet-control-container .leaflet-routing-container-hide {
            display: none;
        }
    </style>
@endpush

@section('section')
    <div class="container">
        <form action="{{ route('roads.store') }}" method="post">
            @csrf
            <h2>Buat Jalan</h2>
            <div class="mb-3">
                <label for="province" class="form-label">Provinsi</label>
                <select name="provinsi" id="province" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="regency" class="form-label">Kabupaten</label>
                <select name="kabupaten" id="regency" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="subdistrict" class="form-label">Kecamatan</label>
                <select name="kecamatan" id="subdistrict" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="village" class="form-label">Desa</label>
                <select name="desa_id" id="village" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="existing-road" class="form-label">Eksisting Jalan</label>
                <select name="eksisting_id" id="existing-road" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                    @foreach($existingRoads as $existingRoad)
                        <option value="{{ $existingRoad->id }}">{{ $existingRoad->eksisting }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="condition-road" class="form-label">Kondisi Jalan</label>
                <select name="kondisi_id" id="condition-road" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                    @foreach($roadConditions as $roadCondition)
                        <option value="{{ $roadCondition->id }}">{{ $roadCondition->kondisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="type-road" class="form-label">Jenis Jalan</label>
                <select name="jenisjalan_id" id="type-road" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                    @foreach($roadTypes as $roadType)
                        <option value="{{ $roadType->id }}">{{ $roadType->jenisjalan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="road-code" class="form-label">Kode Ruas</label>
                <input type="text" name="kode_ruas" class="form-control" id="road-code" placeholder="Cth: R1">
            </div>
            <div class="mb-3">
                <label for="road-name" class="form-label">Nama Ruas</label>
                <input type="text" name="nama_ruas" class="form-control" id="road-name" placeholder="Cth: 10 - 12">
            </div>
            <div class="mb-3">
                <label for="road-length" class="form-label">Panjang Ruas</label>
                <input type="number" name="panjang" class="form-control" id="road-length" placeholder="Cth: 105.333 (dalam meter)">
            </div>
            <div class="mb-3">
                <label for="width-length" class="form-label">Lebar Ruas</label>
                <input type="number" name="lebar" class="form-control" id="width-length" placeholder="Cth: 2 (dalam meter)">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="description" rows="3"></textarea>
            </div>
            <input type="hidden" name="paths" id="hidden-paths">
            <div class="container mb-5" id="mapid"></div>
            <input type="hidden" class="" id="hidden-province" value='@json($provinces)'>
            <button class="btn btn-primary" type="submit">Simpan</button>
            <br><br>
        </form>
    </div>
@endsection

@push('script')
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
            integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
            crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <script src="{{ asset('assets/js/PolylineUtil.encoded.js') }}"></script>
    <script>
        /**
         * Add marker
         *
         * @param latitude {Number}
         * @param longitude {Number}
         */
        function pushMarker(latitude, longitude) {
            var marker = L.marker([latitude, longitude]).addTo(map);

            markers.push({
                marker: marker,
                latitude: latitude,
                longitude: longitude
            });
        }
    </script>
    <script>
        // add marker and set current default view into this lat lang
        var map = L.map('mapid').setView([-8.409518, 115.188919], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        var markers = [];
        var control;

        map.on('click', function(e) {

            let latitude = e.latlng.lat.toString().substring(0, 15);
            let longitude = e.latlng.lng.toString().substring(0, 15);

            if (markers.length < 2) {
                // push marker if markers length is less than 2
                pushMarker(latitude, longitude);

                if (markers.length === 2) {
                    // if markers already 2, then add routing between markers
                    control = L.Routing.control({
                        waypoints: [
                            L.latLng(markers[0].latitude, markers[0].longitude),
                            L.latLng(markers[1].latitude, markers[1].longitude)
                        ],
                        show: false
                    });

                    control.addTo(map);

                    control.on('routesfound', function (e) {
                        var coordinates = e.routes[0].coordinates;

                        $('#hidden-paths').val(L.PolylineUtil.encode(coordinates));
                    })
                }
            } else {

                // remove all component that we bind into map
                map.removeControl(control);
                map.removeLayer(markers[0].marker);
                map.removeLayer(markers[1].marker);
                $('#hidden-paths').val('');

                markers = [];

                pushMarker(latitude, longitude);
            }
        });
    </script>
    <script>
        /**
         * A function to insert data into select option
         *
         * @param component {HTMLElement} an element that must be bind with data
         * @param data {Array} an array of data
         * @param text {String} the text that will be shown to the html, example: desa name or regency name
         * @param dataAttribute {String} the attribute that will bind into data-attr
         */
        function insertDataIntoSelectOptionComponent(component, data, text, dataAttribute = null) {
            // create data attribute to bind into option
            var dataText = 'data-' + dataAttribute;

            for (var datum of data) {
                var options = {
                    value: datum.id,
                    text: datum[text], // get the text
                };

                if (dataAttribute) {
                    // stringify data attribute inside datum if data text is not null
                    options[dataText] = JSON.stringify(datum[dataAttribute]);
                }

                var option = $('<option>', options);

                component.append(option);
            }
        }

    </script>
    <script>
        var provinceComponent    = $('#province');
        var regencyComponent     = $('#regency');
        var subdistrictComponent = $('#subdistrict');
        var villageComponent     = $('#village');

        var data = JSON.parse($('#hidden-province').val());

        // insert data into province (provinsi)
        insertDataIntoSelectOptionComponent(provinceComponent, data, 'provinsi', 'regencies');

        provinceComponent.on('change', function (e) {
            var target    = $(e.target).find('option:selected');
            var regencies = target.data('regencies');

            // insert data into regency (kabupaten)
            insertDataIntoSelectOptionComponent(regencyComponent, regencies, 'kabupaten', 'subdistricts');
        });

        regencyComponent.on('change', function (e) {
            var target       = $(e.target).find('option:selected');
            var subDistricts = target.data('subdistricts');

            // insert data into subdistrict (kecamatan)
            insertDataIntoSelectOptionComponent(subdistrictComponent, subDistricts, 'kecamatan', 'villages');
        });

        subdistrictComponent.on('change', function (e) {
            var target   = $(e.target).find('option:selected');
            var villages = target.data('villages');

            // insert data into village (desa)
            insertDataIntoSelectOptionComponent(villageComponent, villages, 'desa');
        });

    </script>
@endpush
