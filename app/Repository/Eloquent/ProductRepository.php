<?php

namespace App\Repository\Eloquent;


use App\BidBot;
use App\Product;
use App\ProductBid;
use App\Repository\ProductRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\Auth;

class ProductRepository implements ProductRepositoryInterface
{

    private $product;
    private $productBid;
    private $bidBot;
    private $user;

    public function __construct(Product $product, ProductBid $productBid, BidBot $bidBot, User $user)
    {
        $this->product = $product;
        $this->productBid = $productBid;
        $this->bidBot = $bidBot;
        $this->user = $user;
    }

    public function all($data){
        $offset = $data['offset'];
        $limit = $data['limit'];
        $query = $this->product->query();
        $query = $this->getAllQuery($query, $data);
        $products = $query->with('bid')->orderBy('min_price', $data['order'])->offset($offset)->limit($limit)->get();
        return $products;
    }

    public function getCount($data){
        $query = $this->product->query();
        $query = $this->getAllQuery($query, $data);
        return $query->count();
    }

    public function getAllQuery($query, $data){
        if($data['keyword']) $query = $query->where('name', 'like', '%'.$data['keyword'].'%')
            ->orWhere('description', 'like', '%'.$data['keyword'].'%');
        return $query;
    }

    public function getOne($id){
        $user_id = Auth::user()->id;
        return $this->product->with('bid')->with(['autobid'=>function($query)use ($user_id){
                $query->where('user_id', $user_id);
            }])->find($id);
    }

    public function create($attributes){
        return $this->product->create($attributes);
    }

    public function update($id, $data){
        $product = $this->product->find($id);
        return $product->update($data);
    }

    public function delete($id){
        $product = $this->product->find($id);
        $product->delete();
    }

    public function bidToProduct($data){
        return $this->productBid->create($data);
    }

    public function enableAutobid($data){
        $this->bidBot->create($data);
    }

    public function getProductAutobids($product_id){
        return $this->bidBot->where('product_id', $product_id)->get();
    }

    public function getUserAutobids($user_id){
        return $this->bidBot->where('user_id', $user_id)->get();
    }

    public function getProductBids($product_id, $user_id){
        return $this->productBid->where([['product_id', $product_id], ['user_id', $user_id]])->orderBy('id', 'desc')->first();
    }

    public function getProductMaxBid($product_id){
        return $this->productBid->where('product_id',$product_id)->orderBy('id', 'desc')->first();
    }

}
