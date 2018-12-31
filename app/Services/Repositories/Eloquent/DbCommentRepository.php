<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/17/2018
 * Time: 12:48 PM
 */

namespace App\Services\Repositories\Eloquent;

use App\Services\Repositories\Interfaces\CommentInterface;
use DB;

class DbCommentRepository extends RepositoriesAbstract implements CommentInterface
{
    public function getAllComment(array $filters)
    {
        $query = $this->getModel()
                ->where(function ($que) use ($filters){
                    if(isset($filters['status'])){
                        $que->where('status', '=', $filters['status']);
                    }else{
                        $que->where('status', '=', 1);
                    }
                    if(isset($filters['images']) && $filters['images'] == 1){
                        $que->whereNotNull('images');
                    }
                })
                ->limit($filters['limit'])
                ->offset($filters['offset'])
                ->orderBy((isset($sortInfo['column']) && !empty($sortInfo['column'])) ? $sortInfo['column'] : 'created_at', (isset($sortInfo['order']) && !empty($sortInfo['order'])) ? $sortInfo['order'] : 'desc' );
        return $query;
    }

    public function getLastCommentbyUserId($userId)
    {
        $result = $this->getModel()
                ->where('userId', '=', $userId)
                ->orderBy('id', 'DESC')
                ->first();
        return $result;
    }

    public function getTopComment(array $filters)
    {
        $query = $this->getModel()
                ->where(function ($que) use ($filters){
                    if(isset($filters['status'])){
                        $que->where('status', '=', $filters['status']);
                    }else{
                        $que->where('status', '=', 1);
                    }
                    if(isset($filters['images']) && $filters['images'] == 1){
                        $que->whereNotNull('images');
                    }
                })
                ->limit($filters['limit'])
                ->offset($filters['offset'])
                ->select('userId', 'fullName', DB::raw('COUNT(id) as countComment'))
                ->groupBy('userId')
                ->orderBy(DB::raw('COUNT(id)'), 'DESC');
        return $query;
    }

}