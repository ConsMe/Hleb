<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Price;
use App\Product;
use App\Setting;
use JavaScript;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function prices(Request $request)
    {

        JavaScript::put([
            'items' => Price::where('user_id', $request->user()->id)->get()
        ]);
        return view('prices');
    }

    public function changeprice(Request $request)
    {
        if ($request->id == 0) {
            $data = $request->except(['id']);
            $data['user_id'] = $request->user()->id;
            $newItem = Price::create($data);
            return response()->json(['success' => 'Добавлено', 'id' => $newItem->id]);
        } else {
            $item = Price::where(['id' => $request->id, 'user_id' => $request->user()->id])->first();
            if (!$item) {
                return response()->json(['error' => 'Что-то пошло не так, обратитесь к разработчикам', 'id' => $item->id]);
            }
            $item->name = $request->name;
            $item->edizm = $request->edizm;
            $item->price = $request->price;
            $item->save();
            return response()->json(['success' => 'Сохранено', 'id' => $item->id]);
        }
    }

    public function delitem(Request $request)
    {
        $products = Product::where('user_id', $request->user()->id)->get()->toArray();
        foreach ($products as $p) {
            foreach (json_decode($p['ingridients'], True) as $ing) {
                if ($ing['ingId'] == $request->id) {
                    return response()->json(['error' => 'Удали сначала изделия с этим ингридиентом, начни с '.$p['name'], 'id' => $request->id, 'product' => $p['name']]);
                }
            }
        }
        $item = Price::where(['id' => $request->id, 'user_id' => $request->user()->id])->first();
        if ($item) {
            $item->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Этот материал не найден, возможно он был удален ранее']);
    }

    public function products(Request $request)
    {
        $marga = Setting::where(['name' => 'defaultMarja', 'user_id' => $request->user()->id])->first();
        $marga = $marga ? $marga->value : env('DEFAULT_MARGA');
        JavaScript::put([
            'items' => Price::where('user_id', $request->user()->id)->get(),
            'products' => Product::where('user_id', $request->user()->id)->get(),
            'defaultMarja' => $marga
        ]);
        return view('products');
    }

    public function changeproduct(Request $request)
    {
        $items = [];
        foreach (json_decode($request->ingridients, True) as $item) {
            $items[] = $item['ingId'];
        }
        if (Price::where('user_id', $request->user()->id)->whereIn('id', $items)->get()->count() != count($items)) {
            return response()->json(['error' => 'В базе не найден один из ингридиентов этого изделия, проверь раздел "цены"', 'id' => $request->id]);
        }
        if ($request->id == 0) {
            $data = $request->except(['id']);
            $data['user_id'] = $request->user()->id;
            $newProduct = Product::create($data);
            return response()->json(['success' => 'Добавлено', 'id' => $newProduct->id]);
        } else {
            $product = Product::where(['user_id' => $request->user()->id, 'id' => $request->id])->first();
            if (!$product) {
                return response()->json(['error' => 'Что-то пошло не так, обратитесь к разработчикам', 'id' => $request->id]);
            }
            $product->name = $request->name;
            $product->batch = $request->batch;
            $product->marja = $request->marja;
            $product->ingridients = $request->ingridients;
            $product->save();
            return response()->json(['success' => 'Сохранено', 'id' => $product->id]);
        }
    }

    public function delproduct(Request $request)
    {
        $product = Product::where(['id' => $request->id, 'user_id' => $request->user()->id])->first();
        if ($product) {
            $product->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Это изделие не найдено, возможно оно было удалено ранее']);
    }

    public function saveNewDefMarja(Request $request)
    {
        $marga = Setting::where(['user_id' => $request->user()->id, 'name' => 'defaultMarja'])->first();
        if ($marga) {
            $marga->value = $request->marja;
            $marga->save();
        } else {
            Setting::create(['name' => 'defaultMarja', 'value' => $request->marja, 'user_id' => $request->user()->id]);
        }
        return response()->json(['success' => true]);
    }
}
