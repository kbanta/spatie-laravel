<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Family;
use App\Models\Image;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\Size;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class SuperadminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:superadmin']);
    }
    public function index()
    {
        return view('superadmin.superadminhome');
    }
    public function users()
    {

        if (request()->ajax()) {
            return datatables()->of(User::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editUser(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" onClick="deleteUser(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        $roles = Role::all();
        return view('superadmin.account.users', compact('roles'));
    }
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required'],
        ], [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.required' => 'The role field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $users = new User;
            $lastcount = User::count();
            $countUser = $lastcount + 1;
            $uid = 'USR-' . str_pad($countUser, 5, '0', STR_PAD_LEFT);
            $users->display_id = $uid;
            $users->name = $request->input('name');
            $users->email = strtolower($request->input('email'));
            $users->password = Hash::make($request['password']);
            $users->save();
            $users->assignRole($request->role_id);
            return response()->json([
                'success' => 'account added successfully'
            ]);
        }
    }
    public function edituser(Request $request)
    {
        $where = array('users.id' => $request->id);
        $user  = User::join('model_has_roles', 'model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('users.id', '=', $request->id)
            ->select('*', 'users.id as uid', 'users.name as uname', 'roles.name as rolename')->first();
        return Response()->json($user);
    }
    public function updateuser(Request $request)
    {
        if (!empty($request->password)) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            $pass = Hash::make($request['password']);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $users = User::find($request->id);
            $users->name = $request->input('name');
            $users->email = $request->input('email');
            if (!empty($request->password)) {
                $users->password = $pass;
            }
        }
        $users->save();

        DB::table('model_has_roles')->where('model_id', $request->id)->delete();
        $users->assignRole($request->role_id);

        return response()->json([
            'success' => 'account updated successfully'
        ]);
    }
    public function deleteuser($id)
    {
        $users = User::find($id);
        $users->delete();
        return response()->json([
            'success' => 'account deleted successfully'
        ]);
    }
    // Roles Function...
    public function roles()
    {

        if (request()->ajax()) {
            return datatables()->of(Role::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editRole(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" onClick="deleteRole(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-role">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.role.roles');
    }
    public function registerRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => ['required', 'string', 'max:255'],
        ], [
            'role.required' => 'The role field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $role = new Role();
            $role->name = $request->input('role');
            $role->save();
            return response()->json([
                'success' => 'role added successfully'
            ]);
        }
    }
    public function editrole(Request $request)
    {
        $role  = Role::where('roles.id', '=', $request->id)->first();
        return Response()->json($role);
    }
    public function updaterole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $role = Role::find($request->id);
            $role->name = $request->input('role');
            $role->save();
        }

        return response()->json([
            'success' => 'role updated successfully'
        ]);
    }
    public function deleterole($id)
    {
        $role = Role::find($id);
        $role->delete();
        return response()->json([
            'success' => 'role deleted successfully'
        ]);
    }
    // Brands Function...
    public function brands()
    {

        if (request()->ajax()) {
            return datatables()->of(Brand::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editBrand(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-brand">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.brand.brands');
    }
    public function registerBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:255'],
        ], [
            'brand.required' => 'The brand field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $brand = new Brand();
            $brand->name = $request->input('brand');
            $brand->save();
            return response()->json([
                'success' => 'brand added successfully'
            ]);
        }
    }
    public function editbrand(Request $request)
    {
        $brand  = Brand::where('brands.id', '=', $request->id)->first();
        return Response()->json($brand);
    }
    public function updatebrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $brand = Brand::find($request->id);
            $brand->name = $request->input('brand');
            $brand->save();
        }

        return response()->json([
            'success' => 'brand updated successfully'
        ]);
    }
    public function deletebrand($id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        return response()->json([
            'success' => 'brand deleted successfully'
        ]);
    }
    // Categories Function...
    public function categories()
    {
        if (request()->ajax()) {
            return datatables()->of(Category::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editCategory(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-category">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.category.categories');
    }
    public function registerCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => ['required', 'string', 'max:255'],
        ], [
            'category.required' => 'The category field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $category = new Category();
            $category->name = $request->input('category');
            $category->save();
            return response()->json([
                'success' => 'brand added successfully'
            ]);
        }
    }
    public function editcategory(Request $request)
    {
        $category  = Category::where('categories.id', '=', $request->id)->first();
        return Response()->json($category);
    }
    public function updatecategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $category = Category::find($request->id);
            $category->name = $request->input('category');
            $category->save();
        }

        return response()->json([
            'success' => 'brand updated successfully'
        ]);
    }
    public function deletecategory($id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json([
            'success' => 'category deleted successfully'
        ]);
    }
    // Products Type Function...
    public function product_type()
    {
        if (request()->ajax()) {
            return datatables()->of(ProductType::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editProductType(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-product-type">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.product.product_types');
    }
    public function registerProductType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_type' => ['required', 'string', 'max:255'],
        ], [
            'product_type.required' => 'The product_type field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $product_type = new ProductType();
            $product_type->type_name = $request->input('product_type');
            $product_type->save();
            return response()->json([
                'success' => 'brand added successfully'
            ]);
        }
    }
    public function editproduct_type(Request $request)
    {
        $prodtype  = ProductType::where('product_types.id', '=', $request->id)->first();
        return Response()->json($prodtype);
    }
    public function updateproduct_type(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_type' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $prodtype = ProductType::find($request->id);
            $prodtype->type_name = $request->input('product_type');
            $prodtype->save();
        }

        return response()->json([
            'success' => 'brand updated successfully'
        ]);
    }
    public function deleteproduct_type($id)
    {
        $prodtype = ProductType::find($id);
        $prodtype->delete();
        return response()->json([
            'success' => 'prodtype deleted successfully'
        ]);
    }
    // Products FAmily Function...
    public function product_family()
    {
        if (request()->ajax()) {
            return datatables()->of(Family::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editProductFamily(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-product-type">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.product.product_family');
    }
    public function registerProductFamily(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_family' => ['required', 'string', 'max:255'],
        ], [
            'product_family.required' => 'The product_family field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $product_family = new Family();
            $product_family->family_name = $request->input('product_family');
            $product_family->save();
            return response()->json([
                'success' => 'product family added successfully'
            ]);
        }
    }
    public function editproduct_family(Request $request)
    {
        $prodfamily  = Family::where('families.id', '=', $request->id)->first();
        return Response()->json($prodfamily);
    }
    public function updateproduct_family(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_family' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $prodfamily = Family::find($request->id);
            $prodfamily->family_name = $request->input('product_family');
            $prodfamily->save();
        }

        return response()->json([
            'success' => 'brand updated successfully'
        ]);
    }
    public function deleteproduct_family($id)
    {
        $profamily = Family::find($id);
        $profamily->delete();
        return response()->json([
            'success' => 'prod family deleted successfully'
        ]);
    }

    // Products Function...
    public function products(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::select('products.id as pid', 'products.name', 'images.url', 'products.sku')
                ->leftJoin('prices', 'products.id', '=', 'prices.product_id')
                ->leftJoin('images', 'products.id', '=', 'images.product_id')
                ->whereNull('products.deleted_at')
                ->groupBy('products.id', 'products.name', 'images.url', 'products.sku')
                ->get();

            // Map the image filename to its URL using the Storage facade
            $data->transform(function ($item) {
                // Check if the url property is empty or null
                if (!empty($item->url)) {
                    $item->url = Storage::url('product_images/' . $item->url);
                } else {
                    $item->url = '/storage/product_images/no-image.png'; // Replace 'default_image_url.jpg' with your default image URL
                }
                return $item;
            });

            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = url('superadmin/superhome/products/edit/' . $row->pid);
                    $btn = '<a href="' . $editUrl . '" data-toggle="tooltip" data-id="' . $row->pid . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->pid . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-product">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.product.products.products');
    }
    public function productForm(Request $request)
    {
        $brand = Brand::all();
        $category = Category::all();
        $family = Family::all();
        $prod_type = ProductType::all();
        $color = Color::all();
        $size = Size::all();
        return view('superadmin.product.products.product_form', compact('brand', 'category', 'family', 'prod_type', 'color', 'size'));
    }
    public function productFormSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images-main.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate images-main only
            'product_type_id' => ['required'],
            'family_id' => ['required'],
            'brand_id' => ['required'],
            'category_id' => ['required'],
            'product_name' => ['required'],
            'description' => ['required'],
            'sku' => ['required'],
            'product_quantity' => ['required'],
            'price' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            try {
                $cnt_product = Product::max('id') + 1;
                // Create the product
                $product = Product::create([
                    'type_id' => $request->product_type_id,
                    'family_id' => $request->family_id,
                    'brand_id' => $request->brand_id,
                    'category_id' => $request->category_id,
                    'name' => $request->product_name,
                    'description' => $request->description,
                    'sku' => $request->sku,
                    'inventory' => $request->product_quantity,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id,
                    'weight' => $request->weight,
                    'dimension' => $request->dimension,
                ]);

                // Get the ID of the newly created product
                $newProductId = $product->id;

                Price::create([
                    'product_id' => $newProductId,
                    'price' => $request->price,
                    'cost' => $request->cost,
                    'discount' => $request->discount,
                    'special_price_start' => $request->special_price_start,
                    'special_price_end' => $request->special_price_end,
                ]);

                if ($request->hasFile('images-main')) {
                    $image = $request->file('images-main');
                    // foreach ($request->file('images-main') as $image) {
                    // Process and store each image
                    $fileName = time() . rand(111, 9999) . '.' . $image->getClientOriginalExtension();
                    // $imagePath = $image->storeAs('product_images', $fileName); // This line stores the image in the "product_images" folder
                    Storage::disk('product_images')->put($fileName, file_get_contents($image));
                    // Save image info to database
                    Image::create([
                        'product_id' => $cnt_product,
                        'url' => $fileName,
                    ]);
                    // }
                }
                if ($request->hasFile('images-additional')) {
                    foreach ($request->file('images-additional') as $images) {
                        // dd($images)
                        // Process and store each image
                        $fileNames = time() . rand(111, 9999) . '.' . $images->getClientOriginalExtension();
                        // $imagePath = $images->storeAs('product_images', $fileName); // This line stores the images in the "product_images" folder
                        Storage::disk('product_images')->put($fileNames, file_get_contents($images));
                        // Save images info to database
                        ProductImage::create([
                            'product_id' => $cnt_product,
                            'url' => $fileNames,
                        ]);
                    }
                }
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'error inserting'
                ]);
            }
            // Optionally, you can return a success response
            return response()->json([
                'success' => 'product updated successfully'
            ]);
        }
    }
    public function editproduct(Request $request)
    {
        $product  = Product::leftjoin('prices', 'products.id', '=', 'prices.product_id')
            ->leftjoin('images', 'products.id', '=', 'images.product_id')
            // ->leftjoin('product_images','products.id','=','product_images.product_id')
            ->select('*', 'images.url as main_image', 'products.id as id')
            ->where('products.id', '=', $request->id)
            ->whereNull('images.deleted_at')
            ->first();
        $prod_add_image = Product::join('product_images', 'products.id', '=', 'product_images.product_id')
            ->select('product_images.url')
            ->where('products.id', '=', $request->id)
            ->whereNull('product_images.deleted_at')
            ->get()->toArray();
        // dd($product);
        $brand = Brand::all();
        $category = Category::all();
        $family = Family::all();
        $prod_type = ProductType::all();
        $color = Color::all();
        $size = Size::all();
        return view('superadmin.product.products.product_form', compact('prod_add_image', 'product', 'brand', 'category', 'family', 'prod_type', 'color', 'size'));
    }
    public function updateproduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $product = Product::where(['id' => $request->id])->first();
            $product->update([
                'type_id' => $request->product_type_id,
                'family_id' => $request->family_id,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'name' => $request->product_name,
                'description' => $request->description,
                'sku' => $request->sku,
                'inventory' => $request->product_quantity,
                'color_id' => $request->color_id,
                'size_id' => $request->size_id,
                'weight' => $request->weight,
                'dimension' => $request->dimension,
            ]);
            // $product->save();
            $price = Price::where(['product_id' => $request->id])->first();
            $price->update([
                'price' => $request->price,
                'cost' => $request->cost,
                'discount' => $request->discount,
                'special_price_start' => $request->special_price_start,
                'special_price_end' => $request->special_price_end,
            ]);
            // $price->save();
            if ($request->hasFile('images-main')) {
                $image = $request->file('images-main'); //selected image..
                $chkimage = Image::where([
                    'product_id' => $request->id,
                ])->first();
                $fileName = time() . rand(111, 9999) . '.' . $image->getClientOriginalExtension();
                Storage::disk('product_images')->put($fileName, file_get_contents($image));
                if ($chkimage == null) {
                    Image::create([
                        'product_id' => $request->id,
                        'url' => $fileName,
                    ]);
                } else {
                    $chkimage->update([
                        'product_id' => $request->id,
                        'url' => $fileName,
                    ]);
                }
            }

            if ($request->hasFile('images-additional')) {
                if (!empty($request->images_additional_holder)) {
                    foreach ($request->images_additional_holder as $image_old) {
                        ProductImage::where('product_id', $request->id)
                            ->whereNull('deleted_at')
                            ->delete();
                    }
                }
                foreach ($request->file('images-additional') as $images) {
                    $fileNames = time() . rand(111, 9999) . '.' . $images->getClientOriginalExtension();
                    Storage::disk('product_images')->put($fileNames, file_get_contents($images));
                    ProductImage::create([
                        'product_id' => $request->id,
                        'url' => $fileNames,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => 'product updated successfully'
        ]);
    }
    public function deleteproduct($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json([
            'success' => 'product deleted successfully'
        ]);
    }
    // Products Colors Function...
    public function colors()
    {
        if (request()->ajax()) {
            return datatables()->of(Color::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editColor(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-color">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.product.product_colors');
    }
    public function registerColor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => ['required', 'string', 'max:255'],
        ], [
            'color.required' => 'The color field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $color = new Color();
            $color->color = $request->input('color');
            $color->save();
            return response()->json([
                'success' => 'product family added successfully'
            ]);
        }
    }
    public function editcolor(Request $request)
    {
        $color  = Color::where('colors.id', '=', $request->id)->first();
        return Response()->json($color);
    }
    public function updatecolor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $color = Color::find($request->id);
            $color->color = $request->input('color');
            $color->save();
        }

        return response()->json([
            'success' => 'color updated successfully'
        ]);
    }
    public function deletecolor($id)
    {
        $color = Color::find($id);
        $color->delete();
        return response()->json([
            'success' => 'color deleted successfully'
        ]);
    }
    // Products Colors Function...
    public function sizes()
    {
        if (request()->ajax()) {
            return datatables()->of(Size::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editSize(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-size">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.product.product_sizes');
    }
    public function registerSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => ['required', 'string', 'max:255'],
        ], [
            'size.required' => 'The size field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $size = new Size();
            $size->size = $request->input('size');
            $size->save();
            return response()->json([
                'success' => 'size added successfully'
            ]);
        }
    }
    public function editsize(Request $request)
    {
        $size  = Size::where('sizes.id', '=', $request->id)->first();
        return Response()->json($size);
    }
    public function updatesize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $size = Size::find($request->id);
            $size->size = $request->input('size');
            $size->save();
        }

        return response()->json([
            'success' => 'size updated successfully'
        ]);
    }
    public function deletesize($id)
    {
        $size = Size::find($id);
        $size->delete();
        return response()->json([
            'success' => 'prod family deleted successfully'
        ]);
    }
}
