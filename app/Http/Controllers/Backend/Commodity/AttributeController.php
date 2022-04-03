<?php
/**
 * 属性管理
 *
 * @author Raj Luo
 */

namespace App\Http\Controllers\Backend\Commodity;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lyd3e\Lbcp\Safety\Lyd3e;

class AttributeController extends Lyd3e
{
    /**
     * 创建指定类目的自定义规格组（可批量）
     *
     * @param [{"cid": 2073,"name": "规格组1"},{"cid": 2073,"name": "规格组2"}]
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function CreatesACustomSpecificationGroupForTheSpecifiedCategory()
    {
        $params = $this->params;

        if(count($params) != 0){
            foreach ($params as $k=>&$v){
                $validator = Validator::make($v, [
                    'cid'  => 'required|integer',
                    'name' => 'required|string|max:20'
                ]);

                if ($validator->fails()) {
                    return $this->responseHandler('A0400');
                }

                try {
                    DB::table('spec_group')->insert($v);

                } catch (Exception $e) {
                    return $this->responseHandler('C0300', '指定类目的自定义规格组创建失败');
                }
            }
            return $this->responseHandler('00000', '指定类目的自定义规格组创建成功');
        }
    }

    /**
     * 编辑指定规格组
     *
     * @param {"gid": 13,"cid": 2073,"name": "规格组A"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function EditTheSpecifiedSpecificationGroup()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'gid'  => 'required|integer',
            'cid'  => 'required|integer',
            'name' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        try {
            DB::table('spec_group')->where(['gid'=>$params['gid'], 'cid'=>$params['cid']])->update(['name'=>$params['name']]);

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定规格组编辑失败');
        }

        return $this->responseHandler('00000', '指定规格组编辑成功');
    }

    /**
     * 删除指定规格组
     *
     * @param [{"gid": 13},{"gid": 14}]
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function DeletesTheSpecifiedSpecificationGroup()
    {
        $params = $this->params;

        if(count($params) != 0){
            foreach ($params as $k=>&$v){
                $validator = Validator::make($v, [
                    'gid' => 'required|integer'
                ]);

                if ($validator->fails()) {
                    return $this->responseHandler('A0400');
                }

                /*
                 * 删除前的验证
                 */

