<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomrtGruopController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeregistrationController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeDataController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobMenuController;
use App\Http\Controllers\LampController;
use App\Http\Controllers\LampTypeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveDayController;
use App\Http\Controllers\LeaveSettingController;
use App\Http\Controllers\LiffController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OnlineColumbariumController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\PayDataController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromController;
use App\Http\Controllers\PromTypeController;
use App\Http\Controllers\PujaController;
use App\Http\Controllers\PujaDataController;
use App\Http\Controllers\PujaTypeController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\Rpg01Controller;
use App\Http\Controllers\Rpg02Controller;
use App\Http\Controllers\Rpg04Controller;
use App\Http\Controllers\Rpg05Controller;
use App\Http\Controllers\Rpg06Controller;
use App\Http\Controllers\Rpg07Controller;
use App\Http\Controllers\Rpg09Controller;
use App\Http\Controllers\Rpg10Controller;
use App\Http\Controllers\Rpg11Controller;
use App\Http\Controllers\Rpg12Controller;
use App\Http\Controllers\Rpg13Controller;
use App\Http\Controllers\Rpg14Controller;
use App\Http\Controllers\Rpg15Controller;
use App\Http\Controllers\Rpg16Controller;
use App\Http\Controllers\Rpg17Controller;
use App\Http\Controllers\Rpg18Controller;
use App\Http\Controllers\Rpg19Controller;
use App\Http\Controllers\Rpg20Controller;
use App\Http\Controllers\Rpg21Controller;
use App\Http\Controllers\Rpg22Controller;
use App\Http\Controllers\Rpg23Controller;
use App\Http\Controllers\Rpg24Controller;
use App\Http\Controllers\Rpg25Controller;
use App\Http\Controllers\Rpg26Controller;
use App\Http\Controllers\Rpg27Controller;
use App\Http\Controllers\Rpg28Controller;
use App\Http\Controllers\Rpg29Controller;
use App\Http\Controllers\Rpg30Controller;
use App\Http\Controllers\Rpg31Controller;
use App\Http\Controllers\Rpg32Controller;
use App\Http\Controllers\Rpg33Controller;
use App\Http\Controllers\CrematoriumController;
use App\Http\Controllers\SaleDataController;
use App\Http\Controllers\SaleDataControllerNew;
use App\Http\Controllers\SaleSourceController;
use App\Http\Controllers\SeniorityPausesController;
use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\SouvenirTypeController;
use App\Http\Controllers\SuitController;
use App\Http\Controllers\TargetCategoriesController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\TargetItemController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserBankDataController;    
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSaleDataController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\VenderController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\MeritController;
use App\Http\Controllers\ScrappedController;
use App\Models\Pay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\GiveController;
use App\Http\Controllers\IncreaseController;
use App\Http\Controllers\OvertimeController;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
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

// 20230321更新

