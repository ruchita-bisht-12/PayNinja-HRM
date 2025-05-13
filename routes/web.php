<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changepassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/blank-page', [App\Http\Controllers\HomeController::class, 'blank'])->name('blank');

    Route::get('/hakakses', [App\Http\Controllers\HakaksesController::class, 'index'])->name('hakakses.index')->middleware('role');
    Route::get('/hakakses/edit/{id}', [App\Http\Controllers\HakaksesController::class, 'edit'])->name('hakakses.edit')->middleware('role');
    Route::put('/hakakses/update/{id}', [App\Http\Controllers\HakaksesController::class, 'update'])->name('hakakses.update')->middleware('role');
    Route::delete('/hakakses/delete/{id}', [App\Http\Controllers\HakaksesController::class, 'destroy'])->name('hakakses.delete')->middleware('role');

    Route::get('/table-example', [App\Http\Controllers\ExampleController::class, 'table'])->name('table.example');
    Route::get('/clock-example', [App\Http\Controllers\ExampleController::class, 'clock'])->name('clock.example');
    Route::get('/chart-example', [App\Http\Controllers\ExampleController::class, 'chart'])->name('chart.example');
    Route::get('/form-example', [App\Http\Controllers\ExampleController::class, 'form'])->name('form.example');
    Route::get('/map-example', [App\Http\Controllers\ExampleController::class, 'map'])->name('map.example');
    Route::get('/calendar-example', [App\Http\Controllers\ExampleController::class, 'calendar'])->name('calendar.example');
    Route::get('/gallery-example', [App\Http\Controllers\ExampleController::class, 'gallery'])->name('gallery.example');
    Route::get('/todo-example', [App\Http\Controllers\ExampleController::class, 'todo'])->name('todo.example');
    Route::get('/contact-example', [App\Http\Controllers\ExampleController::class, 'contact'])->name('contact.example');
    Route::get('/faq-example', [App\Http\Controllers\ExampleController::class, 'faq'])->name('faq.example');
    Route::get('/news-example', [App\Http\Controllers\ExampleController::class, 'news'])->name('news.example');
    Route::get('/about-example', [App\Http\Controllers\ExampleController::class, 'about'])->name('about.example');



 // SuperAdmin Routes (Can manage Companies)
 Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('companies', SuperAdminController::class)->except(['show']);
});

Route::middleware(['role:admin'])->prefix('company')->name('company.')->group(function () {
    Route::get('companies/{companyId}/employees', [CompanyController::class, 'index'])->name('employees.index');
    Route::get('companies/{companyId}/employees/create', [CompanyController::class, 'create'])->name('employees.create');
    Route::post('companies/{companyId}/employees', [CompanyController::class, 'store'])->name('employees.store');
    Route::get('companies/{companyId}/employees/{employeeId}/edit', [CompanyController::class, 'edit'])->name('employees.edit');
    Route::put('companies/{companyId}/employees/{employeeId}', [CompanyController::class, 'update'])->name('employees.update');
    Route::delete('companies/{companyId}/employees/{employeeId}', [CompanyController::class, 'destroy'])->name('employees.destroy');
});




// Employee Routes (Only employee can view their own profile)
Route::middleware(['role:user'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('profile', [EmployeeController::class, 'show'])->name('profile');
});

});