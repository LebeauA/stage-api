<?php
/**
 * @file
 * Contains \Drupal\comment_form\Form\WorkForm.
 */
namespace Drupal\to_excel\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

class ToExcelForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getColorStatus($statusId){
    $backgroundColor = "";
    switch ($statusId){
      case '1':
        $backgroundColor = '00B0F0';
        break;
      case '2':
        $backgroundColor = 'C6E0B4';
        break;
      case '3':
        $backgroundColor = 'FFC000';
        break;
      case '4':
        $backgroundColor = 'FF0000';
        break;
    }
    return $backgroundColor;
  }
  public function getFormId() {
    return 'to_excel_block';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Export project to Excel file'),
      '#button_type' => 'primary',
    );
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $currentNode = \Drupal::routeMatch()->getParameter('node');
    $projectId = $currentNode->id();
    $documentsNids = \Drupal::entityQuery('node')->condition('type','document')->condition('field_project',$projectId)->execute();
    $documentsNodes =  Node::loadMultiple($documentsNids);
    $commentsNids = [];
    foreach ($documentsNids as $documentNid){
      $commentsDocumentNids = \Drupal::entityQuery('node')->condition('type','comment')->condition('field_document_bound',$documentNid)->execute();
      if($commentsDocumentNids != ''){
        foreach ($commentsDocumentNids as $commentDocumentNid){
          array_push($commentsNids, $commentDocumentNid);
        }
      }
    }
    $commentsNodes = Node::loadMultiple($commentsNids);

    // Using phpoffice/phpspreadsheet for read and write different spreadsheet file formats
    // Doc : https://phpspreadsheet.readthedocs.io/en/latest/

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Document title');
    $sheet->setCellValue('B1', 'Document status');
    $sheet->setCellValue('C1', 'Comment title');
    $sheet->setCellValue('D1', 'Comment description');
    $sheet->setCellValue('E1', 'Comment status');
    $sheet->setCellValue('F1', 'Comment user');
    $sheet->setCellValue('G1', 'Comment date');

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(16);
    $sheet->getColumnDimension('C')->setWidth(16);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(16);
    $sheet->getColumnDimension('F')->setWidth(10);
    $sheet->getColumnDimension('G')->setWidth(20);

    $index = 2;
    foreach ($documentsNodes as $documentNode){

      // Set value to variable for Column 'B'
      $statusTargetId = $documentNode->get('field_status')->target_id;
      if($statusTargetId !=""){
        $valueColumnB = (Term::load($statusTargetId)->getName());

        $sheet->getStyle('B'.$index)->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB($this->getColorStatus($statusTargetId));
      }
      else{
        $valueColumnB = '-- no status --';
      }
      $sheet->setCellValue('A'.$index, $documentNode->getTitle());
      $sheet->setCellValue('B'.$index, $valueColumnB);

      $sheet->getStyle('A'.$index)->getAlignment()->setWrapText(true);
      $sheet->getStyle('B'.$index)->getAlignment()->setWrapText(true);
      $sheet->getStyle('C'.$index)->getAlignment()->setWrapText(true);
      $sheet->getStyle('D'.$index)->getAlignment()->setWrapText(true);
      $sheet->getStyle('E'.$index)->getAlignment()->setWrapText(true);
      $sheet->getStyle('F'.$index)->getAlignment()->setWrapText(true);
      $sheet->getStyle('G'.$index)->getAlignment()->setWrapText(true);

      $commentCount = 0;
      foreach ($commentsNodes as $commentNode){
        if($documentNode->id() === ($commentNode->get(field_document_bound)->target_id)){
          if($commentCount < 1){
            $commentCount = $commentCount + 1;
            $sheet->setCellValue('C'.$index, $commentNode->getTitle());
            $sheet->setCellValue('D'.$index, $commentNode->body->value);
            $sheet->setCellValue('E'.$index, (Term::load($commentNode->get('field_status')->target_id)->getName()));
            $sheet->setCellValue('F'.$index, (User::load($commentNode->uid->target_id))->getAccountName());
            $sheet->setCellValue('G'.$index, \Drupal::service('date.formatter')->format(($commentNode->created->value),'custom', 'd/m/Y h:i'));

            $sheet->getStyle('E'.$index)->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setRGB($this->getColorStatus($commentNode->get('field_status')->target_id));
          }
          else{
            $sheet->insertNewRowBefore($index+$commentCount+1, 1);
            $sheet->setCellValue('C'.($index + $commentCount), $commentNode->getTitle());
            $sheet->setCellValue('D'.($index + $commentCount), $commentNode->body->value);
            $sheet->setCellValue('E'.($index + $commentCount), (Term::load($commentNode->get('field_status')->target_id)->getName()));
            $sheet->setCellValue('F'.($index + $commentCount), (User::load($commentNode->uid->target_id))->getAccountName());
            $sheet->setCellValue('G'.($index + $commentCount), \Drupal::service('date.formatter')->format(($commentNode->created->value),'custom', 'd/m/Y h:i'));

            $sheet->getStyle('E'.($index + $commentCount))->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setRGB($this->getColorStatus($commentNode->get('field_status')->target_id));
            $sheet->getStyle('C'.($index + $commentCount))->getAlignment()->setWrapText(true);
            $sheet->getStyle('D'.($index + $commentCount))->getAlignment()->setWrapText(true);
            $sheet->getStyle('E'.($index + $commentCount))->getAlignment()->setWrapText(true);
            $sheet->getStyle('F'.$index)->getAlignment()->setWrapText(true);
            $sheet->getStyle('G'.$index)->getAlignment()->setWrapText(true);

            $index += 1;
          }
        }
      }
      $index += 1;
    }
    $writer = new Xlsx($spreadsheet);
    $fileName = ($currentNode->getTitle())." (".date("Y-m-d-G-i").")";
    $excelFile = $fileName.'.xlsx';
    $writer->save($excelFile);

    $folder = "public://" . date("Y-m");
    mkdir($folder);
    $finalPath = $folder."/".$excelFile;
    \Drupal::service('file_system')->move($excelFile, $finalPath);

    $new_files = File::create([
      'uri' => $finalPath,
    ]);
    $new_files->save();

    $fileUri = $new_files->getFileUri();
    $path_parts = pathinfo($fileUri);
    $nodeName = $path_parts['filename'];

    $document = Node::create([
      'type' => 'report',
      'title' => $nodeName,
      'field_file' => [
        'target_id' => $new_files->id(),
      ],
      'field_project' => [
        'target_id' => $currentNode->id(),
      ],
    ]);
    $document->save();
  }
}
