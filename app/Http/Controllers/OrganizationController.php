<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Organization;
use App\Models\State;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        //
        $organizations = Organization::with([
            'country',
            'state',
            'city'
        ])->paginate(25);

        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $countries = Country::orderBy('name')->get();

        $states = collect();

        $cities = collect();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        //
        $countries = Country::orderBy('name')->get();

        $states = State::where(
            'country_id',
            $organization->country_id
        )->orderBy('name')->get();

        $cities = City::where(
            'state_id',
            $organization->state_id
        )->orderBy('name')->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        //
    }
}
