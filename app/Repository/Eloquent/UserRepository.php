<?php
/**
 * Created by PhpStorm.
 * User: WF3New
 * Date: 13.04.2021
 * Time: 17:31
 */

namespace App\Repository\Eloquent;


use App\Repository\UserRepositoryInterface;
use App\User;

class UserRepository implements UserRepositoryInterface
{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getOne($id){
        return $this->user->find($id);
    }

}
