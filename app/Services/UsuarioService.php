<?php


/**
 * Created by PhpStorm.
 * User: lblanco
 * Date: 08/03/19
 * Time: 17:03
 */

namespace App\services;

use App\services\PromiseService;


class UserService
{


    private $userServer;

    public function __construct()
    {
        $this->userServer = new PromiseService('https://jsonplaceholder.typicode.com');

    }

    public function getById($id)
    {
        return $this->userServer->get('find/' . $id);
    }


}