                try {
                    DB::table('spec_group')->where($v)->delete();

                } catch (Exception $e) {
                    return $this->responseHandler('C0300', '指定规格组删除失败');
                }
            }
            return $this->responseHandler('00000', '指定规格组删除成功');
        }
    }

    /**
     * 创建指定规格组的自定义属性
     *
     * @param [{"cid": 2073,"gid": 13,"name": "参数1","isnumeric": 1,"unit": "参数1单位","isgeneric": 1,"searchable": 0,"segment": ""},{"cid": 2073,"gid": 13,"name": "参数2","isnumeric": 0,"unit": "","isgeneric": 1,"searchable": 0,"segment": ""}]
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function CreatesCustomPropertiesForTheSpecifiedSpecificationGroup()
    {
        $params = $this->params;

        if(count($params) != 0){
            foreach ($params as $k=>&$v){
                $validator = Validator::make($v, [
                    'cid'        => 'required|integer',
                    'gid'        => 'required|integer',
                    'name'       => 'required|string|max:20',
                    'isnumeric'  => 'required|in:0,1',
                    'isgeneric'  => 'required|in:0,1',
                    'searchable' => 'required|in:0,1',
                ]);

                if ($validator->fails()) {
                    return $this->responseHandler('A0400');
                }

                if ($v['isnumeric'] == 1) {
                    if (empty($v['unit'])) {
                        return $this->responseHandler('A0400');
                    }
                }

                try {
                    DB::table('spec_attribute')->insert($v);

                } catch (Exception $e) {
                    return $this->responseHandler('C0300', '指定规格组的自定义属性创建失败');
                }
            }
            return $this->responseHandler('00000', '指定规格组的自定义属性创建成功');
        }
    }

    /**
     * 编辑指定属性
     *
     * @param {"pid": 88,"cid": 2073,"gid": 13,"name": "参数B","isnumeric": 1,"unit": "参数B单位","isgeneric": 0,"searchable": 1,"segment": "0-1,2-5,6-9"}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function EditTheSpecifiedProperties()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'pid'        => 'required|integer',
            'cid'        => 'required|integer',
            'gid'        => 'required|integer',
            'name'       => 'required|string|max:20',
            'isnumeric'  => 'required|in:0,1',
            'isgeneric'  => 'required|in:0,1',
            'searchable' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        if ($params['isnumeric'] == 1) {
            if (empty($params['unit'])) {
                return $this->responseHandler('A0400');
            }
        }

        $where = [
            'pid'=>$params['pid'],
            'gid'=>$params['gid'],
            'cid'=>$params['cid']
        ];

        $update = [
            'name'=>$params['name'],
            'isnumeric'=>$params['isnumeric'],
            'unit'=>$params['unit'],
            'isgeneric'=>$params['isgeneric'],
            'searchable'=>$params['searchable'],
            'segment'=>$params['segment']
        ];

        try {
            DB::table('spec_attribute')->where($where)->update($update);

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定属性编辑失败');
        }

        return $this->responseHandler('00000', '指定属性编辑成功');
    }

    /**
     * 删除指定属性
     *
     * @param [{"pid": 87},{"pid": 88}]
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function DeletesTheSpecifiedAttribute()
    {
        $params = $this->params;

        if(count($params) != 0){
            foreach ($params as $k=>&$v){
                $validator = Validator::make($v, [
                    'pid' => 'required|integer'
                ]);

                if ($validator->fails()) {
                    return $this->responseHandler('A0400');
                }

                /*
                 * 删除前的验证
                 */

                try {
                    DB::table('spec_attribute')->where($v)->delete();

                } catch (Exception $e) {
                    return $this->responseHandler('C0300', '指定属性删除失败');
                }
            }
            return $this->responseHandler('00000', '指定属性删除成功');
        }
    }

    /**
     * 获取指定类目的规格组列表
     *
     * @param {"ppcid": 21,"pcid": 207,"cid": 2073}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function GetsTheListOfSpecificationGroupsForTheSpecifiedCategory()
    {
        $params = $this->params;
        $result = [];

        $validator = Validator::make($params, [
            'ppcid' => 'integer',
            'pcid'  => 'integer',
            'cid'   => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        if (!empty($params['ppcid'])) {
            try {
                $result_1 = DB::table('spec_group')->where('cid', $params['ppcid'])->get()->toArray();

            } catch (Exception $e) {
                return $this->responseHandler('C0300', '[ppcid]指定类目的规格组列表获取失败');
            }

            $result = array_merge($result, $result_1);
        }

        if (!empty($params['pcid'])) {
            try {
                $result_2 = DB::table('spec_group')->where('cid', $params['pcid'])->get()->toArray();

            } catch (Exception $e) {
                return $this->responseHandler('C0300', '[pcid]指定类目的规格组列表获取失败');
            }

            $result = array_merge($result, $result_2);
        }

        try {
            $result_3 = DB::table('spec_group')->where('cid', $params['cid'])->get()->toArray();

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定类目的规格组列表获取失败');
        }

        $result = array_merge($result, $result_3);

        return $this->responseHandler('00000', '指定类目的规格组列表获取成功', $result);
    }

    /**
     * 获取指定规格组的属性列表
     *
     * @param {"cid": 2073,"gid": 1}
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function GetsThePropertyListForTheSpecifiedSpecificationGroup()
    {
        $params = $this->params;

        $validator = Validator::make($params, [
            'cid' => 'required|integer',
            'gid' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->responseHandler('A0400');
        }

        try {
            $result = DB::table('spec_attribute')->where($params)->get()->toArray();

        } catch (Exception $e) {
            return $this->responseHandler('C0300', '指定规格组的属性列表获取失败');
        }

        return $this->responseHandler('00000', '指定规格组的属性列表获取成功', $result);
    }
}