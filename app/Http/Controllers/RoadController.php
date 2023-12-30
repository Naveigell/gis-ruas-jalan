<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoadRequest;
use App\Models\ExistingRoad;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Road;
use App\Models\RoadCondition;
use App\Models\RoadType;
use App\Models\Subdistrict;
use App\Models\Village;
use App\Utils\GISHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $roads = Road::hydrate($http->listRoad()['ruasjalan']);

        return view('pages.roads.index', compact('roads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $response     = $http->listProvince();
        $villages     = Village::hydrate($response['desa']);
        $subDistricts = Subdistrict::hydrateWithRelations($response['kecamatan'], [['name' => 'villages', 'foreign_key' => 'kec_id', 'data' => $villages]]);
        $regencies    = Regency::hydrateWithRelations($response['kabupaten'], [['name' => 'subdistricts', 'foreign_key' => 'kab_id', 'data' => $subDistricts]]);
        $provinces    = Province::hydrateWithRelations($response['provinsi'], [['name' => 'regencies', 'foreign_key' => 'prov_id', 'data' => $regencies]]);

        $existingRoads  = ExistingRoad::hydrate($http->listExistingRoad()['eksisting']);
        $roadConditions = RoadCondition::hydrate($http->listRoadCondition()['eksisting']);
        $roadTypes      = RoadType::hydrate($http->listRoadType()['eksisting']);

        $roads = Road::hydrate($http->listRoad()['ruasjalan']);

        // take all paths to draw the road except current road
        $paths = $roads->pluck('paths');

        return view('pages.roads.form', compact('provinces', 'existingRoads', 'roadConditions', 'roadTypes', 'paths'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoadRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(RoadRequest $request)
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $response = $http->createRoad($request->validated());

        return redirect(route('roads.index'))->with('success', 'Berhasil membuat jalan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Road  $road
     * @return \Illuminate\Http\Response
     */
    public function show(Road $road)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $response     = $http->listProvince();
        $villages     = Village::hydrate($response['desa']);
        $subDistricts = Subdistrict::hydrateWithRelations($response['kecamatan'], [['name' => 'villages', 'foreign_key' => 'kec_id', 'data' => $villages]]);
        $regencies    = Regency::hydrateWithRelations($response['kabupaten'], [['name' => 'subdistricts', 'foreign_key' => 'kab_id', 'data' => $subDistricts]]);
        $provinces    = Province::hydrateWithRelations($response['provinsi'], [['name' => 'regencies', 'foreign_key' => 'prov_id', 'data' => $regencies]]);

        $existingRoads  = ExistingRoad::hydrate($http->listExistingRoad()['eksisting']);
        $roadConditions = RoadCondition::hydrate($http->listRoadCondition()['eksisting']);
        $roadTypes      = RoadType::hydrate($http->listRoadType()['eksisting']);

        $roads = Road::hydrate($http->listRoad()['ruasjalan']);
        $road  = new Road($http->getRoadById($id)['ruasjalan']);

        // take all paths to draw the road except current road
        $paths = $roads->whereNotIn('id', [$road->id])->pluck('paths');

        $village     = $villages->where('id', $road->desa_id)->first();
        $subDistrict = $subDistricts->where('id', $village->kec_id)->first();
        $regency     = $regencies->where('id', $subDistrict->kab_id)->first();
        $province    = $provinces->where('id', $regency->prov_id)->first();

        return view('pages.roads.form',
            compact(
                'road', 'provinces', 'existingRoads', 'roadConditions',
                'roadTypes', 'village', 'subDistrict', 'regency', 'province', 'paths'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoadRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(RoadRequest $request, $id)
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $response = $http->updateRoad($id, $request->validated());

        return redirect(route('roads.index'))->with('success', 'Berhasil mengubah jalan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $response = $http->deleteRoad($id);

        return redirect(route('roads.index'))->with('success', 'Berhasil menghapus jalan');
    }
}
