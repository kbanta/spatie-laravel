<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Family;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    public function products()
    {
        if (request()->ajax()) {
            return datatables()->of(Product::select('*')->where('deleted_at', '=', null))
                ->addColumn('action',  function ($row) {

                    $btn = '<a href="javascript:void(0)" onClick="editCategory(' . $row->id . ')" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-category">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('superadmin.product.products');
    }
    public function productForm()
    {
        return view('superadmin.product.product_form');
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
            'success' => 'brand updated successfully'
        ]);
    }
    public function deletecolor($id)
    {
        $color = Color::find($id);
        $color->delete();
        return response()->json([
            'success' => 'prod family deleted successfully'
        ]);
    }
}
