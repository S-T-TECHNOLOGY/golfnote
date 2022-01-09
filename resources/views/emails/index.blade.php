<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>GolfNote</title>
  <style>
    .header{
      margin: 0px 10px;
      padding-bottom: 30px;
      text-align: center;
      border-bottom: 2px solid #279280;
    }

    .logo{
      text-align: right;
      margin-right: 10px;
    }
    .title {
      font-size: 16px;
      margin-top: 0px;
      color: black;
      margin-block-start: 0px;
      margin-block-end: 0px;
    }

    .title-vn {
      font-weight: 500;
      margin-top: 10px;
    }
    .title-kor {
      font-weight: bold;
      margin-top: 6px;
      color: #279280;
    }
    .body {
      background-color: #EAF2F2;
      width: 100%;
    }
    .content-container {
      width: 600px;
      background-color: white;
      -webkit-transform: translate(-50%,-50%);
      padding: 40px;
      position: absolute;
      border-radius: 5px;
      top: 50%;
      left: 50%;
    }
    .content {
      padding: 20px 0px;
      margin: 0px 10px;
      border-bottom: 1px solid #DFE1E6;
    }
    .contact {
      font-size: 16px;
      line-height: 28px;
      margin-bottom: 15px;
    }
    p {
      margin-top: 0px;
      color: black;
      margin-block-start: 0px;
      margin-block-end: 0px;
      line-height: 28px;
    }
    a {
      color: blue;
    }
    .title-download {
      margin-left: 10px;
    }
    .footer {
      padding: 30px 0px 0px 0px;
    }
    .store-container {
      display: flex;
      margin-top: 10px;
    }

  </style>
</head>
<body>
<div class="body">
  <div class="content-container">
    <div class="header">
      <img src="http://server1.hanbisoft.com/images/logo.png" class="logo">
      <p class="title title-vn">Hiệp hội Golf Việt Nam Hàn Quốc</p>
      <p class="title title-kor">KOREAN GOLF ASSOCIATION IN VIETNAM</p>
    </div>
    <div class="content">
      @yield('content')
      <p class="contact">Mọi thông tin chi tiết, truy cập tại website <a>[Link website]</a> hoặc liên hệ Trung tâm hỗ trợ khách hàng 24/7 <strong>0987654321.</strong></p>
      <p>Trân trọng, </p>
      <p>Korean Golf Association in Vietnam</p>
    </div>
    <div class="footer">
      <p class="title-download">Tải app tại:</p>
      <div class="store-container">
        <a><img src="http://server1.hanbisoft.com/images/app_store.png"></a>
        <a><img src="http://server1.hanbisoft.com/images/play_store.png"></a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
