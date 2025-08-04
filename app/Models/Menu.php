<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;
    protected $table = "menu";

    protected $fillable = [
        'name',
        'type',
        'slug',
        'parent_id',
        'url',
        'icon',
        'sort',
        'comment'
    ];

    public function parent_data()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public static function getMenusForRole($role_id)
    {
        // 撈出有權限的 menu_id
        $menuIds = DB::table('job_menu')
            ->where('job_id', $role_id)
            ->pluck('menu_id')
            ->toArray();

        // 撈出這些 menu（父子都在裡面，自己在 blade 做層級組合）
        return self::whereIn('id', $menuIds)
            ->orderBy('sort', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
