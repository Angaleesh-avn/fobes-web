<?php

namespace Modules\Plan\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Language\Entities\Language;
use Modules\Plan\Entities\Plan;
use Modules\Plan\Entities\PlanDescription;
use Modules\Plan\Entities\PlanResdex;

class PlanResdexController extends Controller
{
    use ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        // return 2;
        abort_if(! userCan('plan.view'), 403);

        $plans = PlanResdex::get();

        $current_language = currentLanguage();
        $current_language_code = $current_language ? $current_language->code : config('templatecookie.default_language');

        if ($current_language) {
            $plans->load(['descriptions' => function ($q) use ($current_language_code) {
                $q->where('locale', $current_language_code);
            }]);
        }

        return view('plan::plan_resdex.index', compact('plans', 'current_language','current_language_code'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        abort_if(! userCan('plan.create'), 403);

        $app_languages = Language::latest()->get();

        return view('plan::plan_resdex.create', compact('app_languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        // return $request->all();
        abort_if(! userCan('plan.create'), 403);
        // validation
        $validate_array = [];
        $validate_array['label'] = 'required|string|unique:plans,label';
        $validate_array['price'] = 'required|numeric';
        $validate_array['credits'] = 'required|numeric';
        $validate_array['frontend_show'] = 'required|numeric';
        $validate_array['plan_valid_days'] = 'required|numeric';
        $this->validate($request, $validate_array);

        $plan = PlanResdex::create([
            'label' => $request->label,
            'price' => $request->price,
            'credits' => $request->credits,
            'frontend_show' => $request->frontend_show,
            'strikethrough_price' => $request->strikethrough_price,
            'plan_valid_days' => $request->plan_valid_days,
        ]);

        flashSuccess(__('plan_created_successfully'));

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit(PlanResdex $plan_resdex)
    {
        // return $plan_resdex;
        abort_if(! userCan('plan.update'), 403);

        $app_languages = Language::latest()->get();
        // $plan->load('descriptions');
        $plan = $plan_resdex;

        return view('plan::plan_resdex.edit', compact('plan', 'app_languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Renderable
     */
    public function update(Request $request, PlanResdex $plan_resdex)
    {
        // return $plan_resdex;
        abort_if(! userCan('plan.update'), 403);

        // validation
        $validate_array = [];
        $validate_array['label'] = "required|string|unique:plans,label,$plan_resdex->id";
        $validate_array['price'] = 'required|numeric';
        $validate_array['credits'] = 'required|numeric';
        $validate_array['frontend_show'] = 'required|numeric';
        $validate_array['plan_valid_days'] = 'required|numeric';
        $this->validate($request, $validate_array);

        $plan_resdex->update([
            'label' => $request->label,
            'price' => $request->price,
            'credits' => $request->credits,
            'frontend_show' => $request->frontend_show,
            'strikethrough_price' => $request->strikethrough_price,
            'plan_valid_days' => $request->plan_valid_days,
        ]);

        flashSuccess(__('plan_updated_successfully'));

        return redirect()->route('module.plan_resdex.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(PlanResdex $plan_resdex)
    {
        abort_if(! userCan('plan.delete'), 403);

        $plan_resdex->delete();

        flashSuccess(__('plan_deleted_successfully'));

        return back();
    }

}
