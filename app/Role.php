<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    use Notifiable;
//    use HasPermissions;
    use HasRoles;
    protected $table = 'roles';

    protected $fillable = ['name','slug','type'];

    protected $guard_name = 'web';

    public function users(){
        return $this->hasMany('App\UserHasRole','role_id');
    }

    use Sluggable;
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
