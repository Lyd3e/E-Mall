<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//参数加密
Route::match(['get','post'],'/getEncryptParams', 'Common\EncryptController@EncryptParams');

//商品类目管理
Route::match(['get','post'],'/category/create', 'Backend\Commodity\CategoryController@CreateACategory');
Route::match(['get','post'],'/category/delete', 'Backend\Commodity\CategoryController@DeleteTheCategory');
Route::match(['get','post'],'/category/edit', 'Backend\Commodity\CategoryController@EditCategory');
Route::match(['get','post'],'/category/get_sublist', 'Backend\Commodity\CategoryController@GetsAListOfSubcategoriesForTheSpecifiedCategory');
Route::match(['get','post'],'/category/validity_reversal', 'Backend\Commodity\CategoryController@SpecifyCategoryValidityInversion');

//商品属性管理
Route::match(['get','post'],'/attribute/create_group', 'Backend\Commodity\AttributeController@CreatesACustomSpecificationGroupForTheSpecifiedCategory');
Route::match(['get','post'],'/attribute/edit_group', 'Backend\Commodity\AttributeController@EditTheSpecifiedSpecificationGroup');
Route::match(['get','post'],'/attribute/delete_group', 'Backend\Commodity\AttributeController@DeletesTheSpecifiedSpecificationGroup');
Route::match(['get','post'],'/attribute/create_attribute', 'Backend\Commodity\AttributeController@CreatesCustomPropertiesForTheSpecifiedSpecificationGroup');
Route::match(['get','post'],'/attribute/edit_attribute', 'Backend\Commodity\AttributeController@EditTheSpecifiedProperties');
Route::match(['get','post'],'/attribute/delete_attribute', 'Backend\Commodity\AttributeController@DeletesTheSpecifiedAttribute');
Route::match(['get','post'],'/attribute/get_group_list', 'Backend\Commodity\AttributeController@GetsTheListOfSpecificationGroupsForTheSpecifiedCategory');
Route::match(['get','post'],'/attribute/get_attribute_list', 'Backend\Commodity\AttributeController@GetsThePropertyListForTheSpecifiedSpecificationGroup');

