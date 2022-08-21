<!DOCTYPE html>
<html html dir="rtl" lang="ar">
<head>
    <title>Laravel 8 Generate PDF From View</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        p{
            font-size: 20px;

        }

        .image{
      margin-top: 35px;
      margin-right: 50%;
      width:160px;
      height: 160px;
      overflow:hidden;
      display:inline-block;
      background-color:white; /*not necessary, just to show the image box, can be added to img*/
    }
    </style>
</head>

<body>
    <div dir="rtl">
      <h1 style="text-decoration: underline;">القرار :</h1>
       <p> {{ $descision }} </p>
          <div class="image">
            <img src="{{ storage_path('app/public/img_signature/user'. $id .'.jpeg') }}">
          </div>
    </div>

</body>
</html>
