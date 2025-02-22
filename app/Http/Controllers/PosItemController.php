<?php

namespace App\Http\Controllers;

use App\Http\Requests\PosItemRequest;
use App\Models\BeerBrand;
use App\Models\DeviceLine;
use App\Models\Devices;
use App\Models\Location;
use App\Models\PosItem;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PosItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $data = DeviceLine::orderBy("Line")->get();
        $locations = Location::orderBy("LocationName")->get();
        $lastLocation = Location::latest()->first();
        $devices = Devices::where('LocationID', $lastLocation->LocationID)->orderBy("Name")->get();
        $locationsUsers = LocationService::LocationDisplay();

        return view('PosItem.index', compact('locations', 'devices', 'locationsUsers'));
    }

    public function getPosItems(Request $request): JsonResponse
    {
        $locationId = $request->input('location_id');
        $posItems = PosItem::select(['POSItemsID', 'AccountID', 'LocationID', 'bb.Brand as BeerBrandID', 'Ounces', 'ItemName', 'ItemDesc', 'ItemNum'])->leftJoin('BeerBrands as bb', 'bb.BeerBrandsID', '=', 'POSItems.BeerBrandID');
        if (!empty($locationId)) {
            $posItems = $posItems->where('LocationID', $locationId);
        }

        $posItems = $posItems->get();

        return response()->json($posItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PosItemRequest $request): RedirectResponse
    {
        PosItem::create($request->validated());

        return Redirect::route('pos-items.index')
            ->with('success', 'PosItem created successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $posItem = new PosItem();

        return view('PosItem.create', compact('posItem'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        dd('show');
        $posItem = PosItem::where('POSItemsID', $id)->first();

        return view('PosItem.show', compact('posItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $posItem = PosItem::where('POSItemsID', $id)->first();
        $allBrands = BeerBrand::all()->sortBy('Brand');

        return view('PosItem.edit', compact('posItem', 'allBrands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PosItemRequest $request, PosItem $posItem): RedirectResponse
    {
        dd($posItem->toArray());
        $posItem->update($request->validated());

        return Redirect::route('PosItem.index')
            ->with('success', 'PosItem updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        PosItem::where('POSItemsID', $id)->delete();

        return Redirect::route('PosItem.index')
            ->with('success', 'PosItem deleted successfully');
    }
}
