<?php
/**
 * 类目管理
 *
 * @author Raj Luo
 */

namespace App\Http\Controllers\Backend\Commodity;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lyd3e\Lbcp\Safety\Lyd3e;
use phpDocumentor\Reflection\Element;

class CategoryController extends Lyd3e
{
    /**
     * 创建一个类目
     *
     * @param {"pcid": "207","name": "核动力汽车","isvalid": "1"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function CreateACategory()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'pcid'    => 'required|integer',
            'name'    => 'required|string|max:20',
            'isvalid' => 'required|in:1,-1'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        $params['create_time'] = $params['update_time'] = date("Y-m-d H:i:s", time());

        try {
            DB::table('category')->insert($params);

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '类目创建失败');
        }

        return $this->responseHandler('00000', '类目创建成功');
    }

    /**
     * 删除指定类目
     *
     * @param {"cid": "2073","name": "核动力汽车"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function DeleteTheCategory()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'cid'  => 'required|integer',
            'name' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        $result = DB::table('spu')
            ->whereRaw("isvalid = 1 and (cid_1st = '".$params['cid']."' or cid_2nd = '".$params['cid']."' or cid_3rd = '".$params['cid']."')")
            ->get(['cid_1st', 'cid_2nd', 'cid_3rd'])->toArray();

        if (!empty($result)) {
            return $this->responseHandler('A0440', '指定类目使用中，禁止删除操作');
        }

        $update_time = date("Y-m-d H:i:s", time());

        try {
            DB::table('category')->where($params)->update(['isdel' => 1, 'update_time' => $update_time]);

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定类目删除失败');
        }

        return $this->responseHandler('00000', '指定类目删除成功');
    }

    /**
     * 编辑指定类目
     *
     * @param {"cid": "2073","pcid": "207","name": "核动力汽车"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function EditCategory()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'cid'  => 'required|integer',
            'pcid' => 'required|integer',
            'name' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        $update_column = [
            'name'        => $params['name'],
            'update_time' => date("Y-m-d H:i:s", time())
        ];

        try {
            DB::table('category')->where(['cid' => $params['cid'], 'pcid' => $params['pcid'], 'isdel' => -1])->update($update_column);

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定类目编辑失败');
        }

        return $this->responseHandler('00000', '指定类目编辑成功');
    }

    /**
     * 获取指定类目的子类目列表
     *
     * @param {"pcid": "207"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function GetsAListOfSubcategoriesForTheSpecifiedCategory()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'pcid' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        try {
            $result = DB::table('category')->where(['pcid' => $params['pcid'], 'isdel' => -1])->get(['cid', 'name', 'isvalid'])->toArray();

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定类目的子类目列表获取失败');
        }

        return $this->responseHandler('00000', '指定类目的子类目列表获取成功', $result);
    }

    /**
     * 指定类目有效性反转
     *
     * @param {"cid": "2073","name": "核动力汽车"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function SpecifyCategoryValidityInversion()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'cid'  => 'required|integer',
            'name' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        $result = DB::table('category')->where($params)->get(['isvalid']);

        if ($result[0]->isvalid == 1) {
            $result = DB::table('spu')
                ->whereRaw("isvalid = 1 and (cid_1st = '".$params['cid']."' or cid_2nd = '".$params['cid']."' or cid_3rd = '".$params['cid']."')")
                ->get(['cid_1st', 'cid_2nd', 'cid_3rd'])->toArray();

            if (!empty($result)) {
                return $this->responseHandler('A0440', '指定类目使用中，禁止失效操作');
            }

            $isvalid = -1;

        } else $isvalid = 1;

        $update_time = date("Y-m-d H:i:s", time());

        try {
            DB::table('category')->where($params)->update(['isvalid' => $isvalid, 'update_time' => $update_time]);

        } catch (Exception $e) {
            if ($isvalid == 1) {
                return $this->responseHandler('C0300', '指定类目生效失败');

            } else return $this->responseHandler('C0300', '指定类目失效失败');

        }

        if ($isvalid == 1) {
            return $this->responseHandler('00000', '指定类目生效成功');

        } else return $this->responseHandler('00000', '指定类目失效成功');
    }
}