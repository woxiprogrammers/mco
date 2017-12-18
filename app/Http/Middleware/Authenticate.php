<?php

namespace App\Http\Middleware;

use App\ProjectSite;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try{
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $globalProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                    ->where('projects.is_active', true)
                    ->select('projects.name as project_name','project_sites.id as project_site_id','project_sites.name as project_site_name')
                    ->orderBy('project_site_id','desc')
                    ->get();
                if(Session::has('global_project_site')){
                    $selectGlobalProjectSite = Session::get('global_project_site');
                }else{
                    $selectGlobalProjectSite = $globalProjectSites[0]->project_site_id;
                    Session::put('global_project_site',$selectGlobalProjectSite);
                }
                View::share(compact('user','globalProjectSites','selectGlobalProjectSite'));
                return $next($request);
            }else{
                return redirect('/');
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Authenticate middleware',
                'exception' => $e->getMessage()
            ];
            dd($e->getMessage());
            Log::critical(json_encode($data));
        }
    }
}
