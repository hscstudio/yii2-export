<?php

namespace app\controllers;

use Yii;
use app\models\Mahasiswa;
use app\models\MahasiswaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\DynamicModel;

/**
 * MahasiswaController implements the CRUD actions for Mahasiswa model.
 */
class MahasiswaController extends Controller
{
    /**
     * Lists all Mahasiswa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MahasiswaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $field = [
            'fileImport' => 'File Import',
        ];
        $modelImport = DynamicModel::validateData($field, [
            [['fileImport'], 'required'],
            [['fileImport'], 'file', 'extensions'=>'xls,xlsx','maxSize'=>1024*1024],
        ]);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelImport' => $modelImport,
        ]);
    }

    /**
     * Finds the Mahasiswa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mahasiswa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mahasiswa::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
	/*
	IMPORT WITH PHPEXCEL
	*/ 	
    public function actionImport()
    {
        $field = [
            'fileImport' => 'File Import',
        ];
        
        $modelImport = DynamicModel::validateData($field, [
            [['fileImport'], 'required'],
            [['fileImport'], 'file', 'extensions'=>'xls,xlsx','maxSize'=>1024*1024],
        ]);

        if (Yii::$app->request->post()) {
            $modelImport->fileImport = \yii\web\UploadedFile::getInstance($modelImport, 'fileImport');
            if ($modelImport->fileImport && $modelImport->validate()) {                                
                $inputFileType = \PHPExcel_IOFactory::identify($modelImport->fileImport->tempName );
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                $baseRow = 2;
                while(!empty($sheetData[$baseRow]['A'])){
                    $model = new Mahasiswa();
                    $model->nama = (string)$sheetData[$baseRow]['B'];
                    $model->nim = (string)$sheetData[$baseRow]['C'];
                    $model->save(); 
                    //die(print_r($model->errors));
                    $baseRow++;
                }
                Yii::$app->getSession()->setFlash('success', 'Success');
            }
            else{
                Yii::$app->getSession()->setFlash('error', 'Error');
            }
        }
        
        return $this->redirect(['index']);
    }
    
    /*
	EXPORT WITH PHPEXCEL
	*/ 
	public function actionExportExcel()
    {
        $searchModel = new MahasiswaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $template = Yii::getAlias('@hscstudio/export').'/templates/phpexcel/export.xlsx';
        $objPHPExcel = $objReader->load($template);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
        $baseRow=2; // line 2
        foreach($dataProvider->getModels() as $mahasiswa){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$baseRow, $baseRow-1);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$baseRow, $mahasiswa->nama);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$baseRow, $mahasiswa->nim);
            $baseRow++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }   
    
	/*
	EXPORT WITH OPENTBS
	*/
    public function actionExportExcel2()
    {
        $searchModel = new MahasiswaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Initalize the TBS instance
        $OpenTBS = new \hscstudio\export\OpenTBS; // new instance of TBS
        // Change with Your template kaka
		$template = Yii::getAlias('@hscstudio/export').'/templates/opentbs/ms-excel.xlsx';
        $OpenTBS->LoadTemplate($template); // Also merge some [onload] automatic fields (depends of the type of document).
        //$OpenTBS->VarRef['modelName']= "Mahasiswa";				
        $data = [];
        $no=1;
        foreach($dataProvider->getModels() as $mahasiswa){
            $data[] = [
                'no'=>$no++,
                'nama'=>$mahasiswa->nama,
                'nim'=>$mahasiswa->nim,
            ];
        }
        
        $data2[0] = [
                'no'=>'X',
                'nama'=>'Y',
                'nim'=>'Z',
            ];
        $data2[1] = [
                'no'=>'X',
                'nama'=>'Y',
                'nim'=>'Z',
            ];
        $OpenTBS->MergeBlock('data', $data);
        $OpenTBS->MergeBlock('data2', $data2);
        // Output the result as a file on the server. You can change output file
        $OpenTBS->Show(OPENTBS_DOWNLOAD, 'export.xlsx'); // Also merges all [onshow] automatic fields.			
        exit;
    } 
    
	/*
	EXPORT WITH OPENTBS
	*/
    public function actionExportWord()
    {
        $searchModel = new MahasiswaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Initalize the TBS instance
        $OpenTBS = new \hscstudio\export\OpenTBS; // new instance of TBS
        // Change with Your template kaka
		$template = Yii::getAlias('@hscstudio/export').'/templates/opentbs/ms-word.docx';
        $OpenTBS->LoadTemplate($template); // Also merge some [onload] automatic fields (depends of the type of document).
        //$OpenTBS->VarRef['modelName']= "Mahasiswa";				
        $data = [];
        $no=1;
        foreach($dataProvider->getModels() as $mahasiswa){
            $data[] = [
                'no'=>$no++,
                'nama'=>$mahasiswa->nama,
                'nim'=>$mahasiswa->nim,
            ];
        }
        $OpenTBS->MergeBlock('data', $data);
        // Output the result as a file on the server. You can change output file
        $OpenTBS->Show(OPENTBS_DOWNLOAD, 'export.docx'); // Also merges all [onshow] automatic fields.			
        exit;
    } 
    
	/*
	EXPORT WITH MPDF
	*/
    public function actionExportPdf()
    {
        $searchModel = new MahasiswaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $html = $this->renderPartial('_pdf',['dataProvider'=>$dataProvider]);
        $mpdf=new \mPDF('c','A4','','' , 0 , 0 , 0 , 0 , 0 , 0);  
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        exit;
    }
    
}
