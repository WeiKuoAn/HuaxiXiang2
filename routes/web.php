<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomrtGruopController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserBankDataController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PromController;
use App\Http\Controllers\SaleDataController;
use App\Http\Controllers\UserSaleDataController;
use App\Http\Controllers\SaleSourceController;
use App\Http\Controllers\VenderController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeDataController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\PayDataController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\PujaController;
use App\Http\Controllers\PujaTypeController;
use App\Http\Controllers\PujaDataController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTypeController;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/home', function () {
//     return view('index');
// })->middleware('auth')->name('home');

require __DIR__ . '/auth.php';

// Route::group(['prefix' => '/'], function () {
//     Route::get('', [RoutingController::class, 'index'])->name('root');
//     Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
//     Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
//     Route::get('{any}', [RoutingController::class, 'root'])->name('any');
// });

//20230321更新

Route::group(['prefix' => '/'], function () {
    Route::get('', function ()    {
        return view('auth.login');
    });
    //登入後的打卡畫面
    Route::get('dashboard', [DashboardController::class, 'loginSuccess'])->name('index');
    Route::post('/dashboard', [DashboardController::class, 'store'])->name('index.worktime');
    Route::get('dashboard_info', [DashboardController::class, 'index'])->name('dashboard.info');
    
    /*用戶管理*/
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user/create', [UserController::class, 'store'])->name('user.create.data');
    Route::get('user/edit/{id}', [UserController::class, 'show'])->name('user.edit');
    Route::post('user/edit/{id}', [UserController::class, 'update'])->name('user.edit.data');

    /*部門管理*/
    Route::get('branchs', [BranchController::class, 'index'])->name('branchs');
    Route::get('branch/create', [BranchController::class, 'create'])->name('branch.create');
    Route::post('branch/create', [BranchController::class, 'store'])->name('branch.create.data');
    Route::get('branch/edit/{id}', [BranchController::class, 'show'])->name('branch.edit');
    Route::post('branch/edit/{id}', [BranchController::class, 'update'])->name('branch.edit.data');

    /*職稱管理*/
    Route::get('/jobs', [JobController::class, 'index'])->middleware(['auth'])->name('jobs');
    Route::get('/job/create', [JobController::class, 'create'])->name('job.create');
    Route::post('/job/create', [JobController::class, 'store'])->name('job.create.data');
    Route::get('/job/edit/{id}', [JobController::class, 'show'])->name('job.edit');
    Route::post('/job/edit/{id}', [JobController::class, 'update'])->name('job.edit.data');

    /*用戶個人設定*/
    Route::get('user-profile', [PersonController::class, 'show'])->name('user-profile');
    Route::post('user-profile', [PersonController::class, 'update'])->name('user-profile.data');
    Route::get('user-password', [UserController::class, 'password_show'])->name('user-password');
    Route::post('user-password', [UserController::class, 'password_update'])->name('user-password.data');
    
    /*客戶管理 */
    Route::get('customers', [CustomerController::class, 'index'])->name('customer');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('customer/create', [CustomerController::class, 'store'])->name('customer.create.data');
    Route::get('customer/edit/{id}', [CustomerController::class, 'show'])->name('customer.edit');
    Route::post('customer/edit/{id}', [CustomerController::class, 'update'])->name('customer.edit.data');

    /*客戶群組管理*/
    Route::get('/customer/group', [CustomrtGruopController::class, 'index'])->name('customer.group');
    Route::get('/customer/group/create', [CustomrtGruopController::class, 'create'])->name('customer-group.create');
    Route::post('/customer/group/create', [CustomrtGruopController::class, 'store'])->name('customer-group.create.data');
    Route::get('/customer/group/edit/{id}', [CustomrtGruopController::class, 'show'])->name('customer-group.edit');
    Route::post('/customer/group/edit/{id}', [CustomrtGruopController::class, 'update'])->name('customer-group.edit.data');

    /*商品類別管理*/
    Route::get('/product/category', [CategoryController::class, 'index'])->name('product.category');
    Route::get('/product/category/create', [CategoryController::class, 'create'])->name('product.category.create');
    Route::post('/product/category/create', [CategoryController::class, 'store'])->name('product.category.create.data');
    Route::get('/product/category/edit/{id}', [CategoryController::class, 'edit'])->name('product.category.edit');
    Route::post('/product/category/edit/{id}', [CategoryController::class, 'update'])->name('product.category.edit.data');

    Route::get('/products', [ProductController::class, 'index'])->name('product');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/create', [ProductController::class, 'store'])->name('product.data.create');
    Route::get('/product/edit/{id}', [ProductController::class, 'show'])->name('product.edit');
    Route::post('/product/edit/{id}', [ProductController::class, 'update'])->name('product.data.edit');
    Route::get('/product/lims_product_search', [ProductController::class, 'product_search'])->name('product.product_search');
    Route::get('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.del');
    Route::post('/product/delete/{id}', [ProductController::class, 'destroy'])->name('product.del.data');


    /*業務管理*/
    Route::get('/sales', [SaleDataController::class, 'index'])->name('sales');
    Route::get('/sale/create', [SaleDataController::class, 'create'])->name('sale.create');
    Route::post('/sale/create', [SaleDataController::class, 'store'])->name('sale.data.create');
    Route::get('/sale/edit/{id}', [SaleDataController::class, 'show'])->name('sale.edit');
    Route::post('/sale/edit/{id}', [SaleDataController::class, 'update'])->name('sale.data.edit');
    Route::get('/sale/del/{id}', [SaleDataController::class, 'delete'])->name('sale.del');
    Route::post('/sale/del/{id}', [SaleDataController::class, 'destroy'])->name('sale.data.del');
    Route::get('/prom/search', [SaleDataController::class, 'prom_search'])->name('prom.search');
    Route::get('/gdpaper/search', [SaleDataController::class, 'gdpaper_search'])->name('gdpaper.search');
    Route::get('/customer/search', [SaleDataController::class, 'customer_search'])->name('customer.search');
    Route::get('/company/search', [SaleDataController::class, 'company_search'])->name('company.search');

    Route::get('user/{id}/sale', [SaleDataController::class, 'user_sale'])->name('user.sale');

    /*來源管理*/
    Route::get('/sources', [SaleSourceController::class, 'index'])->name('sources');
    Route::get('/source/create', [SaleSourceController::class, 'create'])->name('source.create');
    Route::post('/source/create', [SaleSourceController::class, 'store'])->name('source.create.data');
    Route::get('/source/edit/{id}', [SaleSourceController::class, 'show'])->name('source.edit');
    Route::post('/source/edit/{id}', [SaleSourceController::class, 'update'])->name('source.edit.data');

    /*方案管理*/
    Route::get('/plans', [PlanController::class, 'index'])->name('plans');
    Route::get('/plan/create', [PlanController::class, 'create'])->name('plan.create');
    Route::post('/plan/create', [PlanController::class, 'store'])->name('plan.create.data');
    Route::get('/plan/edit/{id}', [PlanController::class, 'show'])->name('plan.edit');
    Route::post('/plan/edit/{id}', [PlanController::class, 'update'])->name('plan.edit.data');

    /*後續處理管理*/
    Route::get('/proms', [PromController::class, 'index'])->name('proms');
    Route::get('/prom/create', [PromController::class, 'create'])->name('prom.create');
    Route::post('/prom/create', [PromController::class, 'store'])->name('prom.create.data');
    Route::get('/prom/edit/{id}', [PromController::class, 'show'])->name('prom.edit');
    Route::post('/prom/edit/{id}', [PromController::class, 'update'])->name('prom.edit.data');

    /*廠商管理 */
    Route::get('/venders', [VenderController::class, 'index'])->name('venders');
    Route::get('/vender/create', [VenderController::class, 'create'])->name('vender.create');
    Route::post('/vender/create', [VenderController::class, 'store'])->name('vender.create.data');
    Route::get('/vender/edit/{id}', [VenderController::class, 'show'])->name('vender.edit');
    Route::post('/vender/edit/{id}', [VenderController::class, 'update'])->name('vender.edit.data');

    /*收入科目管理*/
    Route::get('/income/sujects', [IncomeController::class, 'index'])->name('income.sujects');
    Route::get('/income/suject/create', [IncomeController::class, 'create'])->name('income.suject.create');
    Route::post('/income/suject/create', [IncomeController::class, 'store'])->name('income.suject.create.data');
    Route::get('/income/suject/edit/{id}', [IncomeController::class, 'show'])->name('income.suject.edit');
    Route::post('/income/suject/edit/{id}', [IncomeController::class, 'update'])->name('income.suject.edit.data');

    /*收入管理*/
    Route::get('/income', [IncomeDataController::class, 'index'])->name('incomes');
    Route::get('/income/create', [IncomeDataController::class, 'create'])->name('income.create');
    Route::post('/income/create', [IncomeDataController::class, 'store'])->name('income.create.data');
    Route::get('/income/edit/{id}', [IncomeDataController::class, 'show'])->name('income.edit');
    Route::post('/income/edit/{id}', [IncomeDataController::class, 'update'])->name('income.edit.data');
    Route::get('/income/del/{id}', [IncomeDataController::class, 'delshow'])->name('income.del');
    Route::post('/income/del/{id}', [IncomeDataController::class, 'delete'])->name('income.del.data');

    /*支出科目管理*/
    Route::get('/pay/sujects', [PayController::class, 'index'])->name('pay.sujects');
    Route::get('/pay/suject/create', [PayController::class, 'create'])->name('pay.suject.create');
    Route::post('/pay/suject/create', [PayController::class, 'store'])->name('pay.suject.create.data');
    Route::get('/pay/suject/edit/{id}', [PayController::class, 'show'])->name('pay.suject.edit');
    Route::post('/pay/suject/edit/{id}', [PayController::class, 'update'])->name('pay.suject.edit.data');

    /*支出管理*/
    Route::get('/pay', [PayDataController::class, 'index'])->name('pays');
    Route::get('/pay/create', [PayDataController::class, 'create'])->name('pay.create');
    Route::post('/pay/create', [PayDataController::class, 'store'])->name('pay.create.data');
    Route::get('/pay/edit/{id}', [PayDataController::class, 'show'])->name('pay.edit');
    Route::post('/pay/edit/{id}', [PayDataController::class, 'update'])->name('pay.edit.data');
    Route::get('/pay/del/{id}', [PayDataController::class, 'delshow'])->name('pay.del');
    Route::post('/pay/del/{id}', [PayDataController::class, 'delete'])->name('pay.del.data');
    Route::get('/pay/check/{id}', [PayDataController::class, 'check'])->name('pay.check');
    Route::post('/pay/check/{id}', [PayDataController::class, 'check_data'])->name('pay.check.data');

    /*零用金管理*/
    Route::get('/cash', [CashController::class, 'index'])->name('cashs');
    Route::get('/cash/create', [CashController::class, 'create'])->name('cash.create');
    Route::post('/cash/create', [CashController::class, 'store'])->name('cash.create.data');
    Route::get('/cash/edit/{id}', [CashController::class, 'show'])->name('cash.edit');
    Route::post('/cash/edit/{id}', [CashController::class, 'update'])->name('cash.edit.data');

    Route::get('pay/vender/number', [VenderController::class, 'number'])->name('vender.number');

    /*專員戶頭設定*/
    Route::get('/user/bank', [UserBankDataController::class, 'index'])->name('user.bank');
    Route::get('/user/bank/create', [UserBankDataController::class, 'create'])->name('user.bank.create');
    Route::post('/user/bank/create', [UserBankDataController::class, 'store'])->name('user.bank.create.data');
    Route::get('/user/bank/edit/{id}', [UserBankDataController::class, 'show'])->name('user.bank.edit');
    Route::post('/user/bank/edit/{id}', [UserBankDataController::class, 'update'])->name('user.bank.edit.data');

    /*人事管理*/
    Route::get('personnels', [PersonnelController::class, 'index'])->name('personnels');

    /*年度總休假管理*/
    Route::get('/vacation', [VacationController::class, 'index'])->name('vacations');
    Route::get('/vacation/create', [VacationController::class, 'create'])->name('vacation.create');
    Route::post('/vacation/create', [VacationController::class, 'store'])->name('vacation.create.data');
    Route::get('/vacation/edit/{id}', [VacationController::class, 'show'])->name('vacation.edit');
    Route::post('/vacation/edit/{id}', [VacationController::class, 'update'])->name('vacation.edit.data');
    
    /*法會類別管理*/
    Route::get('/puja/type', [PujaTypeController::class, 'index'])->name('puja.types');
    Route::get('/puja/type/create', [PujaTypeController::class, 'create'])->name('puja.type.create');
    Route::post('/puja/type/create', [PujaTypeController::class, 'store'])->name('puja.type.create.data');
    Route::get('/puja/type/edit/{id}', [PujaTypeController::class, 'show'])->name('puja.type.edit');
    Route::post('/puja/type/edit/{id}', [PujaTypeController::class, 'update'])->name('puja.type.edit.data');

    /*法會管理*/
    Route::get('/puja', [PujaController::class, 'index'])->name('pujas');
    Route::get('/puja/create', [PujaController::class, 'create'])->name('puja.create');
    Route::post('/puja/create', [PujaController::class, 'store'])->name('puja.create.data');
    Route::get('/puja/edit/{id}', [PujaController::class, 'show'])->name('puja.edit');
    Route::post('/puja/edit/{id}', [PujaController::class, 'update'])->name('puja.edit.data');

    /*法會報名管理*/
    Route::get('/puja_data', [PujaDataController::class, 'index'])->name('puja_datas');
    Route::get('/puja_data/create', [PujaDataController::class, 'create'])->name('puja_data.create');
    Route::post('/puja_data/create', [PujaDataController::class, 'store'])->name('puja_data.create.data');
    Route::get('/puja_data/edit/{id}', [PujaDataController::class, 'show'])->name('puja_data.edit');
    Route::post('/puja_data/edit/{id}', [PujaDataController::class, 'update'])->name('puja_data.edit.data');
    Route::get('/puja_data/del/{id}', [PujaDataController::class, 'delete'])->name('puja_data.del');
    Route::post('/puja_data/del/{id}', [PujaDataController::class, 'destroy'])->name('puja_data.del.data');
    Route::get('/customer/pet/search', [PujaDataController::class, 'customer_pet_search'])->name('customer.pet.search');
    Route::get('/puja/search', [PujaDataController::class, 'puja_search'])->name('puja.search');

    Route::get('image', function()
    {
        $img = Image::make('https://images.pexels.com/photos/4273439/pexels-photo-4273439.jpeg')->resize(300, 200); // 這邊可以隨便用網路上的image取代
        return $img->response('jpg');
    });
    
});

