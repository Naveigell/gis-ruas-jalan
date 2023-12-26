@extends('layouts.app')

@push('style')
    <style>
        textarea {
            height: 200px;
            resize: none;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.min.css">
    <style>
        #mapid { min-height: 500px; }
        .leaflet-control-container .leaflet-routing-container-hide {
            display: none;
        }
    </style>
@endpush

@section('section')
    <div class="container">
        <form action="{{ @$road ? route('roads.update', request()->route('road')) : route('roads.store') }}" method="post">
            @csrf
            @method(@$road ? 'PUT' : 'POST')
            <h2>{{ @$road ? 'Ubah' : 'Buat' }} Jalan</h2>
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
                        <option @if(@$road && $road->eksisting_id == $existingRoad->id) selected @endif value="{{ $existingRoad->id }}">{{ $existingRoad->eksisting }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="condition-road" class="form-label">Kondisi Jalan</label>
                <select name="kondisi_id" id="condition-road" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                    @foreach($roadConditions as $roadCondition)
                        <option @if(@$road && $road->kondisi_id == $roadCondition->id) selected @endif value="{{ $roadCondition->id }}">{{ $roadCondition->kondisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="type-road" class="form-label">Jenis Jalan</label>
                <select name="jenisjalan_id" id="type-road" class="form-control">
                    <option value="">-- Nothing Selected --</option>
                    @foreach($roadTypes as $roadType)
                        <option @if(@$road && $road->jenisjalan_id == $roadType->id) selected @endif value="{{ $roadType->id }}">{{ $roadType->jenisjalan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="road-code" class="form-label">Kode Ruas</label>
                <input type="text" name="kode_ruas" class="form-control" id="road-code" placeholder="Cth: R1" value="{{ @$road ? $road->kode_ruas : '' }}">
            </div>
            <div class="mb-3">
                <label for="road-name" class="form-label">Nama Ruas</label>
                <input type="text" name="nama_ruas" class="form-control" id="road-name" placeholder="Cth: 10 - 12" value="{{ @$road ? $road->nama_ruas : '' }}">
            </div>
            <div class="mb-3">
                <label for="road-length" class="form-label">Panjang Ruas</label>
                <input type="number" name="panjang" class="form-control" id="road-length" placeholder="Cth: 105.333 (dalam meter)" value="{{ @$road ? $road->panjang : '' }}">
            </div>
            <div class="mb-3">
                <label for="width-length" class="form-label">Lebar Ruas</label>
                <input type="number" name="lebar" class="form-control" id="width-length" placeholder="Cth: 2 (dalam meter)" value="{{ @$road ? $road->lebar : '' }}">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="description" rows="3">{{ @$road ? $road->keterangan : '' }}</textarea>
            </div>
            <input type="hidden" name="paths" id="hidden-paths" value="{{ @$road ? $road->paths : '' }}">
            <div class="container mb-5" id="mapid"></div>
            <input type="hidden" class="" id="hidden-province" value='@json($provinces)'>
            <button class="btn btn-primary" type="submit">Simpan</button>
            <br><br>
        </form>
    </div>
@endsection

@push('script')
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.min.js"></script>
    <script src="{{ asset('assets/js/PolylineUtil.encoded.js') }}"></script>
    <script>
        var lastLayer = null;
    </script>
    <script>
        // add marker and set current default view into this lat lang
        var map = L.map('mapid').setView([-8.409518, 115.188919], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        var editableLayers = new L.FeatureGroup();

        map.addLayer(editableLayers);

        var options = {
            position: 'topright',
            draw: {
                polyline: {
                    shapeOptions: {
                        color: 'red',
                        weight: 3,
                        opacity: 0.5,
                        smoothFactor: 1
                    }
                },
                // remove all shape except polyline
                circle: false,
                circlemarker: false,
                marker: false,
                rectangle: false,
                polygon: false,
            },
            edit: false
        };

        // Initialise the draw control and pass it the FeatureGroup of editable layers
        var drawControl = new L.Control.Draw(options);
        map.addControl(drawControl);

        var editableLayers = new L.FeatureGroup();
        map.addLayer(editableLayers);

        map.on('draw:created', function(e) {
            var layer = e.layer;

            // remove last layer
            // if user create a new line, we remove latest layer, to make only one polyline inside map
            if (lastLayer) {
                map.removeLayer(lastLayer);
                lastLayer = layer;
            }

            $('#hidden-paths').val(L.PolylineUtil.encode(layer._latlngs));

            editableLayers.addLayer(layer);
        });
    </script>

    @if(@$road)
        <script>
            // create a polyline given by api
            var coordinates = [];
            var polylines = L.PolylineUtil.decode($('#hidden-paths').val());

            for (var polyline of polylines) {
                coordinates.push(new L.LatLng(polyline[0], polyline[1]));
            }

            // create default polyline and initialize it into last layer
            var Polyline = new L.Polyline(coordinates, {
                color: 'red',
                weight: 3,
                opacity: 0.5,
                smoothFactor: 1
            });

            map.addLayer(Polyline);

            lastLayer = Polyline;
        </script>
    @endif
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

        /**
         * Remove all option from element
         *
         * @param element {HTMLElement}
         */
        function removeAllOptionFromElement(element) {
            $(element).find('option').each(function (index, element) {
                if ($(element).val() === '') {
                    return;
                }

                $(this).remove();
            });
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

            removeAllOptionFromElement(subdistrictComponent);

            // insert data into subdistrict (kecamatan)
            insertDataIntoSelectOptionComponent(subdistrictComponent, subDistricts, 'kecamatan', 'villages');
        });

        subdistrictComponent.on('change', function (e) {
            var target   = $(e.target).find('option:selected');
            var villages = target.data('villages');

            removeAllOptionFromElement(villageComponent);

            // insert data into village (desa)
            insertDataIntoSelectOptionComponent(villageComponent, villages, 'desa');
        });

    </script>
    @if(@$road)
        <input type="hidden" id="hidden-road-id" value="{{ $province->id }}">
        <input type="hidden" id="hidden-regency-id" value="{{ $regency->id }}">
        <input type="hidden" id="hidden-sub-district-id" value="{{ $subDistrict->id }}">
        <input type="hidden" id="hidden-village-id" value="{{ $village->id }}">
        <script>
            // trigger all id for component
            var provinceId    = $('#hidden-road-id').val();
            var regencyId     = $('#hidden-regency-id').val();
            var subDistrictId = $('#hidden-sub-district-id').val();
            var villageId     = $('#hidden-village-id').val();

            provinceComponent.val(provinceId).trigger('change');
            regencyComponent.val(regencyId).trigger('change');
            subdistrictComponent.val(subDistrictId).trigger('change');
            villageComponent.val(villageId).trigger('change');
        </script>
    @endif
@endpush
