<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>grabber</title>
</head>
<body>
<nav>
   <ul>
       <li><a href="/about">About</a></li>
       <li>
           <a href="/">Home</a>
       </li>

   </ul>
</nav>
<h1>My files</h1>
 <ul>
     <?php foreach ($files as $file) : ?>
     <ol>
       <li>
           <?= "<b>Link for download :</b>".$file->link; ?>
       </li>
         <li>
             <?= "<b>File name :</b>".$file->filename; ?>
         </li>
         <li>
             <?= "<b>File size :</b>".$file->filesize; ?>
         </li>
         <li>
             <?= "<b>Date created :</b>".$file->filedate; ?>
         </li>
         <li>
             <?= "<b>Last update :</b>".$file->last_update; ?>
         </li>
     </ol>
     ----------------------------------------------------------------
     <?php endforeach; ?>


 </ul>

</body>
</html>