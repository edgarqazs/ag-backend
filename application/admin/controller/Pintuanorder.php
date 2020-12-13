<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * 拼团数据
 *
 * @icon fa fa-circle-o
 */
class Pintuanorder extends Backend
{
    
    /**
     * Pintuanorder模型对象
     * @var \app\admin\model\Pintuanorder
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Pintuanorder;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $mywhere['status'] = '拼中';
            $mywhere['product_id'] = array('NEQ',0);

            $total = $this->model
                ->where($where)
                ->where($mywhere)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where($mywhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    public function export()
    {
        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter'); //var_dump($filter);exit; // string(28) "{"product_name":"富禾酒"}"
            $op = $this->request->post('op');
            $columns = $this->request->post('columns');

            $excel = new Spreadsheet();

            $excel->getProperties()
                  ->setCreator("FastAdmin")
                  ->setLastModifiedBy("FastAdmin")
                  ->setTitle("标题")
                  ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);



            $excel->getDefaultStyle()->applyFromArray(
                array(
                    'fill'      => array(
                        'type'  => Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font'      => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'indent'     => 1
                    ),
                    'borders'   => array(
                        'allborders' => array('style' => Border::BORDER_THIN),
                    )
                ));




            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('标题');



            $mywhere['status'] = '拼中';
            $mywhere['product_id'] = array('NEQ',0);

            // $offset = 0;
            // $limit = 1000;
            // $limit_list = $this->model
            //             ->where($mywhere)
            //             ->order('id', 'DESC')
            //             ->limit($offset, $limit)
            //             ->select();
            // $limit_list = collection($limit_list)->toArray();
            // $limit_ids = [];
            // foreach($limit_list as $l){
            //     $limit_ids[] = $l['id'];
            // }
            // if($ids == 'all'){
            //     $whereIds = ['id' => ['in', $limit_ids]];
            // }else{
            //     $whereIds = ['id' => ['in', explode(',', $ids)]];
            // }
            $whereIds = $ids == 'all' ? '1=1' : ['id' => ['in', explode(',', $ids)]];

            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();


            $line = 1;
            $list = [];

            $items = $this->model
                   ->field('id,product_name,address_name,address_detail,address_tel,ctime') //->field($columns)
                   ->where($where)->where('status', '拼中')->where('product_id', 'NEQ', 0)                   //->where($whereIds)
                   ->limit(1000)->select();
            // ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
            //     $styleArray = array(
            //         'font' => array(
            //             'color' => array('rgb' => '000000'),
            //             'size'  => 12,
            //             'name'  => 'Verdana'
            //         ));
            //     $list = $items = collection($items)->toArray();

            //     foreach ($items as $key => $v) {

            //         foreach ($v as $k => $ele) {
            //             $tmparray = explode("_text",$k);
            //             if(count($tmparray)>1){
            //                 $items[$key][$tmparray[0]] = $ele;
            //                 unset($items[$key][$k]);
            //             }
            //         }
            //     }

            //     foreach ($items as $index => $item) {
            //         $line++;
            //         $col = 0;
            //         foreach ($item as $field => $value) {

            //             $worksheet->setCellValueByColumnAndRow($col, $line, $value);
            //             $worksheet->getStyleByColumnAndRow($col, $line)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            //             $worksheet->getCellByColumnAndRow($col, $line)->getStyle()->applyFromArray($styleArray);
            //             $col++;
            //         }
            //     }
            // });

                $styleArray = array(
                    'font' => array(
                        'color' => array('rgb' => '000000'),
                        'size'  => 12,
                        'name'  => 'Verdana'
                    ));
                $list = $items = collection($items)->toArray();

                foreach ($items as $key => $v) {

                    foreach ($v as $k => $ele) {
                        $tmparray = explode("_text",$k);
                        if(count($tmparray)>1){
                            $items[$key][$tmparray[0]] = $ele;
                            unset($items[$key][$k]);
                        }
                    }
                }

                foreach ($items as $index => $item) {
                    $line++;
                    $col = 0;
                    foreach ($item as $field => $value) {

                        $worksheet->setCellValueByColumnAndRow($col, $line, $value);
                        $worksheet->getStyleByColumnAndRow($col, $line)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                        $worksheet->getCellByColumnAndRow($col, $line)->getStyle()->applyFromArray($styleArray);
                        $col++;
                    }
                }

                
                $first = array_keys($list[0]);
                foreach ($first as $k => $ele) {
                    $tmparray = explode("_text",$ele);
                    if(count($tmparray)>1){
                        unset($first[$k]);
                    }
                }


            foreach ($first as $index => $item) {
                $worksheet->setCellValueByColumnAndRow($index, 1, __($item));
            }

            $excel->createSheet();
            // Redirect output to a client’s web browser (Excel2007)
            $title = date("YmdHis");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0


            $objWriter = IOFactory::createWriter($excel, 'Xlsx');
            $objWriter->save('php://output');
            return;
        }
    }

}
