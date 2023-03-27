<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LicensesController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:enter_admin');
    }

    public function index()
    {
        return view('admin.licenses');
    }

    public function create()
    {
        return view('admin.license_create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'community_id' => ['required', 'integer'],
        ]);

        $license = License::create([
            'community_id' => $request->community_id,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'max_games' => $request->max_games,
            'max_users' => $request->max_users,
            'is_active' => true,
        ]);

        return redirect('/admin/licenses/' . $license->id);
    }

    public function show(License $license)
    {
        return view('admin.users-view')->with(['license' => $license]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\License  $license
     * @return \Illuminate\Http\Response
     */
    public function edit(License $license)
    {
        return view('admin.license-edit')->with('license', $license);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\Response
     */
    public function update(License $license, Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\License  $license
     * @return \Illuminate\Http\Response
     */
    public function destroy(License $license)
    {
        abort(403);
    }

    /**
     * inactivate the specified user.
     *
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\Response
     */
    public function inactivate(License $license)
    {
        $license->is_active = false;
        $license->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $license
     * @return \Illuminate\Http\Response
     */
    public function activate(User $license)
    {
        $license->is_active = true;
        $license->save();
        return back();
    }
}
