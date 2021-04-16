<?php

namespace App\Repository;

interface ProductRepositoryInterface
{

    public function all($attributes);

    public function getCount($attributes);

    public function getOne($id);

    public function create($attributes);

    public function update($id, $attributes);

    public function delete($id);

    public function bidToProduct($attributes);

    public function enableAutobid($attributes);

    public function getProductAutobids($product_id);

    public function getUserAutobids($user_id);

    public function getProductBids($product_id, $user_id);

    public function getProductMaxBid($product_id);
}
