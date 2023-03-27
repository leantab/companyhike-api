<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class CommunityController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:enter_admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coms = Community::paginate(15);
        return view('admin.communities', ['communities' => $coms]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('admin.community_create', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:communities,name',
            'admin' => 'required|exists:users,id',
            'file' => 'required'
        ]);

        $community = Community::create([
            'name' => $request->name,
        ]);

        $user = User::findOrFail($request->admin);
        //$user->allowAccessToCommunity($community);
        $comus = CommunityUser::create([
            'user_id' => $user->id,
            'community_id' => $community->id,
            'role_id' => 1,
        ]);

        if ($request->file('file') != null) {
            $img = Image::make($request->file('file'))->fit(250, 250)->save(storage_path('app/logos/' . $community->id . '.jpg'));
        }

        return redirect('/admin/communities/' . $community->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Community  $community
     * @return \Illuminate\Http\Response
     */
    public function show(Community $community)
    {
        return view('admin.communities-view', ['community' => $community]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Community  $community
     * @return \Illuminate\Http\Response
     */
    public function edit(Community $community)
    {
        return view('admin.communities-edit', [
            'segment' => $community,
            'users' => $community->admins,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Community  $community
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Community $community)
    {
        $this->validate($request, [
            'name' => 'required',
            'admin' => 'required|exists:users,id',
        ]);

        //$com = Community::findOrFail($community->id);

        $user = User::findOrFail($request->admin);

        if (!$user->isSegmentAdmin($community->id)) {

            if (!$community->users->contains($user)) {
                dd('NO encontró al usuario');
                DB::table('segment_user')->insert([
                    'segment_id' => $community->id,
                    'user_id' => $user->id,
                    'role_id' => 1
                ]);
                $user->allowAccessToSegment($community);
            } else {
                DB::table('segment_user')->where([['user_id', $user->id], ['segment_id', $community->id]])->update(['role_id' => 1]);
            }
        }

        if ($request->file('file') != null) {
            if (file_exists(storage_path('app/logos/' . $community->id . '.jpg'))) {
                unlink(storage_path('app/logos/' . $community->id . '.jpg'));
            }
            $img = Image::make($request->file('file'))->fit(250, 250)->save(storage_path('app/logos/' . $community->id . '.jpg'));
        }

        return redirect('/admin/segments/' . $community->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Community  $community
     * @return \Illuminate\Http\Response
     */
    public function destroy(Community $community)
    {
        abort(500);
    }
}
