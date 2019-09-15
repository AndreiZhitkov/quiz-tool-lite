<?php global $wpdb; ?>
<h1><?php _e('Export / Import', 'qtl') ?></h1>
<h2><?php _e('Export Questions', 'qtl') ?></h2>
<a href="admin.php?page=ai-quiz-export&download=csv" class="button-primary"><?php _e('Export Questions as CSV', 'qtl') ?></a><br/>
<br>
<hr/>

<h2><?php _e('Import Questions', 'qtl') ?></h2>

<?php
AI_Quiz_importExport::checkCSVUpload(); // Check to see if a CSV file has been uploaded
?>

<form name="csvUploadForm" method="post" action="admin.php?page=ai-quiz-export" enctype="multipart/form-data">
<?php _e('Upload your CSV import file.', 'qtl') ?><br/><br/>
<input type="file" name="csvFile" size="20"/>
<input type="hidden" name="mode" value="submit"><br/><br/>
<input type="submit" value="<?php _e('Import Questions', 'qtl') ?>" class="button-primary">
</form><br>
<hr/>



<h2><?php _e('Εξαγωγή αποτελεσμάτων εξετάσεων', 'qtl') ?></h2>



<div class="btn">
<form action="" method="post">
    <button type="submit" id="btnExport" name='exam' value="Export to Excel" class="button-primary">Export EXAMS</button>
    <button type="submit" href="<?php admin_url('page=ai-quiz-export');?>" id="btnExport" name='student' value="Export to Excel" class="button-primary">Export STUDENTS</button>
    <a href="admin.php?page=ai-quiz-export&download=student1" class="button-primary"><?php _e('Export Questions as CSV2222', 'qtl') ?></a><br/>

</form>
</div>


<?php 

if (isset($_POST["exam"])) {
    echo "exam";
    $student_results = $wpdb->get_results('SELECT * FROM wp_posts');
    // var_dump($student_results);
 }
 if (isset($_POST["student"])) {
    // echo "student";
    // $student_results = $wpdb->get_results('SELECT * FROM wp_ai_quiz_tbluserquizresponses');
    // var_dump($student_results);

    
}
?>