Route::group(['prefix' => '/'], function () {
    Route::get('', function () {
        Auth::logout();
        return view('auth.login');
    });
    // 登入後的打卡畫面
    Route::get('dashboard', [DashboardController::class, 'loginSuccess'])->name('index');
    Route::post('/dashboard', [DashboardController::class, 'store'])->name('index.worktime');
    Route::get('dashboard_info', [DashboardController::class, 'index'])->name('dashboard.info');
    Route::get('sale_dashboard', [DashboardController::class, 'sale_index'])->name('sale.dashboard.info');

    /* 用戶管理 */
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user/create', [UserController::class, 'store'])->name('user.create.data');
    Route::get('user/edit/{id}', [UserController::class, 'show'])->name('user.edit');
    Route::post('user/edit/{id}', [UserController::class, 'update'])->name('user.edit.data');

    // 用戶出勤
    Route::get('user/work/{id}', [WorkController::class, 'user_work'])->name('user.work.index');
    Route::get('user/work/edit/{id}', [WorkController::class, 'showuserwork'])->name('user.work.edit');
    Route::post('user/work/edit/{id}', [WorkController::class, 'edituserwork'])->name('user.work.edit.data');
    Route::get('user/work/del/{id}', [WorkController::class, 'showdeluserwork'])->name('user.work.del');
    Route::post('user/work/del/{id}', [WorkController::class, 'deluserwork'])->name('user.work.del.data');
    Route::get('user/work/{id}/export', [WorkController::class, 'export'])->name('user.work.export');

    /* 部門管理 */
    Route::get('branchs', [BranchController::class, 'index'])->name('branchs');
    Route::get('branch/create', [BranchController::class, 'create'])->name('branch.create');
    Route::post('branch/create', [BranchController::class, 'store'])->name('branch.create.data');
    Route::get('branch/edit/{id}', [BranchController::class, 'show'])->name('branch.edit');
    Route::post('branch/edit/{id}', [BranchController::class, 'update'])->name('branch.edit.data');

    /* 職稱管理 */
    Route::get('/jobs', [JobController::class, 'index'])->middleware(['auth'])->name('jobs');
    Route::get('/job/create', [JobController::class, 'create'])->name('job.create');
    Route::post('/job/create', [JobController::class, 'store'])->name('job.create.data');
    Route::get('/job/edit/{id}', [JobController::class, 'show'])->name('job.edit');
    Route::post('/job/edit/{id}', [JobController::class, 'update'])->name('job.edit.data');

    /* 用戶個人設定 */
    Route::get('user-profile', [PersonController::class, 'show'])->name('user-profile');
    Route::post('user-profile', [PersonController::class, 'update'])->name('user-profile.data');
    Route::get('user-password', [UserController::class, 'password_show'])->name('user-password');
    Route::post('user-password', [UserController::class, 'password_update'])->name('user-password.data');
    Route::get('person/sales', [PersonController::class, 'sale_index'])->name('person.sales');
    Route::get('person/wait/sales', [PersonController::class, 'wait_sale_index'])->name('person.wait.sales');
    Route::get('person/pays', [PersonController::class, 'pay_index'])->name('person.pays');
    Route::get('person/leave_days', [PersonController::class, 'leave_index'])->name('person.leave_days');
    Route::get('person/leave_day/check/{id}', [PersonController::class, 'leave_check_show'])->name('person.leave_day.check');
    Route::post('person/leave_day/check/{id}', [PersonController::class, 'leave_check_update'])->name('person.leave_day.check.data');
    Route::get('/person_inventory', [PersonController::class, 'person_inventory'])->name('person.inventory');
    Route::get('person/sale_statistics', [PersonController::class, 'sale_statistics'])->name('preson.sale_statistics');
    Route::get('person/last_leave_days', [PersonController::class, 'last_leave_days'])->name('person.last_leave_days');

    /* 請假管理 */
    Route::get('personnel/leave_days', [LeaveDayController::class, 'index'])->name('personnel.leave_days');
    Route::get('personnel/user/{id}/leave_day', [LeaveDayController::class, 'user_index'])->name('user.leave_day');
    Route::get('leave_day/create', [LeaveDayController::class, 'create'])->name('leave_day.create');
    Route::post('leave_day/create', [LeaveDayController::class, 'store'])->name('leave_day.create.data');
    Route::post('/leave-day/upload-file', [LeaveDayController::class, 'uploadFile'])->name('leave_day.upload_file');
    Route::get('leave_day/edit/{id}', [LeaveDayController::class, 'show'])->name('leave_day.edit');
    Route::post('leave_day/edit/{id}', [LeaveDayController::class, 'update'])->name('leave_day.edit.data');
    Route::get('leave_day/del/{id}', [LeaveDayController::class, 'delete'])->name('leave_day.del');
    Route::post('leave_day/del/{id}', [LeaveDayController::class, 'destroy'])->name('leave_day.del.data');
    Route::get('leave_day/check/{id}', [LeaveDayController::class, 'check'])->name('leave_day.check');
    Route::post('leave_day/check/{id}', [LeaveDayController::class, 'check_data'])->name('leave_day.check.data');

    // 假別管理
    Route::get('personnel/leaves', [LeaveController::class, 'index'])->name('personnel.leaves');
    Route::get('personnel/leaves/create', [LeaveController::class, 'create'])->name('personnel.leaves.create');
    Route::post('personnel/leaves/create', [LeaveController::class, 'store'])->name('personnel.leaves.create.data');
    Route::get('personnel/leaves/edit/{id}', [LeaveController::class, 'edit'])->name('personnel.leaves.edit');
    Route::post('personnel/leaves/edit/{id}', [LeaveController::class, 'update'])->name('personnel.leaves.edit.data');
    Route::get('personnel/leaveSetting/create', [LeaveSettingController::class, 'create'])->name('personnel.leavesitting.create');
    Route::post('personnel/leaveSetting/store', [LeaveSettingController::class, 'store'])->name('personnel.leavesitting.create.data');
    Route::get('personnel/leaveSetting/edit/{id}', [LeaveSettingController::class, 'edit'])->name('personnel.leavesitting.edit');
    Route::post('personnel/leaveSetting/edit/{id}', [LeaveSettingController::class, 'update'])->name('personnel.leavesitting.edit.data');

    /* 客戶管理 */
    Route::get('customers', [CustomerController::class, 'index'])->name('customer');
    Route::get('customer_data', [CustomerController::class, 'customer_data'])->name('customer.data');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('customer/create', [CustomerController::class, 'store'])->name('customer.create.data');
    Route::get('customer/detail/{id}', [CustomerController::class, 'detail'])->name('customer.detail');
    Route::get('customer/edit/{id}', [CustomerController::class, 'show'])->name('customer.edit');
    Route::post('customer/edit/{id}', [CustomerController::class, 'update'])->name('customer.edit.data');
    Route::get('customer/del/{id}', [CustomerController::class, 'delete'])->name('customer.del');
    Route::post('customer/del/{id}', [CustomerController::class, 'destroy'])->name('customer.del.data');
    Route::get('customer/{id}/sales', [CustomerController::class, 'sales'])->name('customer.sales');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customer.export');

    /* 拜訪管理 */
    Route::get('search_district', [VisitController::class, 'search_district'])->name('search.district');  // ajax搜尋區域

    Route::get('hospitals', [VisitController::class, 'hospitals'])->name('hospitals');  // 醫院
    Route::get('etiquettes', [VisitController::class, 'etiquettes'])->name('etiquettes');  // 禮儀社
    Route::get('reproduces', [VisitController::class, 'reproduces'])->name('reproduces');  // 繁殖場
    Route::get('dogparks', [VisitController::class, 'dogparks'])->name('dogparks');  // 狗園
    Route::get('salons', [VisitController::class, 'salons'])->name('salons');  // 美容院
    Route::get('others', [VisitController::class, 'others'])->name('others');  // 其他合作廠商
    Route::get('source/sales/{id}', [VisitController::class, 'source_sale'])->name('visit.source.sale');  // 來源銷售

    Route::get('visit/{id}', [VisitController::class, 'index'])->name('visits');
    Route::get('visit/create/{id}', [VisitController::class, 'create'])->name('visit.create');
    Route::post('visit/create/{id}', [VisitController::class, 'store'])->name('visit.create.data');
    Route::get('visit/edit/{cust_id}/{id}', [VisitController::class, 'show'])->name('visit.edit');
    Route::post('visit/edit/{cust_id}/{id}', [VisitController::class, 'update'])->name('visit.edit.data');
    Route::get('visit/del/{cust_id}/{id}', [VisitController::class, 'delete'])->name('visit.del');
    Route::post('visit/del/{cust_id}{id}', [VisitController::class, 'destroy'])->name('visit.del.data');
    Route::get('visit/company/create', [VisitController::class, 'company_create'])->name('visit.company.create');
    Route::post('visit/company/create', [VisitController::class, 'company_store'])->name('visit.company.create.data');
    Route::get('visit/company/edit/{id}', [VisitController::class, 'company_edit'])->name('visit.company.edit');
    Route::post('visit/company/edit/{id}', [VisitController::class, 'company_update'])->name('visit.company.edit.data');

    /* 客戶群組管理 */
    Route::get('/customer/group', [CustomrtGruopController::class, 'index'])->name('customer.group');
    Route::get('/customer/group/create', [CustomrtGruopController::class, 'create'])->name('customer-group.create');
    Route::post('/customer/group/create', [CustomrtGruopController::class, 'store'])->name('customer-group.create.data');
    Route::get('/customer/group/edit/{id}', [CustomrtGruopController::class, 'show'])->name('customer-group.edit');
    Route::post('/customer/group/edit/{id}', [CustomrtGruopController::class, 'update'])->name('customer-group.edit.data');

    /* 商品類別管理 */
    Route::get('/product/category', [CategoryController::class, 'index'])->name('product.category');
    Route::get('/product/category/create', [CategoryController::class, 'create'])->name('product.category.create');
    Route::post('/product/category/create', [CategoryController::class, 'store'])->name('product.category.create.data');
    Route::get('/product/category/edit/{id}', [CategoryController::class, 'edit'])->name('product.category.edit');
    Route::post('/product/category/edit/{id}', [CategoryController::class, 'update'])->name('product.category.edit.data');

    /* 商品管理 */
    Route::get('/products', [ProductController::class, 'index'])->name('product');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/create', [ProductController::class, 'store'])->name('product.data.create');
    Route::get('/product/edit/{id}', [ProductController::class, 'show'])->name('product.edit');
    Route::post('/product/edit/{id}', [ProductController::class, 'update'])->name('product.data.edit');
    Route::get('/product/lims_product_search', [ProductController::class, 'product_search'])->name('product.product_search');
    Route::get('/product/cost_product_search', [ProductController::class, 'cost_product_search'])->name('product.cost_product_search');
    Route::get('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.del');
    Route::post('/product/delete/{id}', [ProductController::class, 'destroy'])->name('product.del.data');
    Route::get('/product/prom_product_search', [ProductController::class, 'prom_product_search'])->name('product.prom_product_search');
    Route::get('/product/variants', [ProductController::class, 'getVariants'])->name('product.variants');

    /* 商品進貨 */
    Route::get('/product/cost_search', [RestockController::class, 'product_cost_search'])->name('gdpaper.cost.search');
    Route::get('/product/restock', [RestockController::class, 'index'])->name('product.restock');
    Route::get('/product/restock/create', [RestockController::class, 'create'])->name('product.restock.create');
    Route::post('/product/restock/create', [RestockController::class, 'store'])->name('product.restock.create.data');
    Route::get('/product/restock/edit/{id}', [RestockController::class, 'show'])->name('product.restock.edit');
    Route::post('/product/restock/edit/{id}', [RestockController::class, 'update'])->name('product.restock.edit.data');
    Route::get('/product/restock/del/{id}', [RestockController::class, 'delete'])->name('product.restock.del');
    Route::post('/product/restock/del/{id}', [RestockController::class, 'destroy'])->name('product.restock.del.data');

    Route::get('/product/restock/pay/{id}', [RestockController::class, 'pay_index'])->name('product.restock.pay');
    Route::get('/product/restock/pay/create/{id}', [RestockController::class, 'pay_create'])->name('product.restock.pay.create');
    Route::post('/product/restock/pay/create/{id}', [RestockController::class, 'pay_store'])->name('product.restock.pay.create.data');
    Route::get('/product/restock/pay/edit/{id}', [RestockController::class, 'pay_edit'])->name('product.restock.pay.edit');
    Route::post('/product/restock/pay/edit/{id}', [RestockController::class, 'pay_update'])->name('product.restock.pay.edit.data');
    Route::get('/product/restock/pay/del/{id}', [RestockController::class, 'pay_delete'])->name('product.restock.pay.del');
    Route::post('/product/restock/pay/del/{id}', [RestockController::class, 'pay_destroy'])->name('product.restock.pay.del.data');

    /* 商品盤點管理 */
    Route::get('/product/inventorys', [InventoryController::class, 'index'])->name('product.inventorys');
    Route::get('/product/inventory/create', [InventoryController::class, 'create'])->name('product.inventory.create');
    Route::post('/product/inventory/create', [InventoryController::class, 'store'])->name('product.inventory.create.data');
    Route::get('/product/inventory/del/{id}', [InventoryController::class, 'delete'])->name('product.inventory.del');
    Route::post('/product/inventory/del/{id}', [InventoryController::class, 'destroy'])->name('product.inventory.del.data');
    Route::get('/product/inventoryItem/{product_inventory_id}', [InventoryController::class, 'inventoryItem_index'])->name('inventoryItem.edit');
    Route::post('/product/inventoryItem/{product_inventory_id}', [InventoryController::class, 'inventoryItem_edit'])->name('inventoryItem.edit.data');

    /* 業務管理 */
    Route::get('/sales', [SaleDataController::class, 'index'])->name('sales');
    Route::get('/sales/export', [SaleDataController::class, 'export'])->name('sales.export');
    
    Route::get('/sales/excel', [SaleDataController::class, 'excel'])->name('sales.excel');

    Route::get('/sale/create', [SaleDataController::class, 'create'])->name('sale.create');
    Route::get('/sale/create/test', [SaleDataController::class, 'test'])->name('sale.test');
    Route::post('/sale/create', [SaleDataController::class, 'store'])->name('sale.data.create');
    Route::get('/sale/edit/{id}', [SaleDataController::class, 'show'])->name('sale.edit');
    Route::post('/sale/edit/{id}', [SaleDataController::class, 'update'])->name('sale.data.edit');
    Route::get('/sale/del/{id}', [SaleDataController::class, 'delete'])->name('sale.del');
    Route::post('/sale/del/{id}', [SaleDataController::class, 'destroy'])->name('sale.data.del');
    Route::get('/sale/check/history', [SaleDataController::class, 'checkHistory'])->name('sales.checkHistory');
    Route::get('/sale/history/{id}', [SaleDataController::class, 'history'])->name('sale.history');

    Route::get('/sale/create/gpt', [SaleDataControllerNew::class, 'create_gpt'])->name('sale.create.gpt');
    Route::post('/sale/create/gpt', [SaleDataControllerNew::class, 'store_gpt'])->name('sale.data.create.gpt');

    //報廢單
    Route::get('/sale/scrapped/create', [ScrappedController::class, 'create'])->name('sale.scrapped.create');
    Route::post('/sale/scrapped/create', [ScrappedController::class, 'store'])->name('sale.scrapped.create.data');
    Route::get('/sale/scrapped/{id}/edit', [ScrappedController::class, 'edit'])->name('sale.scrapped.edit');
    Route::put('/sale/scrapped/{id}', [ScrappedController::class, 'update'])->name('sale.scrapped.update');
    Route::get('/sale/scrapped/{id}/delete', [ScrappedController::class, 'delete'])->name('sale.scrapped.delete');
    Route::delete('/sale/scrapped/{id}', [ScrappedController::class, 'destroy'])->name('sale.scrapped.destroy');
    Route::get('/sale/scrapped/{id}/check', [ScrappedController::class, 'check_show'])->name('sale.scrapped.check');
    Route::put('/sale/scrapped/{id}/check', [ScrappedController::class, 'check_data'])->name('sale.scrapped.check.data');

    // 業務確認對帳
    Route::get('/sale/check/{id}', [SaleDataController::class, 'check_show'])->name('sale.check');
    Route::post('/sale/check/{id}', [SaleDataController::class, 'check_update'])->name('sale.data.check');

    Route::get('/sale/show/{sale_on}', [SaleDataController::class, 'sale_on_show'])->name('sale.sale_on_show');
    // 業務轉單或是對拆
    Route::get('/sale/change/{id}', [SaleDataController::class, 'change_show'])->name('sale.change');
    Route::post('/sale/change/{id}', [SaleDataController::class, 'change_update'])->name('sale.data.change');
    Route::get('/sale/change_record/{id}', [SaleDataController::class, 'change_record'])->name('sale.change.record');

    Route::get('/sale/change_plan/{id}', [SaleDataController::class, 'change_plan_show'])->name('sale.change_plan');
    Route::post('/sale/change_plan/{id}', [SaleDataController::class, 'change_plan_update'])->name('sale.data.change_plan');
    // 尾款ajax
    Route::get('/sales/final_price', [SaleDataController::class, 'final_price'])->name('sales.final_price');

    Route::get('/prom/search', [SaleDataController::class, 'prom_search'])->name('prom.search');
    Route::get('/gdpaper/search', [SaleDataController::class, 'gdpaper_search'])->name('gdpaper.search');
    Route::get('/customer/search', [SaleDataController::class, 'customer_search'])->name('customer.search');
    Route::get('/company/search', [SaleDataController::class, 'company_search'])->name('company.search');
    Route::get('/customers/by-type', [SaleDataController::class, 'get_customers_by_type'])->name('customers.by-type');
    Route::get('/sale/check_sale_on', [SaleDataController::class, 'check_sale_on'])->name('sale.check_sale_on');
    Route::get('/sale/statistics', [SaleDataController::class, 'getOrderStatistics'])->name('sale.statistics');
    Route::get('wait/sales', [SaleDataController::class, 'wait_index'])->name('wait.sales');

    Route::get('user/{id}/sale', [SaleDataController::class, 'user_sale'])->name('user.sale');

    /* 來源管理 */
    Route::get('/sources', [SaleSourceController::class, 'index'])->name('sources');
    Route::get('/source/create', [SaleSourceController::class, 'create'])->name('source.create');
    Route::post('/source/create', [SaleSourceController::class, 'store'])->name('source.create.data');
    Route::get('/source/edit/{id}', [SaleSourceController::class, 'show'])->name('source.edit');
    Route::post('/source/edit/{id}', [SaleSourceController::class, 'update'])->name('source.edit.data');
    Route::get('/source/del/{id}', [SaleSourceController::class, 'delete'])->name('source.del');
    Route::post('/source/del/{id}', [SaleSourceController::class, 'destroy'])->name('source.del.data');

    /* 套裝管理 */
    Route::get('/suits', [SuitController::class, 'index'])->name('suits');
    Route::get('/suit/create', [SuitController::class, 'create'])->name('suit.create');
    Route::post('/suit/create', [SuitController::class, 'store'])->name('suit.create.data');
    Route::get('/suit/edit/{id}', [SuitController::class, 'show'])->name('suit.edit');
    Route::post('/suit/edit/{id}', [SuitController::class, 'update'])->name('suit.edit.data');
    Route::get('/suit/del/{id}', [SuitController::class, 'delete'])->name('suit.del');
    Route::post('/suit/del/{id}', [SuitController::class, 'destroy'])->name('suit.del.data');

    /* 方案管理 */
    Route::get('/plans', [PlanController::class, 'index'])->name('plans');
    Route::get('/plan/create', [PlanController::class, 'create'])->name('plan.create');
    Route::post('/plan/create', [PlanController::class, 'store'])->name('plan.create.data');
    Route::get('/plan/edit/{id}', [PlanController::class, 'show'])->name('plan.edit');
    Route::post('/plan/edit/{id}', [PlanController::class, 'update'])->name('plan.edit.data');
    Route::get('/plan/del/{id}', [PlanController::class, 'delete'])->name('plan.del');
    Route::post('/plan/del/{id}', [PlanController::class, 'destroy'])->name('plan.del.data');

    /* 後續處理類別管理 */
    Route::get('/prom_types', [PromTypeController::class, 'index'])->name('prom_types');
    Route::get('/prom_type/create', [PromTypeController::class, 'create'])->name('prom_type.create');
    Route::post('/prom_type/create', [PromTypeController::class, 'store'])->name('prom_type.create.data');
    Route::get('/prom_type/edit/{id}', [PromTypeController::class, 'show'])->name('prom_type.edit');
    Route::post('/prom_type/edit/{id}', [PromTypeController::class, 'update'])->name('prom_type.edit.data');

    /* 後續處理管理 */
    Route::get('/proms', [PromController::class, 'index'])->name('proms');
    Route::get('/prom/create', [PromController::class, 'create'])->name('prom.create');
    Route::post('/prom/create', [PromController::class, 'store'])->name('prom.create.data');
    Route::get('/prom/edit/{id}', [PromController::class, 'show'])->name('prom.edit');
    Route::post('/prom/edit/{id}', [PromController::class, 'update'])->name('prom.edit.data');

    /* 紀念品類別管理 */
    Route::get('/souvenir_types', [SouvenirTypeController::class, 'index'])->name('souvenir_types');
    Route::get('/souvenir_type/create', [SouvenirTypeController::class, 'create'])->name('souvenir_type.create');
    Route::post('/souvenir_type/create', [SouvenirTypeController::class, 'store'])->name('souvenir_type.create.data');
    Route::get('/souvenir_type/edit/{id}', [SouvenirTypeController::class, 'show'])->name('souvenir_type.edit');
    Route::post('/souvenir_type/edit/{id}', [SouvenirTypeController::class, 'update'])->name('souvenir_type.edit.data');
    Route::get('/souvenirType/search', [SouvenirTypeController::class, 'souvenirType_search'])->name('souvenirType.search');

    /* 紀念品管理 */
    Route::get('/souvenirs', [SouvenirController::class, 'index'])->name('souvenirs');
    Route::get('/souvenir/create', [SouvenirController::class, 'create'])->name('souvenir.create');
    Route::post('/souvenir/create', [SouvenirController::class, 'store'])->name('souvenir.create.data');
    Route::get('/souvenir/edit/{id}', [SouvenirController::class, 'show'])->name('souvenir.edit');
    Route::post('/souvenir/edit/{id}', [SouvenirController::class, 'update'])->name('souvenir.edit.data');
    Route::get('/souvenir/search', [SouvenirController::class, 'souvenir_search'])->name('souvenir.search');

    /* 廠商管理 */
    Route::get('/venders', [VenderController::class, 'index'])->name('venders');
    Route::get('/vender/create', [VenderController::class, 'create'])->name('vender.create');
    Route::post('/vender/create', [VenderController::class, 'store'])->name('vender.create.data');
    Route::get('/vender/edit/{id}', [VenderController::class, 'show'])->name('vender.edit');
    Route::post('/vender/edit/{id}', [VenderController::class, 'update'])->name('vender.edit.data');

    /* 收入科目管理 */
    Route::get('/income/sujects', [IncomeController::class, 'index'])->name('income.sujects');
    Route::get('/income/suject/create', [IncomeController::class, 'create'])->name('income.suject.create');
    Route::post('/income/suject/create', [IncomeController::class, 'store'])->name('income.suject.create.data');
    Route::get('/income/suject/edit/{id}', [IncomeController::class, 'show'])->name('income.suject.edit');
    Route::post('/income/suject/edit/{id}', [IncomeController::class, 'update'])->name('income.suject.edit.data');

    /* 收入管理 */
    Route::get('/income', [IncomeDataController::class, 'index'])->name('incomes');
    Route::get('/income/create', [IncomeDataController::class, 'create'])->name('income.create');
    Route::post('/income/create', [IncomeDataController::class, 'store'])->name('income.create.data');
    Route::get('/income/edit/{id}', [IncomeDataController::class, 'show'])->name('income.edit');
    Route::post('/income/edit/{id}', [IncomeDataController::class, 'update'])->name('income.edit.data');
    Route::get('/income/del/{id}', [IncomeDataController::class, 'delshow'])->name('income.del');
    Route::post('/income/del/{id}', [IncomeDataController::class, 'delete'])->name('income.del.data');

    /* 支出科目管理 */
    Route::get('/pay/sujects', [PayController::class, 'index'])->name('pay.sujects');
    Route::get('/pay/suject/create', [PayController::class, 'create'])->name('pay.suject.create');
    Route::post('/pay/suject/create', [PayController::class, 'store'])->name('pay.suject.create.data');
    Route::get('/pay/suject/edit/{id}', [PayController::class, 'show'])->name('pay.suject.edit');
    Route::post('/pay/suject/edit/{id}', [PayController::class, 'update'])->name('pay.suject.edit.data');

    /* 支出管理 */
    Route::get('user/{id}/pay', [PayDataController::class, 'user_pay'])->name('user.pay');
    Route::get('/pay', [PayDataController::class, 'index'])->name('pays');
    Route::get('/pay/create', [PayDataController::class, 'create'])->name('pay.create');
    Route::post('/pay/create', [PayDataController::class, 'store'])->name('pay.create.data');
    Route::get('/pay/edit/{id}', [PayDataController::class, 'show'])->name('pay.edit');
    Route::post('/pay/edit/{id}', [PayDataController::class, 'update'])->name('pay.edit.data');
    Route::get('/pay/del/{id}', [PayDataController::class, 'delshow'])->name('pay.del');
    Route::post('/pay/del/{id}', [PayDataController::class, 'delete'])->name('pay.del.data');
    Route::get('/pay/check/{id}', [PayDataController::class, 'check'])->name('pay.check');
    Route::post('/pay/check/{id}', [PayDataController::class, 'check_data'])->name('pay.check.data');
    Route::get('/pay/history/{id}', [PayDataController::class, 'history'])->name('pay.history');
    Route::post('/pay/export', [PayDataController::class, 'export'])->name('pay.export');

    /* 零用金管理 */
    Route::get('/cash', [CashController::class, 'index'])->name('cashs');
    Route::get('/cash/create', [CashController::class, 'create'])->name('cash.create');
    Route::post('/cash/create', [CashController::class, 'store'])->name('cash.create.data');
    Route::get('/cash/edit/{id}', [CashController::class, 'show'])->name('cash.edit');
    Route::post('/cash/edit/{id}', [CashController::class, 'update'])->name('cash.edit.data');

    Route::get('pay/vender/number', [VenderController::class, 'number'])->name('vender.number');

    /* 專員戶頭設定 */
    Route::get('/user/bank', [UserBankDataController::class, 'index'])->name('user.bank');
    Route::get('/user/bank/create', [UserBankDataController::class, 'create'])->name('user.bank.create');
    Route::post('/user/bank/create', [UserBankDataController::class, 'store'])->name('user.bank.create.data');
    Route::get('/user/bank/edit/{id}', [UserBankDataController::class, 'show'])->name('user.bank.edit');
    Route::post('/user/bank/edit/{id}', [UserBankDataController::class, 'update'])->name('user.bank.edit.data');

    /* 人事管理 */
    Route::get('personnels', [PersonnelController::class, 'index'])->name('personnels');

    /* 例假日總覽 */
    Route::get('personnel/holidays', [PersonnelController::class, 'holidays'])->name('personnel.holidays');
    Route::get('personnel/holiday/create', [PersonnelController::class, 'holiday_create'])->name('personnel.holidays.create');
    Route::post('personnel/holiday/create', [PersonnelController::class, 'holiday_store'])->name('personnel.holidays.create.data');
    Route::get('personnel/holiday/edit/{user_id}/{year}/{month}', [PersonnelController::class, 'holiday_edit'])->name('personnel.holidays.edit');
    Route::post('personnel/holiday/edit/{user_id}/{year}/{month}', [PersonnelController::class, 'holiday_update'])->name('personnel.holidays.edit.data');
    Route::get('personnel/other_holidays', [PersonnelController::class, 'other_holidays'])->name('personnel.other_holidays');

    /* 年度總休假管理 */
    Route::get('/vacation', [VacationController::class, 'index'])->name('vacations');
    Route::get('/vacation/create', [VacationController::class, 'create'])->name('vacation.create');
    Route::post('/vacation/create', [VacationController::class, 'store'])->name('vacation.create.data');
    Route::get('/vacation/edit/{year}', [VacationController::class, 'show'])->name('vacation.edit');
    Route::post('/vacation/edit/{year}', [VacationController::class, 'update'])->name('vacation.edit.data');

    // 年資設定
    Route::get('/SeniorityPauses/{user_id}', [SeniorityPausesController::class, 'index'])->name('SeniorityPausess');
    Route::get('/SeniorityPauses/{user_id}/create', [SeniorityPausesController::class, 'create'])->name('SeniorityPauses.create');
    Route::post('/SeniorityPauses/{user_id}/create', [SeniorityPausesController::class, 'store'])->name('SeniorityPauses.create.data');
    Route::get('/SeniorityPauses/{user_id}/edit/{id}', [SeniorityPausesController::class, 'show'])->name('SeniorityPauses.edit');
    Route::post('/SeniorityPauses/{user_id}/edit/{id}', [SeniorityPausesController::class, 'update'])->name('SeniorityPauses.edit.data');
    Route::get('/SeniorityPauses/{user_id}/del/{id}', [SeniorityPausesController::class, 'delete'])->name('SeniorityPauses.del');
    Route::post('/SeniorityPauses/{user_id}/del/{id}', [SeniorityPausesController::class, 'destroy'])->name('SeniorityPauses.del.data');

    /* 法會類別管理 */
    Route::get('/puja/type', [PujaTypeController::class, 'index'])->name('puja.types');
    Route::get('/puja/type/create', [PujaTypeController::class, 'create'])->name('puja.type.create');
    Route::post('/puja/type/create', [PujaTypeController::class, 'store'])->name('puja.type.create.data');
    Route::get('/puja/type/edit/{id}', [PujaTypeController::class, 'show'])->name('puja.type.edit');
    Route::post('/puja/type/edit/{id}', [PujaTypeController::class, 'update'])->name('puja.type.edit.data');

    /* 法會管理 */
    Route::get('/puja', [PujaController::class, 'index'])->name('pujas');

    Route::get('/puja/create', [PujaController::class, 'create'])->name('puja.create');
    Route::post('/puja/create', [PujaController::class, 'store'])->name('puja.create.data');
    Route::get('/puja/edit/{id}', [PujaController::class, 'show'])->name('puja.edit');
    Route::post('/puja/edit/{id}', [PujaController::class, 'update'])->name('puja.edit.data');

    /* 法會報名管理 */
    Route::get('/puja_data', [PujaDataController::class, 'index'])->name('puja_datas');
    Route::get('/puja_data/export', [PujaDataController::class, 'export'])->name('puja_datas.export');
    Route::get('/puja_data/create', [PujaDataController::class, 'create'])->name('puja_data.create');
    Route::post('/puja_data/create', [PujaDataController::class, 'store'])->name('puja_data.create.data');
    Route::get('/puja_data/edit/{id}', [PujaDataController::class, 'show'])->name('puja_data.edit');
    Route::post('/puja_data/edit/{id}', [PujaDataController::class, 'update'])->name('puja_data.edit.data');
    Route::get('/puja_data/del/{id}', [PujaDataController::class, 'delete'])->name('puja_data.del');
    Route::post('/puja_data/del/{id}', [PujaDataController::class, 'destroy'])->name('puja_data.del.data');
    Route::get('/customer/pet/search', [PujaDataController::class, 'customer_pet_search'])->name('customer.pet.search');
    Route::get('/puja/search', [PujaDataController::class, 'puja_search'])->name('puja.search');

    /* 合約類別管理 */
    Route::get('/contractType', [ContractTypeController::class, 'index'])->name('contractTypes');
    Route::get('/contractType/create', [ContractTypeController::class, 'create'])->name('contractType.create');
    Route::post('/contractType/create', [ContractTypeController::class, 'store'])->name('contractType.create.data');
    Route::get('/contractType/edit/{id}', [ContractTypeController::class, 'show'])->name('contractType.edit');
    Route::post('/contractType/edit/{id}', [ContractTypeController::class, 'update'])->name('contractType.edit.data');

    /* 合約管理 */
    Route::get('/contract', [ContractController::class, 'index'])->name('contracts');
    Route::get('/contract/export', [ContractController::class, 'export'])->name('contract.export');
    Route::get('/contract/create', [ContractController::class, 'create'])->name('contract.create');
    Route::post('/contract/create', [ContractController::class, 'store'])->name('contract.create.data');
    Route::get('/contract/edit/{id}', [ContractController::class, 'show'])->name('contract.edit');
    Route::post('/contract/edit/{id}', [ContractController::class, 'update'])->name('contract.edit.data');
    Route::get('/contract/del/{id}', [ContractController::class, 'delete'])->name('contract.del');
    Route::post('/contract/del/{id}', [ContractController::class, 'destroy'])->name('contract.del.data');

    /* 平安燈類別管理 */
    Route::get('/lampType', [LampTypeController::class, 'index'])->name('lampTypes');
    Route::get('/lampType/create', [LampTypeController::class, 'create'])->name('lampType.create');
    Route::post('/lampType/create', [LampTypeController::class, 'store'])->name('lampType.create.data');
    Route::get('/lampType/edit/{id}', [LampTypeController::class, 'show'])->name('lampType.edit');
    Route::post('/lampType/edit/{id}', [LampTypeController::class, 'update'])->name('lampType.edit.data');

    /* 平安燈管理 */
    Route::get('/lamp', [LampController::class, 'index'])->name('lamps');
    Route::get('/lamp/export', [LampController::class, 'export'])->name('lamp.export');
    Route::get('/lamp/create', [LampController::class, 'create'])->name('lamp.create');
    Route::post('/lamp/create', [LampController::class, 'store'])->name('lamp.create.data');
    Route::get('/lamp/edit/{id}', [LampController::class, 'show'])->name('lamp.edit');
    Route::post('/lamp/edit/{id}', [LampController::class, 'update'])->name('lamp.edit.data');
    Route::get('/lamp/del/{id}', [LampController::class, 'delete'])->name('lamp.del');
    Route::post('/lamp/del/{id}', [LampController::class, 'destroy'])->name('lamp.del.data');

    /* 報表管理 */
    // RPG 報表路由 - 根據不同權限分組

    // 公開報表 - 所有員工可訪問
    Route::middleware(['auth', 'rpg.flexible:public'])->group(function () {
        Route::get('/rpg/group', [Rpg07Controller::class, 'rpg07'])->name('rpg07');
        Route::get('/rpg/group/export', [Rpg07Controller::class, 'export'])->name('rpg07.export');
    });

    // 管理權限報表 - 主管以上可訪問
    Route::middleware(['auth', 'rpg.flexible:management'])->group(function () {
        Route::get('/rpg/rpg12', [Rpg12Controller::class, 'rpg12'])->name('rpg12');
        Route::get('/rpg/rpg01', [Rpg01Controller::class, 'rpg01'])->name('rpg01');
        Route::get('/rpg/rpg01/detail/{date}/{plan_id}', [Rpg01Controller::class, 'detail'])->name('rpg01.detail');
        Route::get('/rpg/rpg04', [Rpg04Controller::class, 'rpg04'])->name('rpg04');
        Route::get('/rpg/rpg06', [Rpg06Controller::class, 'rpg06'])->name('rpg06');  // 舊法會查詢
        Route::get('/rpg/rpg13', [Rpg13Controller::class, 'rpg13'])->name('rpg13');
        Route::get('/rpg/rpg09', [Rpg09Controller::class, 'rpg09'])->name('rpg09');
        Route::get('/rpg/rpg10', [Rpg10Controller::class, 'rpg10'])->name('rpg10');
        Route::get('/rpg/rpg15', [Rpg15Controller::class, 'rpg15'])->name('rpg15');
        Route::get('/rpg/rpg16', [Rpg16Controller::class, 'rpg16'])->name('rpg16');
        Route::get('/rpg/rpg16/{month}/{prom_id}/detail', [Rpg16Controller::class, 'detail'])->name('rpg16.detail');
        Route::get('/rpg/rpg17', [Rpg17Controller::class, 'rpg17'])->name('rpg17');
        Route::get('/rpg/rpg17/{month}/{prom_id}/detail', [Rpg17Controller::class, 'detail'])->name('rpg17.detail');
        Route::get('/rpg/rpg25', [Rpg25Controller::class, 'rpg25'])->name('rpg25');
        Route::get('/rpg/rpg25/{month}/{prom_id}/detail', [Rpg25Controller::class, 'detail'])->name('rpg25.detail');
        Route::get('/rpg/rpg22', [Rpg22Controller::class, 'rpg22'])->name('rpg22');
        Route::get('/rpg/rpg22/{month}/{prom_id}/detail', [Rpg22Controller::class, 'detail'])->name('rpg22.detail');
        Route::get('/rpg/rpg21', [Rpg21Controller::class, 'rpg21'])->name('rpg21');
        Route::get('/rpg/rpg27', [Rpg27Controller::class, 'rpg27'])->name('rpg27');
        Route::get('/rpg/rpg14', [Rpg14Controller::class, 'rpg14'])->name('rpg14');
        Route::get('/rpg/rpg23', [Rpg23Controller::class, 'rpg23'])->name('rpg23');
        Route::get('/rpg/rpg23/detail/{district}', [Rpg23Controller::class, 'detail'])->name('rpg23.detail');
        Route::get('/rpg/rpg24', [Rpg24Controller::class, 'rpg24'])->name('rpg24');
        Route::get('/rpg/rpg14/detail/{date}/{source_code}', [Rpg14Controller::class, 'detail'])->name('rpg14.detail');
        Route::get('/rpg/rpg14/month/detail/{month}/{source_code}', [Rpg14Controller::class, 'month_detail'])->name('rpg14.month.detail');
        Route::get('/rpg/rpg27/{year}/{month}/{source_id}/{company_id}/detail', [Rpg27Controller::class, 'detail'])->name('rpg27.detail');
        Route::get('/rpg/rpg31', [Rpg31Controller::class, 'rpg31'])->name('rpg31');
        Route::get('/rpg/rpg31/{month}/{lamp_type}/detail', [Rpg31Controller::class, 'detail'])->name('rpg31.detail');
        Route::get('/rpg/rpg30', [Rpg30Controller::class, 'rpg30'])->name('rpg30');
        Route::get('/rpg/rpg30/detail/{month}/{type}', [Rpg30Controller::class, 'detail'])->name('rpg30.detail');
        Route::get('/rpg/rpg30/detail/suit/{season_start}/{season_end}/{suit_id}', [Rpg30Controller::class, 'season_suit_detail'])->name('rpg30.season.suit.detail');
        Route::get('/rpg/rpg30/detail/urn-souvenir/{season_start}/{season_end}/{urn_souvenir}', [Rpg30Controller::class, 'season_urn_souvenir_detail'])->name('rpg30.season.urn_souvenir.detail');
        Route::get('/rpg/rpg33', [Rpg33Controller::class, 'index'])->name('rpg33');
        Route::get('/rpg/rpg33/export', [Rpg33Controller::class, 'export'])->name('rpg33.export');
        
        // 火化爐管理路由
        Route::prefix('crematorium')->name('crematorium.')->group(function () {
            // 設備管理
            Route::get('/', [CrematoriumController::class, 'index'])->name('index');
            Route::get('/create', [CrematoriumController::class, 'create'])->name('create');
            Route::post('/store', [CrematoriumController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CrematoriumController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [CrematoriumController::class, 'update'])->name('update');
            Route::delete('/{id}', [CrematoriumController::class, 'destroy'])->name('destroy');
            
            // 預約管理
            Route::get('/bookings', [CrematoriumController::class, 'bookings'])->name('bookings');
            Route::get('/bookings/create', [CrematoriumController::class, 'createBooking'])->name('createBooking');
            Route::post('/bookings/store', [CrematoriumController::class, 'storeBooking'])->name('storeBooking');
            
            // 維護記錄管理
            Route::get('/maintenance', [CrematoriumController::class, 'maintenance'])->name('maintenance');
            Route::get('/maintenance/create', [CrematoriumController::class, 'createMaintenance'])->name('createMaintenance');
            Route::post('/maintenance/store', [CrematoriumController::class, 'storeMaintenance'])->name('storeMaintenance');
        });
    });

    // 1. 高權限報表 - 只有主管以上可以訪問
    Route::middleware(['auth', 'rpg.flexible:restricted'])->group(function () {
        Route::get('/rpg/rpg02', [Rpg02Controller::class, 'rpg02'])->name('rpg02');
        Route::get('/rpg/rpg02/detail/{after_date}/{before_date}/{pay_id}', [Rpg02Controller::class, 'detail'])->name('rpg02.detail');
        Route::get('/rpg/rpg05', [Rpg05Controller::class, 'rpg05'])->name('rpg05');
        Route::get('/rpg/rpg06/export', [Rpg06Controller::class, 'export'])->name('rpg06.export');  // 舊法會查詢
        Route::get('/rpg/rpg11', [Rpg11Controller::class, 'rpg11'])->name('rpg11');
        
       
        Route::get('/rpg/rpg18', [Rpg18Controller::class, 'rpg18'])->name('rpg18');
        Route::get('/rpg/rpg19', [Rpg19Controller::class, 'rpg19'])->name('rpg19');
        Route::get('/rpg/rpg20', [Rpg20Controller::class, 'rpg20'])->name('rpg20');
        
        Route::get('/rpg/rpg26', [Rpg26Controller::class, 'rpg26'])->name('rpg26');
        Route::get('/rpg/rpg28', [Rpg28Controller::class, 'rpg28'])->name('rpg28');
        Route::get('/rpg/rpg29', [Rpg29Controller::class, 'rpg29'])->name('rpg29');
        
        
        Route::get('/rpg/rpg32', [Rpg32Controller::class, 'rpg32'])->name('rpg32');
    });

    // 2. 公開報表 - 所有員工都可以訪問（示例：rpg10）
    Route::middleware(['auth', 'rpg.flexible:public'])->group(function () {
        Route::get('/rpg/rpg10', [Rpg10Controller::class, 'rpg10'])->name('rpg10');
    });

    // 達標類別
    Route::get('/targetCategory', [TargetCategoriesController::class, 'index'])->name('targetCategories');
    Route::get('/targetCategory/create', [TargetCategoriesController::class, 'create'])->name('targetCategory.create');
    Route::post('/targetCategory/create', [TargetCategoriesController::class, 'store'])->name('targetCategory.create.data');
    Route::get('/targetCategory/edit/{id}', [TargetCategoriesController::class, 'show'])->name('targetCategory.edit');
    Route::post('/targetCategory/edit/{id}', [TargetCategoriesController::class, 'update'])->name('targetCategory.edit.data');
    Route::get('/targetCategory/del/{id}', [TargetCategoriesController::class, 'delete'])->name('targetCategory.del');
    Route::post('/targetCategory/del/{id}', [TargetCategoriesController::class, 'destroy'])->name('targetCategory.del.data');

    // 達標管理
    Route::get('/target', [TargetController::class, 'index'])->name('target');
    Route::get('/target/create', [TargetController::class, 'create'])->name('target.create');
    Route::post('/target/create', [TargetController::class, 'store'])->name('target.create.data');
    Route::get('/target/edit/{id}', [TargetController::class, 'show'])->name('target.edit');
    Route::post('/target/edit/{id}', [TargetController::class, 'update'])->name('target.edit.data');
    Route::get('/target/del/{id}', [TargetController::class, 'delete'])->name('target.del');
    Route::post('/target/del/{id}', [TargetController::class, 'destroy'])->name('target.del.data');
    Route::put('/target-item/{id}', [TargetItemController::class, 'update'])->name('target_item.update');

    // 待辦管理
    Route::get('/task', [TaskController::class, 'index'])->name('task');
    Route::get('/task/create', [TaskController::class, 'create'])->name('task.create');
    Route::post('/task/create', [TaskController::class, 'store'])->name('task.create.data');
    Route::post('/task/ajax/create', [TaskController::class, 'ajax_store'])->name('task.ajax.create.data');
    Route::post('/task/check', [TaskController::class, 'check'])->name('task.create.check');
    Route::post('/task-item/complete', [TaskController::class, 'check'])->name('task.item.complete');
    Route::get('/task/edit/{id}', [TaskController::class, 'show'])->name('task.edit');
    Route::post('/task/edit/{id}', [TaskController::class, 'update'])->name('task.edit.data');
    Route::get('/task/del/{id}', [TaskController::class, 'delete'])->name('task.del');
    Route::post('/task/del/{id}', [TaskController::class, 'destroy'])->name('task.del.data');

    // 選單管理
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/sub/{parentId}', [MenuController::class, 'subMenu'])->name('menu.sub');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu/create', [MenuController::class, 'store'])->name('menu.create.data');
    Route::get('/menu/edit/{id}', [MenuController::class, 'show'])->name('menu.edit');
    Route::post('/menu/edit/{id}', [MenuController::class, 'update'])->name('menu.edit.data');
    Route::post('/menu/update-order', [MenuController::class, 'updateOrder'])->name('menu.updateOrder');
    Route::post('/menu/delete/{id}', [MenuController::class, 'destroy'])->name('menu.delete');

    // 權限選單管理
    Route::get('/job-menu', [JobMenuController::class, 'index'])->name('job.menu.index');
    Route::get('/job-menu/create', [JobMenuController::class, 'create'])->name('job.menu.create');
    Route::post('/job-menu/create', [JobMenuController::class, 'store'])->name('job.menu.create.data');
    Route::get('/job-menu/edit/{id}', [JobMenuController::class, 'show'])->name('job.menu.edit');
    Route::post('/job-menu/edit/{id}', [JobMenuController::class, 'update'])->name('job.menu.edit.data');

    Route::get('/liff', [LiffController::class, 'index'])->name('liff.index');
    Route::get('/api/banks/{bankCode}/branches', [BankController::class, 'getBranches']);

    Route::get('/online-columbarium', [OnlineColumbariumController::class, 'index'])->name('columbarium.index');

    //儲戶
    Route::get('/deregistration', [DeregistrationController::class, 'index'])->name('deregistration.index');
    Route::get('/deregistration/create', [DeregistrationController::class, 'create'])->name('deregistration.create');
    Route::post('/deregistration/create', [DeregistrationController::class, 'store'])->name('deregistration.create.data');
    Route::get('/deregistration/edit/{id}', [DeregistrationController::class, 'edit'])->name('deregistration.edit');
    Route::post('/deregistration/edit/{id}', [DeregistrationController::class, 'update'])->name('deregistration.edit.data');
    Route::get('/deregistration/del/{id}', [DeregistrationController::class, 'delete'])->name('deregistration.del');
    Route::post('/deregistration/del/{id}', [DeregistrationController::class, 'destroy'])->name('deregistration.del.data');

    //功德件
    Route::get('/merit', [MeritController::class, 'index'])->name('merit.index');
    Route::get('/merit/create', [MeritController::class, 'create'])->name('merit.create');
    Route::post('/merit/create', [MeritController::class, 'store'])->name('merit.create.data');
    Route::get('/merit/edit/{id}', [MeritController::class, 'show'])->name('merit.edit');
    Route::post('/merit/edit/{id}', [MeritController::class, 'update'])->name('merit.edit.data');
    Route::get('/merit/del/{id}', [MeritController::class, 'delete'])->name('merit.del');
    Route::post('/merit/del/{id}', [MeritController::class, 'destroy'])->name('merit.del.data');


    //贈送管理
    Route::get('/give', [GiveController::class, 'index'])->name('give.index');
    Route::get('/give/create', [GiveController::class, 'create'])->name('give.create');
    Route::post('/give/create', [GiveController::class, 'store'])->name('give.create.data');
    Route::get('/give/edit/{id}', [GiveController::class, 'edit'])->name('give.edit');
    Route::post('/give/edit/{id}', [GiveController::class, 'update'])->name('give.edit.data');
    Route::get('/give/del/{id}', [GiveController::class, 'delete'])->name('give.del');
    Route::post('/give/del/{id}', [GiveController::class, 'destroy'])->name('give.del.data');

    //加成管理
    Route::get('/increase', [IncreaseController::class, 'index'])->name('increase.index');
    Route::get('/increase/create', [IncreaseController::class, 'create'])->name('increase.create');
    Route::post('/increase/create', [IncreaseController::class, 'store'])->name('increase.create.data');
    Route::get('/increase/edit/{id}', [IncreaseController::class, 'edit'])->name('increase.edit');
    Route::put('/increase/edit/{id}', [IncreaseController::class, 'update'])->name('increase.edit.data');
    Route::get('/increase/del/{id}', [IncreaseController::class, 'delete'])->name('increase.del');
    Route::delete('/increase/del/{id}', [IncreaseController::class, 'destroy'])->name('increase.del.data');
    Route::get('/increase/export', [IncreaseController::class, 'export'])->name('increase.export');

//加班管理
Route::get('/overtime', [OvertimeController::class, 'index'])->name('overtime.index');
Route::get('/overtime/create', [OvertimeController::class, 'create'])->name('overtime.create');
Route::post('/overtime/create', [OvertimeController::class, 'store'])->name('overtime.create.data');
Route::get('/overtime/edit/{id}', [OvertimeController::class, 'edit'])->name('overtime.edit');
Route::put('/overtime/edit/{id}', [OvertimeController::class, 'update'])->name('overtime.edit.data');
Route::get('/overtime/del/{id}', [OvertimeController::class, 'delete'])->name('overtime.del');
Route::delete('/overtime/del/{id}', [OvertimeController::class, 'destroy'])->name('overtime.del.data');
Route::get('/overtime/approve/{id}', [OvertimeController::class, 'approve'])->name('overtime.approve');
Route::post('/overtime/reject/{id}', [OvertimeController::class, 'reject'])->name('overtime.reject');
Route::get('/overtime/export', [OvertimeController::class, 'export'])->name('overtime.export');


    Route::get('image', function () {
        $img = Image::make('https://images.pexels.com/photos/4273439/pexels-photo-4273439.jpeg')->resize(300, 200);  // 這邊可以隨便用網路上的image取代
        return $img->response('jpg');
    });


});
