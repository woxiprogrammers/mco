<?php

namespace App\Http\Middleware;

use App\ProjectSite;
use App\UserProjectSiteRelation;
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
                $globalProjectSite = $selectGlobalProjectSite = array();
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                    $globalProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                        ->where('projects.is_active', true)
                        ->select('projects.name as project_name','project_sites.id as project_site_id','project_sites.name as project_site_name')
                        ->orderBy('project_site_id','desc')
                        ->get();
                }else{
                    $globalProjectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                        ->join('user_project_site_relation','user_project_site_relation.project_site_id','=','project_sites.id')
                        ->where('projects.is_active', true)
                        ->where('user_project_site_relation.user_id', $user->id)
                        ->select('projects.name as project_name','project_sites.id as project_site_id','project_sites.name as project_site_name')
                        ->orderBy('project_site_id','desc')
                        ->get();
                }
                if(empty($globalProjectSites)){
                    if(Session::has('global_project_site')){
                        if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'){
                            $selectGlobalProjectSite = Session::get('global_project_site');
                        }else{
                            $selectGlobalProjectSite = Session::get('global_project_site');
                            $projectSiteRelationExists = UserProjectSiteRelation::where('user_id',$user->id)->where('project_site_id',$selectGlobalProjectSite)->first();
                            if($projectSiteRelationExists == null){
                                $selectGlobalProjectSite = $globalProjectSites[0]->project_site_id;
                                Session::put('global_project_site',$selectGlobalProjectSite);
                            }
                        }
                    }else{
                        $selectGlobalProjectSite = $globalProjectSites[0]->project_site_id;
                        Session::put('global_project_site',$selectGlobalProjectSite);
                    }


                $globalProjectSite = ProjectSite::findOrFail(Session::get('global_project_site'));
                }
                View::share(compact('user','globalProjectSites','selectGlobalProjectSite','globalProjectSite'));
                return $next($request);
            }else{
                return redirect('/');
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Authenticate middleware',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
