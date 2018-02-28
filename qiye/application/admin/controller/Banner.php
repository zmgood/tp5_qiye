<?php

namespace app\admin\controller;

use app\admin\common\Base;

use think\Request;
use app\admin\model\Banner as BannerModel;

class Banner extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //1.获取所有数据记录
        $banner=BannerModel::all();
        $count=BannerModel::count();

        //2.模板赋值
        $this->view->assign('banner',$banner);
        $this->view->assign('count',$count);

        //3.模板渲染
        return $this->view->fetch('banner_list');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return $this->view->fetch('banner_add');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //1.判断提交类型
        if($this->request->isPost()){
            //1。获取提交的数据，包括上传文件
            $data=$this->request->param(true);

            //2.获取上传的文件对象
            $file=$this->request->file('image');

            //3.判断是否获取到文件
            if(empty($file)){
                $this->error($file->getError());

            }
            //4.上传文件
            $map=[
                'ext'=>'jpg,png',
                'size'=>6000000, //6M,单位字节

            ];
            $info=$file->validate($map)->move(ROOT_PATH.'public/uploads/');
            if(is_null($info)){
                $this->error($file->getError());
            }

            //5.向表中新增数据
            $data['image']=$info->getSaveName();
            $res=BannerModel::create($data);

            //6.判断是否新增成功
            if(is_null($res)){
                $this->error('新增失败');
            }
            $this->success('新增成功');

            }else{
            $this->error('提交类型错误~~');
            }


    }


    public function edit($id)
    {
        //1.查询要编辑的记录
        $data=BannerModel::get($id);

        //2.将查询结果赋值给模板
        $this->view->assign('data',$data);

        //3.渲染模板
        return $this->view->fetch('banner_edit');
    }


    public function update()
    {
        //1.获取所有提交的数据，包括文件
        $data=$this->request->param(true);


        //2.对于文件单独操作，打包成一个文件对象
        //$file = $this->request->file('image');
        $file = request()->file('image');


        //3.文件验证与上传

        $info=$file->validate(['ext'=>'jpg,png,gif','size'=>10000000 ])->move(ROOT_PATH.'public/uploads/');
        if(is_null($info)){
            $this->error($file->getError());
        }

        //4.执行更新操作
        $res= BannerModel::update([
            'image'=>$info->getSaveName(),
            'link'=>$data['link'],
            'desc'=>$data['desc'],
        ],['id'=>$data['id']]);


        //5.检测更新
        if(is_null($res)){
            $this->error('更新失败~~');
        }

        //6.更新成功
        $this->error('更新成功~~');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
        BannerModel::destroy($id);



    }
}
