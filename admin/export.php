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

