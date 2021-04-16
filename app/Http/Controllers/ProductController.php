<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Http\Requests\AutobidRequest;
use App\Http\Requests\BidRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Repository\ProductRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    private $productRepository;
    private $userRepository;
    private $imageHelper;

    public function __construct(ProductRepositoryInterface $productRepository,
                                UserRepositoryInterface $userRepository,
                                ImageHelper $imageHelper)
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->imageHelper = $imageHelper;
    }

    public function all(Request $request){
        $data = $request->all();
        if(!array_key_exists('keyword', $data)) $data['keyword'] = '';
        if(!array_key_exists('offset', $data)) $data['offset'] = 0;
        if(!array_key_exists('limit', $data)) $data['limit'] = 20;
        if(!array_key_exists('order', $data)) $data['order'] = 'desc';
        $products = $this->productRepository->all($data);
        $count = $this->productRepository->getCount($data);
        $response = ['products'=>$products, 'count'=>$count];
        return new SuccessResource(['data'=>$response, 'message'=>'success']);
    }

    public function getOne($id){
        return $this->productRepository->getOne($id);
    }

    public function create(ProductRequest $request){
        $data = $request->all();
        if($request->has('image')){
            $path = $this->imageHelper->uploadImage($request->file('image'));
            $data['image'] = $path;
        }

        $product = $this->productRepository->create($data);
        return new SuccessResource(['message'=>'Product created successfully', 'data' => $product]);
    }

    public function update(ProductUpdateRequest $request, $id){
        $data = $request->all();
        $product = $this->productRepository->getOne($id);
        if(!$product){
            return new ErrorResource(['message'=>'Product not found']);
        }
        if($request->has('image')){
            $path = $this->imageHelper->uploadImage($request->file('image'));
            $data['image'] = $path;
        }
        $this->productRepository->update($id, $data);
        $product = $this->productRepository->getOne($id);
        return new SuccessResource(['message'=>'Product updated successfully', 'data' => $product]);
    }

    public function delete($id){
        $product = $this->productRepository->getOne($id);
        if(!$product){
            return new ErrorResource(['message'=>'Product not found']);
        }
        $this->productRepository->delete($id);
        return new SuccessResource(['message'=>'Product deleted successfully']);
    }

    public function bid(BidRequest $request){
        $data = $request->all();
        $user_id = Auth::user()->id;
        $data['user_id'] = $user_id;
        $product_id = $data['product_id'];
        $product = $this->productRepository->getOne($product_id);
        $close_date = Carbon::parse($product->close_date);
        $now = Carbon::now();
        if(!$product){
            return new ErrorResource(['message'=>'Product not found']);
        }
        $user = $this->userRepository->getOne($user_id);
        if(!$user){
            return new ErrorResource(['message'=>'User not found']);
        }
        if($now->gte($close_date)) return new ErrorResource(['message'=>'Date expired']);

        if($data['amount'] <= $product->min_price || ($product->bid && $product->bid->amount >= $data['amount'])){
            return new ErrorResource(['message'=>'The product price is greater']);
        }
        $this->productRepository->bidToProduct($data);

        $this->checkExistingAutobids($product_id, $user_id);

        $updated_product = $this->productRepository->getOne($product_id);

        return new SuccessResource(['data'=>$updated_product, 'message'=>'bid success']);
    }

    public function enableAutobid($product_id){
        $user_id = Auth::user()->id;
        $product = $this->productRepository->getOne($product_id);
        $close_date = Carbon::parse($product->close_date);
        $now = Carbon::now();
        if(!$product){
            return new ErrorResource(['message'=>'Product not found']);
        }
        $user = $this->userRepository->getOne($user_id);
        if(!$user){
            return new ErrorResource(['message'=>'User not found']);
        }
        if($now->gte($close_date)) return new ErrorResource(['message'=>'Date expired']);
        $data = [
            'user_id'   => $user_id,
            'product_id'=> $product_id
        ];
        $this->productRepository->enableAutobid($data);

        return new SuccessResource(['message'=>'Autobid enabled successfully']);
    }

    public function checkExistingAutobids($product_id, $user_id){
        $autobids = $this->productRepository->getProductAutobids($product_id);
        $max_amount_reached = true;
        if($autobids){
            foreach ($autobids as $autobid){
                if($autobid->user_id != $user_id){
                    $user_bids = $this->productRepository->getUserAutobids($autobid->user_id);
                    $user = $this->userRepository->getOne($autobid->user_id);
                    $amount = 0;
                    foreach ($user_bids as $user_bid){
                        $bid = $this->productRepository->getProductBids($user_bid->product_id, $user_bid->user_id);
                        if($bid) $amount += $bid->amount;
                    }
                    $productBid = $this->productRepository->getProductMaxBid($product_id);
                    $price = $productBid->amount + 1;
                    if($price <= $user->max_amount && $productBid->user_id != $user->id) {
                        $data = [
                            'user_id'   => $user->id,
                            'product_id'=> $product_id,
                            'amount'    => $price
                        ];
                        $this->productRepository->bidToProduct($data);
                        $max_amount_reached = false;
                    }
                }
            }
            if(!$max_amount_reached) $this->checkExistingAutobids($product_id, null);
        }
    }

}
