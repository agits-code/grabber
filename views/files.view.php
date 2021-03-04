<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>grabber</title>
</head>
<body>






<h5> updated <?= date('Y-m-d H:i:s T', $now); ?></h5>
<nav>
    <ul>
        <li><a href="/about">About</a></li>

        <li><a href="/download" target="_blank">Download</a></li>

        <li><a href="/decompress">Decompress</a></li>

        <li><a href="/read" target="_blank">Read</a></li>
        <li><a href="/">Home</a></li>


    </ul>
</nav>
<h1>My files</h1>
<h3> in progress</h3>
 <ul>
     <?php foreach ($files as $file) : ?>
     <ol>

         <li>
             <?= "<b>File name :</b>".$file->filename; ?>
         </li>
         <li>
             <?= "<b>File size :</b>".$file->filesize; ?>
         </li>
         <li>
             <?= ($file->pointer) ? "<b>Reading progress :</b>.$file->pointer" : "<b>Download progress :</b>.$file->filecursor";?>
         </li>

     </ol>
     ----------------------------------------------------------------
     <?php endforeach; ?>


 </ul>

</body>
</html>