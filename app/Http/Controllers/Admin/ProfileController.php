<?php

namespace App\Http\Controllers\Admin;

use App\History;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Profiles;
use Symfony\Component\HttpKernel\Profiler\Profile;
use App\Profile_history;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }

    public function create(Request $request)
    {
        $this->validate($request, Profiles::$rules);

        $profile = new Profiles;
        $form = $request->all();
  
        // データベースに保存する
        $profile->fill($form);
        $profile->save();
        return redirect('admin/profile/create');
    }

    public function edit(Request $request)
    {
        $profile = Profiles::find($request->id);
        if (empty($profile)) {
            abort(404);
        }
        //$profile_histories = Profile_history::find(1);
        return view('admin.profile.edit', ['profile_form' => $profile]);
    }

    public function update(Request $request)
    {
        $this->validate($request, Profiles::$rules);
        // profile Modelからデータを取得する
        $profile = Profiles::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profile_form = $request->all();
        unset($profile_form['_token']);
        unset($profile_form['remove']);

        // 該当するデータを上書きして保存する
        $profile->fill($profile_form)->save();

        $history = new Profile_history;
        $history->profiles_id = $profile->id;
        $history->edited_at = Carbon::now();
        $history->save();
        
        return redirect('admin/profile/edit');
    }
}
