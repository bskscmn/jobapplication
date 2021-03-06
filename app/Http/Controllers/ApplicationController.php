<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use DateTime;

class ApplicationController extends Controller
{
    public function index(Request $request) {

        $user = auth()->user();

        if( $request['search'] != null) {

            $q = $request['search'];

            $apps = Application::where('user_id', $user->id)
                ->where('company','LIKE','%'.$q.'%')
                ->orWhere('contactPerson','LIKE','%'.$q.'%')
                ->orWhere('email','LIKE','%'.$q.'%')
                ->orWhere('phone','LIKE','%'.$q.'%')
                ->orWhere('post_title','LIKE','%'.$q.'%')
                ->orWhere('location','LIKE','%'.$q.'%')
                ->orWhere('description','LIKE','%'.$q.'%')
                ->orderBy('id', 'DESC')->with('condition')->paginate(5)
                ->withQueryString();
            return Inertia::render('Applications/Index',['apps' => $apps, 'search' => $q]);
        }else{
            $tab = $request['tab'];

            if( $tab == 'applied'  ) {
            $apps = Application::where('user_id', $user->id)->where('condition_id', '!=', 1)->orderBy('app_date', 'DESC')->with('condition')->paginate(5)->withQueryString();
            return Inertia::render('Applications/Index',['apps' => $apps, 'tab' => $tab]);
            }
            if( $tab == 'notApplied'  ) {
            $apps = Application::where('user_id', $user->id)->where('condition_id', 1)->orderBy('id', 'DESC')->with('condition')->paginate(5)->withQueryString();
            return Inertia::render('Applications/Index',['apps' => $apps, 'tab' => $tab]);
            }
            if( $tab == null  ) {
            $apps = Application::where('user_id', $user->id)->orderBy('id', 'DESC')->with('condition')->paginate(5)->withQueryString();
            return Inertia::render('Applications/Index',['apps' => $apps, 'tab' => $tab]);
            }
        }
        	
    }

    public function show(int $id) {

    	$app = Application::where('id', $id)->with('condition')->first();

    	return Inertia::render('Applications/Show',[
    		'app' => $app
    	]);
    }

    public function create() {

    	return Inertia::render('Applications/Create',[]);
    }

    public function store(Request $request)
    {
        
    	$validator = Validator::make($request->all(), [
            'company' => 'required|max:255',
            'post_title' => 'required',
            'app_date'  => 'required_unless:condition_id,1'
        ])->validate();

    	$user = auth()->user();
        $request['user_id'] = $user->id;

        if($request['app_date']){
	        $d = DateTime::createFromFormat('d-m-Y H:i', $request['app_date']);
	        $request['app_date'] = $d->format('Y-m-d H:i:s');
	    }
        Application::create($request->all());

        return Redirect::route('applications.index')->with('success', 'New application is added succesfully.');
    }

    public function edit(int $id,Request $request) {

    	$app = Application::where('id', $id)->with('condition')->first();

    	return Inertia::render('Applications/Edit',[
    		'app' => $app,
    	]);
    }

    public function update(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'company' => 'required|max:255',
            'post_title' => 'required',
            'app_date'  => 'required_unless:condition_id,1'
        ])->validate();
    	
    	if($request['app_date']){
            $d = DateTime::createFromFormat('d-m-Y H:i', $request['app_date']);
            $request['app_date'] = $d->format('Y-m-d H:i:s');
        }
        
        $application = Application::where('id', $request->id)->first();
        $application->update($request->all());


        return Redirect::route('application.show',$request->id)->with('success', 'Application updated.');
    }


    public function destroy(Request $request)
    {
        $application = Application::find($request->id);
        $application->delete();

        return redirect('/applications')->with('success', 'Application deleted successfully');
    }
}
